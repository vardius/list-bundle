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

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

/**
 * ListFilterEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListFilterEvent extends ListEvent
{
    /** @var FormInterface */
    protected $form;
    /** @var string  */
    protected $alias;

    /**
     * @param string $routeName
     * @param QueryBuilder $queryBuilder
     * @param FormInterface $form
     * @param string $alias
     */
    function __construct($routeName, QueryBuilder $queryBuilder, FormInterface $form, $alias)
    {
        parent::__construct($routeName, $queryBuilder);

        $this->form = $form;
        $this->alias = $alias;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->form->getData();
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
