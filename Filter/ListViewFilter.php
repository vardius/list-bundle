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

/**
 * ListViewFilter
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListViewFilter
{
    /** @var mixed */
    protected $formType;
    /** @var  callable|ArrayCollection */
    protected $filter;

    /**
     * @param mixed $formType
     * @param callable|ArrayCollection $filter
     */
    function __construct($formType, $filter)
    {
        if (!is_callable($filter) && !$filter instanceof ArrayCollection) {
            throw new \InvalidArgumentException(
                'Expected argument of type "callable" or Collection of Vardius\Bundle\ListBundle\Filter\Filter, ' . get_class(
                    $filter
                ) . ' given'
            );
        }

        $this->formType = $formType;
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @param mixed $formType
     * @return ListViewFilter
     */
    public function setFormType($formType):self
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
     * @return ListViewFilter
     */
    public function setFilter($filter):self
    {
        $this->filter = $filter;
    }
}
