<?php
/**
 * This file is part of the healthbridge_api package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Collection;

use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Vardius\Bundle\ListBundle\Filter\Factory\ListViewFilterFactory;
use Vardius\Bundle\ListBundle\Filter\ListViewFilter;

/**
 * Class FilterCollection
 * @package Vardius\Bundle\ListBundle\Collection
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FilterCollection implements \IteratorAggregate
{
    /** @var array */
    protected $items = [];
    /** @var  ListViewFilterFactory */
    protected $factory;

    /**
     * ColumnCollection constructor.
     * @param ListViewFilterFactory $factory
     */
    public function __construct(ListViewFilterFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new Iterator($this->items);
    }

    /**
     * @param ListViewFilter $element
     * @return mixed
     */
    public function contains(ListViewFilter $element)
    {
        return in_array($element, $this->items, true);
    }

    /**
     * @param ResolvedFormTypeInterface|FormTypeInterface|string $formType
     * @param callable|string $filter
     * @return $this
     */
    public function add($formType, $filter)
    {
        $column = $this->factory->get($formType, $filter);
        $this->items[] = $column;

        return $this;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function remove($key)
    {
        if (!isset($this->items[$key]) && !array_key_exists($key, $this->items)) {
            return null;
        }

        $removed = $this->items[$key];
        unset($this->items[$key]);

        return $removed;
    }

    /**
     * @param ListViewFilter $element
     * @return bool
     */
    public function removeElement(ListViewFilter $element)
    {
        $key = array_search($element, $this->items, true);

        if ($key === false) {
            return false;
        }

        unset($this->items[$key]);

        return true;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        return isset($this->items[$key]) ? $this->items[$key] : null;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }
}
