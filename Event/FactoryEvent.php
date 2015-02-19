<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Event;


use Symfony\Component\Form\FormFactory;
use Vardius\Bundle\ListBundle\Action\Factory\ActionFactory;
use Vardius\Bundle\ListBundle\Filter\Factory\ListViewFilterFactory;
use Vardius\Bundle\ListBundle\Column\Factory\ColumnFactory;

/**
 * FactoryEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FactoryEvent
{
    /** @var FormFactory */
    protected $formFactory;
    /** @var  ColumnFactory */
    protected $columnFactory;
    /** @var  ActionFactory */
    protected $actionFactory;
    /** @var  ListViewFilterFactory */
    protected $filterFactory;

    /**
     * @param FormFactory $formFactory
     * @param ColumnFactory $columnFactory
     * @param ActionFactory $actionFactory
     * @param ListViewFilterFactory $filterFactory
     */
    function __construct(FormFactory $formFactory, ColumnFactory $columnFactory, ActionFactory $actionFactory, ListViewFilterFactory $filterFactory)
    {
        $this->formFactory = $formFactory;
        $this->columnFactory = $columnFactory;
        $this->actionFactory = $actionFactory;
        $this->filterFactory = $filterFactory;
    }

    /**
     * @return FormFactory
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @param FormFactory $formFactory
     */
    public function setFormFactory($formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @return ColumnFactory
     */
    public function getColumnFactory()
    {
        return $this->columnFactory;
    }

    /**
     * @param ColumnFactory $columnFactory
     */
    public function setColumnFactory($columnFactory)
    {
        $this->columnFactory = $columnFactory;
    }

    /**
     * @return ActionFactory
     */
    public function getActionFactory()
    {
        return $this->actionFactory;
    }

    /**
     * @param ActionFactory $actionFactory
     */
    public function setActionFactory($actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * @return ListViewFilterFactory
     */
    public function getFilterFactory()
    {
        return $this->filterFactory;
    }

    /**
     * @param ListViewFilterFactory $filterFactory
     */
    public function setFilterFactory($filterFactory)
    {
        $this->filterFactory = $filterFactory;
    }
}