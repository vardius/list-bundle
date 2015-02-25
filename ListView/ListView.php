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
    /** @var  ArrayCollection */
    protected $columns;
    /** @var  ArrayCollection */
    protected $actions;

    /**
     * @param FactoryEvent $event
     * @param int $limit
     * @param string $title
     * @param EventDispatcherInterface $eventDispatcher
     */
    function __construct(FactoryEvent $event, $limit, $title, EventDispatcherInterface $eventDispatcher)
    {
        $this->factoryEvent = $event;
        $this->dispatcher = $eventDispatcher;
        $this->limit = $limit;
        $this->title = $title;
        $this->columns = new ArrayCollection();
        $this->rowActions = new ArrayCollection();
        $this->filters = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    /**
     * @param ListDataEvent $event
     * @return array
     */
    public function getData(ListDataEvent $event)
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
        $paginatorFactory = $this->factoryEvent->getPaginatorFactory();
        $paginator = $paginatorFactory->get($queryBuilder, $currentPage, $this->getLimit());

        $routeName = $event->getRouteName();
        $this->dispatcher->dispatch(ListEvents::PRE_QUERY_BUILDER, new ListEvent($routeName, $queryBuilder));

        $offset = ($currentPage * $this->limit) - $this->limit + 1;
        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($this->limit);

        $column = $event->getColumn();
        if ($column !== null) {
            $queryBuilder->orderBy($data->getClassName() . '.' . $column, strtoupper($event->getSort()));
        }

        $filterForms = [];
        /** @var ListViewFilter $filter */
        foreach ($this->filters as $filter) {

            $formFactory = $this->factoryEvent->getFormFactory();
            $form = $formFactory->create($filter->getFormType(), []);

            $form->handleRequest($event->getRequest());

            $filterEvent = new FilterEvent($routeName, $queryBuilder, $form);
            $this->dispatcher->dispatch(ListEvents::FILTER, $filterEvent);
            $queryBuilder = call_user_func_array($filter->getFilters(), [$filterEvent]);

            $filterForms[] = $form->createView();
        }

        $this->dispatcher->dispatch(ListEvents::POST_QUERY_BUILDER, new ListEvent($routeName, $queryBuilder));

        return [
            'results' => $queryBuilder->getQuery()->getResult(),
            'filterForms' => $filterForms,
            'paginator' => $paginator->render(),
        ];
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

}
