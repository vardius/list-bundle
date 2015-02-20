<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\ListView\Factory;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactory;
use Vardius\Bundle\ListBundle\Action\Factory\ActionFactory;
use Vardius\Bundle\ListBundle\Column\Factory\ColumnFactory;
use Vardius\Bundle\ListBundle\Event\FactoryEvent;
use Vardius\Bundle\ListBundle\Filter\Factory\ListViewFilterFactory;
use Vardius\Bundle\ListBundle\ListView\ListView;
use Vardius\Bundle\ListBundle\Paginator\Factory\PaginatorFactory;

/**
 * ListViewFactory
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListViewFactory
{
    /** @var int */
    protected $limit;
    /** @var string */
    protected $title;
    /** @var FormFactory */
    protected $formFactory;
    /** @var  EventDispatcherInterface */
    protected $dispatcher;
    /** @var  ColumnFactory */
    protected $columnFactory;
    /** @var  ActionFactory */
    protected $actionFactory;
    /** @var  ListViewFilterFactory */
    protected $filterFactory;
    /** @var  PaginatorFactory */
    protected $paginatorFactory;

    /**
     * @param int $limit
     * @param string $title
     * @param FormFactory $formFactory
     * @param ColumnFactory $columnFactory
     * @param ActionFactory $actionFactory
     * @param ListViewFilterFactory $filterFactory
     * @param PaginatorFactory $paginatorFactory
     * @param EventDispatcherInterface $eventDispatcher
     */
    function __construct($limit, $title, FormFactory $formFactory, ColumnFactory $columnFactory, ActionFactory $actionFactory, ListViewFilterFactory $filterFactory, PaginatorFactory $paginatorFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->formFactory = $formFactory;
        $this->columnFactory = $columnFactory;
        $this->actionFactory = $actionFactory;
        $this->filterFactory = $filterFactory;
        $this->paginatorFactory = $paginatorFactory;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @return ListView
     */
    public function get()
    {
        $event = new FactoryEvent($this->formFactory, $this->columnFactory, $this->actionFactory, $this->filterFactory, $this->paginatorFactory);
        $listView = new ListView($event, $this->limit, $this->title, $this->dispatcher);

        return $listView;
    }
}