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

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * ListFilterEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListFilterEvent extends ListEvent
{
    /** @var FormInterface */
    protected $form;
    /** @var string|null */
    protected $alias;

    /**
     * @param string $routeName
     * @param mixed $query
     * @param Request $request
     * @param FormInterface $form
     * @param string|null $alias
     */
    function __construct(string $routeName, $query, Request $request, FormInterface $form, string $alias = null)
    {
        parent::__construct($routeName, $query, $request);

        $this->form = $form;
        $this->alias = $alias;
    }

    /**
     * @return FormInterface
     */
    public function getForm():FormInterface
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getData():array
    {
        return $this->form->getData();
    }

    /**
     * @return string|null
     */
    public function getAlias():string
    {
        return $this->alias;
    }
}
