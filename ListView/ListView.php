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
use Elastica\Query;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Vardius\Bundle\ListBundle\Action\Action;
use Vardius\Bundle\ListBundle\Action\ActionInterface;
use Vardius\Bundle\ListBundle\Collection\ActionCollection;
use Vardius\Bundle\ListBundle\Collection\ColumnCollection;
use Vardius\Bundle\ListBundle\Collection\FilterCollection;
use Vardius\Bundle\ListBundle\Column\Column;
use Vardius\Bundle\ListBundle\Column\ColumnInterface;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\Event\ListEvent;
use Vardius\Bundle\ListBundle\Event\ListEvents;
use Vardius\Bundle\ListBundle\Event\ListFilterEvent;
use Vardius\Bundle\ListBundle\Event\ListResultEvent;
use Vardius\Bundle\ListBundle\Filter\FilterInterface;
use Vardius\Bundle\ListBundle\Filter\ListViewFilter;

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
    /** @var bool */
    protected $paginator;
    /** @var  ContainerInterface */
    protected $container;
    /** @var  ColumnCollection|ColumnInterface[] */
    protected $columns;
    /** @var  ActionCollection|ActionInterface */
    protected $actions;
    /** @var  FilterCollection|FilterInterface[] */
    protected $filters;
    /** @var  mixed */
    protected $query = null;

    /**
     * @param int $limit
     * @param string $dbDriver
     * @param bool $paginator
     * @param ContainerInterface $container
     */
    function __construct(int $limit, string $dbDriver, bool $paginator, ContainerInterface $container)
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
     * @param bool $onlyResults
     * @param bool $returnQueryBuilder
     * @return mixed
     */
    public function getData(ListDataEvent $event, bool $onlyResults = false, bool $returnQueryBuilder = false)
    {
        /** @var string|null $alias */
        $alias = null;
        /** @var mixed $query */
        $query = null;

        $dataProvider = $this->container->get('vardius_list.data_provider.factory')->get($this->dbDriver);
        extract($dataProvider->getQuery($event->getData(), $this->getQuery()), EXTR_OVERWRITE);

        $request = $event->getRequest();
        $routeName = $event->getRouteName();
        $sort = $event->getSort();
        $limit = $event->getLimit();
        $limit = $limit ?: $this->getLimit();
        $column = $event->getColumn();
        $ids = $request->get('ids', []);
        $filterForms = [];
        $paginator = null;
        $order = $this->getOrder();

        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(ListEvents::PRE_QUERY_BUILDER, new ListEvent($routeName, $query, $request));

        if (empty($ids)) {
            /** @var ListViewFilter $filter */
            foreach ($this->filters as $filter) {
                $formFactory = $this->container->get('form.factory');
                $form = $formFactory->create($filter->getFormType());

                $form->handleRequest($request);

                $listFilterEvent = new ListFilterEvent($routeName, $query, $request, $form, $alias);
                $dispatcher->dispatch(ListEvents::FILTER, $listFilterEvent);

                $formFilter = $filter->getFilter();
                if (is_callable($formFilter) || is_array($formFilter)) {
                    $query = call_user_func_array($formFilter, [$listFilterEvent]);
                } else {
                    foreach ($formFilter as $field => $fieldFilter) {
                        if ($form->has($field)) {
                            $filterEvent = new FilterEvent($query, $alias, $field, $form[$field]->getData());
                            if (is_callable($fieldFilter) || is_array($fieldFilter)) {
                                $query = call_user_func_array($fieldFilter, [$filterEvent]);
                            } else {
                                $query = $fieldFilter->apply($filterEvent);
                            }
                        }
                    }
                }

                $filterForms[] = $form->createView();
            }
        }
        $query = $dataProvider->applyQueries($query, $alias, $column, $sort, $ids, $order);

        if ($this->paginator && empty($ids)) {
            $dispatcher->dispatch(ListEvents::PRE_PAGINATOR, new ListEvent($routeName, $query, $request));

            $paginatorFactory = $this->container->get('vardius_list.paginator.factory');
            $paginator = $paginatorFactory->get($query, $event->getPage(), $limit);
            $query = $paginator->paginate();
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
     * @param bool $ui
     * @return string
     */
    public function render(ListDataEvent $event, bool $ui = true):string
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
    public function getTitle():string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return ListView
     */
    public function setTitle(string $title):self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit():int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return ListView
     */
    public function setLimit(int $limit):self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return string
     */
    public function getDbDriver():string
    {
        return $this->dbDriver;
    }

    /**
     * @param string $dbDriver
     * @return ListView
     */
    public function setDbDriver(string $dbDriver):self
    {
        $this->dbDriver = $dbDriver;
        return $this;
    }

    /**
     * @return array
     */
    public function getOrder():array
    {
        return $this->order;
    }

    /**
     * @param string $column
     * @param string $order
     * @return ListView
     */
    public function addOrder(string $column, string $order = 'asc'):self
    {
        $this->order[$column] = $order;

        return $this;
    }

    /**
     * @param string $column
     * @return ListView
     */
    public function removeOrder(string $column):self
    {
        if (array_key_exists($column, $this->order)) {
            unset($this->order[$column]);
        }

        return $this;
    }

    /**
     * @return ColumnCollection|ColumnInterface[]
     */
    public function getColumns():ColumnCollection
    {
        return $this->columns;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array $options
     * @return ListView
     */
    public function addColumn(string $name, string $type, array $options = []):self
    {
        $this->columns->add($name, $type, $options);

        return $this;
    }

    /**
     * @param Column $column
     * @return ListView
     */
    public function removeColumn(Column $column):self
    {
        $this->columns->removeElement($column);

        return $this;
    }

    /**
     * @return ActionCollection
     */
    public function getActions():ActionCollection
    {
        return $this->actions;
    }

    /**
     * @param string $name
     * @param string $path
     * @param string $icon
     * @param array $parameters
     * @return ListView
     */
    public function addAction(string $path, string $name = null, string $icon = null, array $parameters = []):self
    {
        $this->actions->add($path, $name, $icon, $parameters);

        return $this;
    }

    /**
     * @param Action $column
     * @return ListView
     */
    public function removeAction(Action $column):self
    {
        $this->actions->removeElement($column);

        return $this;
    }

    /**
     * @return FilterCollection
     */
    public function getFilters():FilterCollection
    {
        return $this->filters;
    }

    /**
     * @param mixed $formType
     * @param callable|string $filter
     * @return ListView
     */
    public function addFilter($formType, $filter):self
    {
        $this->filters->add($formType, $filter);

        return $this;
    }

    /**
     * @param ListViewFilter $filter
     * @return ListView
     */
    public function removeFilter(ListViewFilter $filter):self
    {
        $this->filters->removeElement($filter);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     * @return ListView
     */
    public function setQuery($query):self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getView():string
    {
        return $this->view;
    }

    /**
     * @param string $view
     * @return ListView
     */
    public function setView(string $view):self
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPagination():bool
    {
        return $this->paginator;
    }

    /**
     * @param bool $pagination
     * @return ListView
     */
    public function setPagination(bool $pagination):self
    {
        $this->paginator = $pagination;

        return $this;
    }
}
