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

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Vardius\Bundle\ListBundle\Filter\Provider\FilterProvider;

/**
 * ListViewFilter
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListViewFilter
{
    /** @var ResolvedFormTypeInterface|FormTypeInterface|string */
    protected $formType;
    /** @var  callable|ArrayCollection */
    protected $filter;

    /**
     * @param ResolvedFormTypeInterface|FormTypeInterface|string $formType
     * @param callable|ArrayCollection $filter
     */
    function __construct($formType, $filter)
    {
        if (!is_callable($filter) && !$filter instanceof ArrayCollection) {
            throw new \InvalidArgumentException(
                'Expected argument of type "callable" or Collection of Vardius\Bundle\ListBundle\Filter\Filter, '.get_class(
                    $filter
                ).' given'
            );
        }

        $this->formType = $formType;
        $this->filter = $filter;
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
     * @return callable|ArrayCollection
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param callable|ArrayCollection $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }
}
