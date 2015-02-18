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
 * FilterEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FilterEvent extends ListEvent
{
    /** @var FormInterface */
    protected $form;

    /**
     * @param $routeName
     * @param QueryBuilder $queryBuilder
     * @param FormInterface $form
     */
    function __construct($routeName, QueryBuilder $queryBuilder, FormInterface $form)
    {
        parent::__construct($routeName, $queryBuilder);

        $this->form = $form;
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
}