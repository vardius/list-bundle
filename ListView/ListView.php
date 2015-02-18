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
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Vardius\Bundle\ListBundle\Action\Action;
use Vardius\Bundle\ListBundle\Action\Factory\ActionFactory;
use Vardius\Bundle\ListBundle\Column\Column;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\Event\ListEvent;
use Vardius\Bundle\ListBundle\Event\ListEvents;
use Vardius\Bundle\ListBundle\Filter\Factory\ListViewFilterFactory;
use Vardius\Bundle\ListBundle\Filter\ListViewFilter;
use Vardius\Bundle\ListBundle\Column\Factory\ColumnFactory;

/**
 * ListView
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListView
{
    /** @var  EventDispatcherInterface */
    protected $dispatcher;
    /** @var FormFactory */
    protected $formFactory;
    /** @var  ColumnFactory */
    protected $columnFactory;
    /** @var  ActionFactory */
    protected $actionFactory;
    /** @var  ListViewFilterFactory */
    protected $filterFactory;
    /** @var ArrayCollection */
    protected $filters;
    /** @var array */
    protected $filterForms = [];
    /** @var int */
    protected $limit = 10;
    /** @var  ArrayCollection */
    protected $columns;
    /** @var  ArrayCollection */
    protected $actions;

    /**
     * @param FormFactory $formFactory
     * @param ColumnFactory $columnFactory
     * @param ActionFactory $actionFactory
     * @param ListViewFilterFactory $filterFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    function __construct(FormFactory $formFactory, ColumnFactory $columnFactory, ActionFactory $actionFactory, ListViewFilterFactory $filterFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->formFactory = $formFactory;
        $this->columnFactory = $columnFactory;
        $this->actionFactory = $actionFactory;
        $this->filterFactory = $filterFactory;
        $this->dispatcher = $eventDispatcher;
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

        $offset = ($event->getPage() * $this->limit) - $this->limit + 1;

        $data = $event->getData();
        if ($data instanceof EntityRepository) {
            $queryBuilder = $data->createQueryBuilder('d');
        } elseif ($data instanceof QueryBuilder) {
            $queryBuilder = $data;
        } else {
            throw new \InvalidArgumentException('Expected argument of type "EntityRepository or QueryBuilder", ' . get_class($data) . ' given');
        }

        $routeName = $event->getRouteName();
        $this->dispatchEvent(ListEvents::PRE_QUERY_BUILDER, new ListEvent($routeName, $queryBuilder));

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($this->limit);

        $column = $event->getColumn();
        if ($event->getColumn() !== null) {
            $queryBuilder->orderBy('d.' . $column, strtoupper($event->getSort()));
        }

        /** @var ListViewFilter $filter */
        foreach ($this->filters as $filter) {
            $form = $this->formFactory->create($filter->getFormType(), []);
            $form->handleRequest($event->getRequest());
            $this->filterForms[] = $form;

            $filterEvent = new FilterEvent($routeName, $queryBuilder, $form);
            $this->dispatchEvent(ListEvents::FILTER, $filterEvent);
            $queryBuilder = call_user_func_array($filter->getFilters(), [$filterEvent]);
        }

        $this->dispatchEvent(ListEvents::POST_QUERY_BUILDER, new ListEvent($routeName, $queryBuilder));

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param FilterEvent $filterEvent
     */
    public function dispatchEvent(FilterEvent $filterEvent)
    {
        $this->dispatcher->dispatch(ListEvents::FILTER, $filterEvent);
    }

    /**
     * @return array
     */
    public function getFilterForm()
    {
        return $this->filterForms;
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
        $filter = $this->filterFactory->get($formType, $filters);
        $this->filters->add($filter);

        return $this;
    }

    /**
     * @param ListViewFilter $filter
     * @return ListView
     */
    public function removeFilter(ListViewFilter $filter)
    {
        if ($this->filters->contains($filter)) {
            $this->filters->removeElement($filter);
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
        $column = $this->columnFactory->get($name, $type, $options);
        $this->columns->add($column);

        return $this;
    }

    /**
     * @param Column $column
     * @return ListView
     */
    public function removeColumn(Column $column)
    {
        if ($this->columns->contains($column)) {
            $this->columns->removeElement($column);
        }

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
     * @param string $type
     * @param string $icon
     * @return $this
     */
    public function addAction($path, $name = null, $type = 'row', $icon = null)
    {
        $action = $this->actionFactory->get($path, $name, $type, $icon);
        $this->actions->add($action);

        return $this;
    }

    /**
     * @param Action $column
     * @return ListView
     */
    public function removeAction(Action $column)
    {
        if ($this->actions->contains($column)) {
            $this->actions->removeElement($column);
        }

        return $this;
    }

}