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
    public function getIterator():Iterator
    {
        return new Iterator($this->items);
    }

    /**
     * @param ListViewFilter $element
     * @return bool
     */
    public function contains(ListViewFilter $element):bool
    {
        return in_array($element, $this->items, true);
    }

    /**
     * @param mixed $formType
     * @param callable|string $filter
     * @return FilterCollection
     */
    public function add($formType, $filter):self
    {
        $column = $this->factory->get($formType, $filter);
        $this->items[] = $column;

        return $this;
    }

    /**
     * @param int $key
     * @return mixed|null
     */
    public function remove(int $key):ListViewFilter
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
    public function removeElement(ListViewFilter $element):bool
    {
        $key = array_search($element, $this->items, true);

        if ($key === false) {
            return false;
        }

        unset($this->items[$key]);

        return true;
    }

    /**
     * @param int $key
     * @return ListViewFilter|null
     */
    public function get(int $key):ListViewFilter
    {
        return isset($this->items[$key]) ? $this->items[$key] : null;
    }

    /**
     * @return array
     */
    public function toArray():array
    {
        return $this->items;
    }
}
