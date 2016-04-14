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

use Vardius\Bundle\ListBundle\Action\Action;
use Vardius\Bundle\ListBundle\Action\ActionInterface;
use Vardius\Bundle\ListBundle\Collection\ActionCollection;
use Vardius\Bundle\ListBundle\Collection\ColumnCollection;
use Vardius\Bundle\ListBundle\Collection\FilterCollection;
use Vardius\Bundle\ListBundle\Column\Column;
use Vardius\Bundle\ListBundle\Column\ColumnInterface;
use Vardius\Bundle\ListBundle\Data\Factory\DataProviderFactory;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\Event\ListEvent;
use Vardius\Bundle\ListBundle\Event\ListEvents;
use Vardius\Bundle\ListBundle\Event\ListFilterEvent;
use Vardius\Bundle\ListBundle\Event\ListResultEvent;
use Vardius\Bundle\ListBundle\Filter\FilterInterface;
use Vardius\Bundle\ListBundle\Filter\ListViewFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * ListView
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListView
{
    /** @var string */
    protected $dbDriver;
    /** @var int */
    protected $limit;
    /** @var string */
    protected $title = '';
    /** @var  string */
    protected $view;
    /** @var array */
    protected $order = [];
    /** @var boolean */
    protected $paginator;
    /** @var  ContainerInterface */
    protected $container;
    /** @var  ColumnCollection|ColumnInterface[] */
    protected $columns;
    /** @var  ActionCollection|ActionInterface */
    protected $actions;
    /** @var  FilterCollection|FilterInterface[] */
    protected $filters;
    /** @var  EntityRepository|QueryBuilder|\ModelCriteria|null */
    protected $query = null;

    /**
     * @param ContainerInterface $container
     * @param int $limit
     * @param string $dbDriver
     * @param boolean $paginator
     */
    function __construct($limit, $dbDriver, $paginator, ContainerInterface $container)
    {
        $this->limit = $limit;
        $this->dbDriver = $dbDriver;
        $this->paginator = $paginator;
        $this->container = $container;
        $this->columns = new ColumnCollection($container->get('vardius_list.column.factory'));
        $this->filters = new FilterCollection($container->get('vardius_list.list_view_filter.factory'));
        $this->actions = new ActionCollection($container->get('vardius_list.action.factory'));
    }

    /**
     * @param ListDataEvent $event
     * @param boolean $onlyResults
     * @param boolean $returnQueryBuilder
     * @return QueryBuilder|\ModelCriteria|array
     */
    public function getData(ListDataEvent $event, $onlyResults = false, $returnQueryBuilder = false)
    {
        /** @var string|null $alias */
        $alias = null;
        /** @var QueryBuilder|\ModelCriteria $query */
        $query = null;

        $dataProvider = $this->container->get('vardius_list.data_provider.factory')->get($this->dbDriver);
        extract($dataProvider->getQuery($event->getData()), EXTR_OVERWRITE);

        $request = $event->getRequest();
        $routeName = $event->getRouteName();
        $sort = $event->getSort();
        $limit = $event->getLimit();
        $limit = $limit ?: $this->getLimit();
        $column = $event->getColumn();
        $ids = $request->get('ids', []);
        $filterForms = [];
        $paginator = null;

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(ListEvents::PRE_QUERY_BUILDER, new ListEvent($routeName, $query, $request));

        $query = $dataProvider->applyQueries($query, $alias, $column, $sort, $ids);

        if (empty($ids)) {
            /** @var ListViewFilter $filter */
            foreach ($this->filters as $filter) {
                $formFactory = $this->container->get('form.factory');
                $form = $formFactory->create($filter->getFormType());

                $form->handleRequest($request);

                $listFilterEvent = new ListFilterEvent($routeName, $query, $request, $form, $alias);
                $dispatcher->dispatch(ListEvents::FILTER, $listFilterEvent);

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
                $dispatcher->dispatch(ListEvents::PRE_PAGINATOR, new ListEvent($routeName, $query, $request));

                $paginatorFactory = $this->container->get('vardius_list.paginator.factory');
                $paginator = $paginatorFactory->get($query, $event->getPage(), $limit);
                $query = $paginator->paginate();
            }
        }

        $dispatcher->dispatch(ListEvents::POST_QUERY_BUILDER, new ListEvent($routeName, $query, $request));

        if ($returnQueryBuilder) {
            return $query;
        } else {
            $resultsEvent = new ListResultEvent($routeName, $query, $request);
            $results = $dispatcher->dispatch(ListEvents::RESULTS, $resultsEvent)->getResults();

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

        return $this->container->get('vardius_list.view.renderer')->renderView($this->getView(), $params);
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
     * @return string
     */
    public function getDbDriver()
    {
        return $this->dbDriver;
    }

    /**
     * @param string $dbDriver
     * @return ListView
     */
    public function setDbDriver($dbDriver)
    {
        $this->dbDriver = $dbDriver;
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
     * @return ColumnCollection|ColumnInterface[]
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
        $this->columns->add($name, $type, $options);

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
        $this->actions->add($path, $name, $icon, $parameters);

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
        $this->filters->add($formType, $filter);

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
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param QueryBuilder|\ModelCriteria $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

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

