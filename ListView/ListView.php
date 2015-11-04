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
use Vardius\Bundle\ListBundle\Event\ListFilterEvent;
use Vardius\Bundle\ListBundle\Filter\ListViewFilter;
use Vardius\Bundle\ListBundle\Filter\Provider\FilterProvider;
use Vardius\Bundle\ListBundle\View\RendererInterface;

/**
 * ListView
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListView
{
    /** @var FactoryEvent */
    protected $factoryEvent;
    /** @var int */
    protected $limit;
    /** @var string */
    protected $title;
    /** @var  ArrayCollection */
    protected $columns;
    /** @var  ArrayCollection */
    protected $actions;
    /** @var ArrayCollection */
    protected $filters;
    /** @var  string */
    protected $view;
    /** @var  QueryBuilder|null */
    protected $queryBuilder = null;
    /** @var array */
    protected $order = [];
    /** @var boolean */
    protected $paginator;
    /** @var  RendererInterface */
    protected $renderer;
    /** @var  EventDispatcherInterface */
    protected $dispatcher;

    /**
     * @param ContainerInterface $container
     * @param int $limit
     * @param string $title
     * @param boolean $paginator
     * @param EventDispatcherInterface $eventDispatcher
     */
    function __construct(
        ContainerInterface $container,
        $limit,
        $title,
        $paginator,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $formFactory = $container->get('form.factory');
        $columnFactory = $container->get('vardius_list.column.factory');
        $actionFactory = $container->get('vardius_list.action.factory');
        $filterFactory = $container->get('vardius_list.list_view_filter.factory');
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
        $data = $this->queryBuilder !== null ? $this->queryBuilder : $event->getData();
        if ($data instanceof EntityRepository) {
            $alias = $data->getClassName();
            $queryBuilder = $data->createQueryBuilder($alias);
        } elseif ($data instanceof QueryBuilder) {
            $queryBuilder = $data;
            $aliases = $queryBuilder->getRootAliases();
            $alias = array_values($aliases)[0];
        } else {
            throw new \InvalidArgumentException(
                'Expected argument of type "EntityRepository or QueryBuilder", '.get_class($data).' given'
            );
        }

        $currentPage = $event->getPage();
        $request = $event->getRequest();
        $routeName = $event->getRouteName();
        $column = $event->getColumn();
        $sort = $event->getSort();
        $filterForms = [];
        $paginator = null;

        $this->dispatcher->dispatch(ListEvents::PRE_QUERY_BUILDER, new ListEvent($routeName, $queryBuilder));

        if ($column !== null && $sort !== null) {
            $queryBuilder->addOrderBy($alias.'.'.$column, strtoupper($sort));
        }
        unset($sort);

        if (!empty($this->order)) {
            foreach ($this->order as $sort => $order) {
                if ($column !== $sort) {
                    $queryBuilder->addOrderBy($alias.'.'.$sort, strtoupper($order));
                }
            }
        }

        $ids = $request->get('ids', []);
        if (!empty($ids)) {
            $queryBuilder
                ->andWhere($alias.'.id IN (:ids)')
                ->setParameter('ids', $ids);
        } else {
            /** @var ListViewFilter $filter */
            foreach ($this->filters as $filter) {
                $formFactory = $this->factoryEvent->getFormFactory();
                $form = $formFactory->create($filter->getFormType(), []);

                $form->handleRequest($request);

                $listFilterEvent = new ListFilterEvent($routeName, $queryBuilder, $form, $alias);
                $this->dispatcher->dispatch(ListEvents::FILTER, $listFilterEvent);

                $formFilter = $filter->getFilter();
                if (is_callable($formFilter)) {
                    $queryBuilder = call_user_func_array($formFilter, [$listFilterEvent]);
                } else {
                    foreach ($formFilter as $field => $fieldFilter) {
                        $filterEvent = new FilterEvent($queryBuilder, $alias, $field, $form[$field]->getData());
                        if (is_callable($fieldFilter)) {
                            $queryBuilder = call_user_func_array($fieldFilter, [$filterEvent]);
                        } else {
                            $queryBuilder = $fieldFilter->apply($filterEvent);
                        }
                    }
                }

                $filterForms[] = $form->createView();
            }

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
        }

        $this->dispatcher->dispatch(ListEvents::POST_QUERY_BUILDER, new ListEvent($routeName, $queryBuilder));

        if ($returnQueryBuilder) {
            return $queryBuilder;
        } else {
            $data = ['results' => $queryBuilder->getQuery()->getResult()];
            if ($onlyResults) {

                return $data;
            } else {

                return array_merge(
                    $data,
                    [
                        'filterForms' => $filterForms,
                        'paginator' => $paginator,
                    ]
                );
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
        $params = array_merge(
            $data,
            [
                'columns' => $this->getColumns(),
                'actions' => $this->getActions(),
                'ui' => $ui,
            ]
        );

        return $this->renderer->renderView($this->getView(), $params);
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
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
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
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param string $column
     * @param string $order
     * @return $this
     */
    public function addOrder($column, $order = 'asc')
    {
        $this->order[$column] = $order;

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function removeOrder($column)
    {
        if (array_key_exists($column, $this->order)) {
            unset($this->order[$column]);
        }

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
     * @return $this
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
     * @return $this
     */
    public function removeAction(Action $column)
    {
        $this->actions->removeElement($column);

        return $this;
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
     * @param callable|string $filter
     * @return $this
     */
    public function addFilter($formType, $filter)
    {
        $filterFactory = $this->factoryEvent->getFilterFactory();
        $filter = $filterFactory->get($formType, $filter);
        $this->filters->add($filter);

        return $this;
    }

    /**
     * @param ListViewFilter $filter
     * @return $this
     */
    public function removeFilter(ListViewFilter $filter)
    {
        $this->filters->removeElement($filter);

        return $this;
    }

    /**
     * @return QueryBuilder|null
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return $this
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

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
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPagination()
    {
        return $this->paginator;
    }

    /**
     * @param $pagination
     * @return $this
     */
    public function setPagination($pagination)
    {
        $this->paginator = $pagination;

        return $this;
    }
}

