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
use Vardius\Bundle\ListBundle\Column\ColumnInterface;
use Vardius\Bundle\ListBundle\Event\FactoryEvent;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\Event\ListEvent;
use Vardius\Bundle\ListBundle\Event\ListEvents;
use Vardius\Bundle\ListBundle\Event\ListFilterEvent;
use Vardius\Bundle\ListBundle\Event\ListResultEvent;
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
    /** @var  ArrayCollection|ColumnInterface[] */
    protected $columns;
    /** @var  ArrayCollection */
    protected $actions;
    /** @var ArrayCollection */
    protected $filters;
    /** @var  string */
    protected $view;
    /** @var  QueryBuilder|\ModelCriteria|null */
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
     * @param $queryBuilder
     * @return array
     */
    protected function getQuery($queryBuilder)
    {
        $data = $this->queryBuilder !== null ? $this->queryBuilder : $queryBuilder;
        if ($data instanceof EntityRepository) {
            $alias = $data->getClassName();
            $query = $data->createQueryBuilder($alias);
        } elseif ($data instanceof QueryBuilder) {
            $query = $data;
            $aliases = $query->getRootAliases();
            $alias = array_values($aliases)[0];
        } elseif ($data instanceof \ModelCriteria) {
            $alias = null;
            $query = $data;
        } else {
            throw new \InvalidArgumentException(
                'Expected argument of type "EntityRepository, QueryBuilder or ModelCriteria", ' . get_class($data) . ' given'
            );
        }

        return [
            'query' => $query,
            'alias' => $alias
        ];
    }

    /**
     * @param QueryBuilder|\ModelCriteria $query
     * @param string|null $alias
     * @param string|null $column
     * @param string|null $sort
     * @param array $ids
     * @return mixed
     */
    protected function applyQueries($query, $alias, $column, $sort, $ids = [])
    {
        if ($query instanceof QueryBuilder) {
            if ($column !== null && $sort !== null) {
                $query->addOrderBy($alias . '.' . $column, strtoupper($sort));
            }
            unset($sort);

            if (!empty($this->order)) {
                foreach ($this->order as $sort => $order) {
                    if ($column !== $sort) {
                        $query->addOrderBy($alias . '.' . $sort, strtoupper($order));
                    }
                }
            }

            if (!empty($ids)) {
                $query
                    ->andWhere($alias . '.id IN (:ids)')
                    ->setParameter('ids', $ids);
            }
        } elseif ($query instanceof \ModelCriteria) {
            if ($column !== null && $sort !== null) {
                $query->orderBy($column, $sort);
            }
            unset($sort);

            if (!empty($this->order)) {
                foreach ($this->order as $sort => $order) {
                    if ($column !== $sort) {
                        $query->orderBy($sort, $order);
                    }
                }
            }

            if (!empty($ids)) {
                $query->add('id', $ids, \Criteria::IN);
            }
        }

        return $query;
    }

    /**
     * @param ListDataEvent $event
     * @param boolean $onlyResults
     * @param boolean $returnQueryBuilder
     * @return QueryBuilder|\ModelCriteria|array
     */
    public function getData(ListDataEvent $event, $onlyResults = false, $returnQueryBuilder = false)
    {
        /** @var QueryBuilder|\ModelCriteria $query */
        $query = null;
        /** @var string|null $alias */
        $alias = null;
        extract($this->getQuery($event->getData()), EXTR_OVERWRITE);

        $request = $event->getRequest();
        $routeName = $event->getRouteName();
        $sort = $event->getSort();
        $limit = $event->getLimit();
        $limit = $limit ?: $this->getLimit();
        $column = $event->getColumn();
        $ids = $request->get('ids', []);
        $filterForms = [];
        $paginator = null;

        $this->dispatcher->dispatch(ListEvents::PRE_QUERY_BUILDER, new ListEvent($routeName, $query, $request));

        $query = $this->applyQueries($query, $alias, $column, $sort, $ids);

        if (empty($ids)) {
            /** @var ListViewFilter $filter */
            foreach ($this->filters as $filter) {
                $formFactory = $this->factoryEvent->getFormFactory();
                $form = $formFactory->create($filter->getFormType());

                $form->handleRequest($request);

                $listFilterEvent = new ListFilterEvent($routeName, $query, $request, $form, $alias);
                $this->dispatcher->dispatch(ListEvents::FILTER, $listFilterEvent);

                $formFilter = $filter->getFilter();
                if (is_callable($formFilter)) {
                    $query = call_user_func_array($formFilter, [$listFilterEvent]);
                } else {
                    foreach ($formFilter as $field => $fieldFilter) {
                        $filterEvent = new FilterEvent($query, $alias, $field, $form[$field]->getData());
                        if (is_callable($fieldFilter)) {
                            $query = call_user_func_array($fieldFilter, [$filterEvent]);
                        } else {
                            $query = $fieldFilter->apply($filterEvent);
                        }
                    }
                }

                $filterForms[] = $form->createView();
            }

            if ($this->paginator) {
                $this->dispatcher->dispatch(ListEvents::PRE_PAGINATOR, new ListEvent($routeName, $query, $request));

                $paginatorFactory = $this->factoryEvent->getPaginatorFactory();
                $paginator = $paginatorFactory->get($query, $event->getPage(), $limit);
                $query = $paginator->paginate();
            }
        }

        $this->dispatcher->dispatch(ListEvents::POST_QUERY_BUILDER, new ListEvent($routeName, $query, $request));

        if ($returnQueryBuilder) {
            return $query;
        } else {
            $resultsEvent = new ListResultEvent($routeName, $query, $request);
            $results = $this->dispatcher->dispatch(ListEvents::RESULTS, $resultsEvent)->getResults();

            if ($onlyResults) {

                return $results;
            } else {

                return [
                    'results' => $results,
                    'filterForms' => $filterForms,
                    'paginator' => $paginator,
                ];
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
     * @return ArrayCollection|ColumnInterface[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param string $name
     * @param string $type
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
     * @return QueryBuilder|\ModelCriteria|null
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @param QueryBuilder|\ModelCriteria $queryBuilder
     * @return $this
     */
    public function setQueryBuilder($queryBuilder)
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

