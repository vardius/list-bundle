<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter;


use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * ListViewFilter
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListViewFilter
{
    /** @var ResolvedFormTypeInterface|FormTypeInterface|string */
    protected $formType;
    /** @var  callable */
    protected $filters;

    /**
     * @param ResolvedFormTypeInterface|FormTypeInterface|string $formType
     * @param callable $filters
     */
    function __construct($formType, $filters)
    {
        if (!is_callable($filters)) {
            throw new \InvalidArgumentException('Expected argument of type "callable", ' . get_class($filters) . ' given');
        }

        $this->formType = $formType;
        $this->filters = $filters;
    }

    /**
     * @return ResolvedFormTypeInterface|FormTypeInterface|string
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @param ResolvedFormTypeInterface|FormTypeInterface|string $formType
     */
    public function setFormType($formType)
    {
        $this->formType = $formType;
    }

    /**
     * @return callable
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param callable $filters
     */
    public function setFilters($filters)
    {
        if (!is_callable($filters)) {
            throw new \InvalidArgumentException('Expected argument of type "callable", ' . get_class($filters) . ' given');
        }

        $this->filters = $filters;
    }
}