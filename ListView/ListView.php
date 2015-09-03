<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\ListView;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Vardius\Bundle\ListBundle\Action\Action;
use Vardius\Bundle\ListBundle\Column\Column;
use Vardius\Bundle\ListBundle\Event\FactoryEvent;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\Event\ListEvent;
use Vardius\Bundle\ListBundle\Event\ListEvents;
use Vardius\Bundle\ListBundle\Filter\ListViewFilter;
use Vardius\Bundle\ListBundle\View\RendererInterface;

/**
 * ListView
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListView
{
    /** @var  EventDispatcherInterface */
    protected $dispatcher;
    /** @var FactoryEvent */
    protected $factoryEvent;
    /** @var ArrayCollection */
    protected $filters;
    /** @var int */
    protected $limit;
    /** @var string */
    protected $title;
    /** @var boolean */
    protected $paginator;
    /** @var  ArrayCollection */
    protected $columns;
    /** @var  ArrayCollection */
    protected $actions;
    /** @var  string */
    protected $view;
    /** @var  RendererInterface */
    protected $renderer;

    /**
     * @param ContainerInterface $container
     * @param int $limit
     * @param string $title
     * @param EventDispatcherInterface $eventDispatcher
     */
    function __construct(ContainerInterface $container, $limit, $title, $paginator, EventDispatcherInterface $eventDispatcher)
    {
        $formFactory = $container->get('form.factory');
        $columnFactory = $container->get('vardius_list.column.factory');
        $actionFactory = $container->get('vardius_list.action.factory');
        $filterFactory = $container->get('vardius_list.filter.factory');
        $paginatorFactory = $container->get('vardius_list.paginator.factory');

        $event = new FactoryEvent($formFactory, $columnFactory, $actionFactory, $filterFactory, $paginatorFactory);

        $this->limit = $limit;
        $this->title = $title;
        $this->paginator = $paginator;
        $this->factoryEvent = $event;
        $this->dispatcher = $eventDispatcher;
        $this->renderer = $container->get('vardius_list.view.renderer');
        $this->columns = new ArrayCollection();
        $this->filters = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    /**
     * @param ListDataEvent $event
     * @param boolean $onlyResults
     * @param boolean $returnQueryBuilder
     * @return array|QueryBuilder
     */
    public function getData(ListDataEvent $event, $onlyResults = false, $returnQueryBuilder = false)
    {
        $data = $event->getData();
        if ($data instanceof EntityRepository) {
            $queryBuilder = $data->createQueryBuilder($data->getClassName());
        } elseif ($data instanceof QueryBuilder) {
            $queryBuilder = $data;
        } else {
            throw new \InvalidArgumentException('Expected argument of type "EntityRepository or QueryBuilder", ' . get_class($data) . ' given');
        }

        $currentPage = $event->getPage();
        $request = $event->getRequest();
        $routeName = $event->getRouteName();
        $column = $event->getColumn();
        $filterForms = [];
        $paginator = null;

        $this->dispatcher->dispatch(ListEvents::PRE_QUERY_BUILDER, new ListEvent($routeName, $queryBuilder));

        if ($column !== null) {
            $queryBuilder->orderBy($data->getClassName() . '.' . $column, strtoupper($event->getSort()));
        }

        if ($onlyResults) {
            $ids = $request->get('ids', []);
            if (!empty($ids)) {
                $queryBuilder
                    ->andWhere($data->getClassName() . '.id IN (:ids)')
                    ->setParameter('ids', $ids);
            }
        } else {
            if ($this->paginator) {
                $paginatorFactory = $this->factoryEvent->getPaginatorFactory();
                $paginator = $paginatorFactory->get($queryBuilder, $currentPage, $this->getLimit());

                $offset = ($currentPage * $this->limit) - $this->limit;
                $queryBuilder
                    ->setFirstResult($offset)
                    ->setMaxResults($this->limit);
            } else {
                $paginator = null;
            }

            /** @var ListViewFilter $filter */
            foreach ($this->filters as $filter) {
                $formFactory = $this->factoryEvent->getFormFactory();
                $form = $formFactory->create($filter->getFormType(), []);

                $form->handleRequest($request);

                $filterEvent = new FilterEvent($routeName, $queryBuilder, $form);
                $this->dispatcher->dispatch(ListEvents::FILTER, $filterEvent);
                $queryBuilder = call_user_func_array($filter->getFilters(), [$filterEvent]);

                $filterForms[] = $form->createView();
            }
        }

        $this->dispatcher->dispatch(ListEvents::POST_QUERY_BUILDER, new ListEvent($routeName, $queryBuilder));

        if ($returnQueryBuilder) {

            return $queryBuilder;
        } else {
            $data = ['results' => $queryBuilder->getQuery()->getResult()];
            if ($onlyResults) {

                return $data;
            } else {

                return array_merge($data, [
                    'filterForms' => $filterForms,
                    'paginator' => $paginator,
                ]);
            }
        }

    }

    /**
     * Render list data
     * The ui parameter tells us if user interface is enable (buttons, links etc.)
     *
     * @param ListDataEvent $event
     * @param $ui
     * @return string
     */
    public function render(ListDataEvent $event, $ui = true)
    {
        $data = $this->getData($event, !$ui);
        $params = array_merge($data, [
            'columns' => $this->getColumns(),
            'actions' => $this->getActions(),
            'ui' => $ui
        ]);

        return $this->renderer->renderView($this->getView(), $params);
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return ArrayCollection
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param ResolvedFormTypeInterface|FormTypeInterface|string $formType
     * @param callable $filters
     * @return ListView
     */
    public function addFilter($formType, $filters)
    {
        $filterFactory = $this->factoryEvent->getFilterFactory();
        $filter = $filterFactory->get($formType, $filters);
        $this->filters->add($filter);

        return $this;
    }

    /**
     * @param ListViewFilter $filter
     * @return ListView
     */
    public function removeFilter(ListViewFilter $filter)
    {
        $this->filters->removeElement($filter);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $name
     * @param $type
     * @param array $options
     * @return $this
     */
    public function addColumn($name, $type, array $options = [])
    {
        $columnFactory = $this->factoryEvent->getColumnFactory();
        $column = $columnFactory->get($name, $type, $options);
        $this->columns->add($column);

        return $this;
    }

    /**
     * @param Column $column
     * @return ListView
     */
    public function removeColumn(Column $column)
    {
        $this->columns->removeElement($column);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $icon
     * @param array $parameters
     * @return $this
     */
    public function addAction($path, $name = null, $icon = null, $parameters = [])
    {
        $actionFactory = $this->factoryEvent->getActionFactory();
        $action = $actionFactory->get($path, $name, $icon, $parameters);
        $this->actions->add($action);

        return $this;
    }

    /**
     * @param Action $column
     * @return ListView
     */
    public function removeAction(Action $column)
    {
        $this->actions->removeElement($column);

        return $this;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}
