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

use Vardius\Bundle\ListBundle\Column\Column;
use Vardius\Bundle\ListBundle\Column\Factory\ColumnFactory;

/**
 * Class ColumnCollection
 * @package Vardius\Bundle\ListBundle\Collection
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ColumnCollection implements \IteratorAggregate
{
    /** @var array */
    protected $items = [];
    /** @var  ColumnFactory */
    protected $factory;

    /**
     * ColumnCollection constructor.
     * @param ColumnFactory $factory
     */
    public function __construct(ColumnFactory $factory)
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
     * @param Column $element
     * @return bool
     */
    public function contains(Column $element):bool
    {
        return in_array($element, $this->items, true);
    }

    /**
     * @param string $name
     * @param $type
     * @param array $options
     * @return ColumnCollection
     */
    public function add(string $name, $type, array $options = []):self
    {
        $element = $this->factory->get($name, $type, $options);
        $this->items[] = $element;

        return $this;
    }

    /**
     * @param int $key
     * @return Column
     */
    public function remove(int $key):Column
    {
        if (!isset($this->items[$key]) && !array_key_exists($key, $this->items)) {
            return null;
        }

        $removed = $this->items[$key];
        unset($this->items[$key]);

        return $removed;
    }

    /**
     * @param Column $element
     * @return bool
     */
    public function removeElement(Column $element):bool
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
     * @return Column
     */
    public function get(int $key)
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
