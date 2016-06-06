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

use Vardius\Bundle\ListBundle\Action\Action;
use Vardius\Bundle\ListBundle\Action\Factory\ActionFactory;

/**
 * Class ActionCollection
 * @package Vardius\Bundle\ListBundle\Collection
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ActionCollection implements \IteratorAggregate
{
    /** @var array */
    protected $items = [];
    /** @var  ActionFactory */
    protected $factory;

    /**
     * ColumnCollection constructor.
     * @param ActionFactory $factory
     */
    public function __construct(ActionFactory $factory)
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
     * @param Action $element
     * @return bool
     */
    public function contains(Action $element):bool
    {
        return in_array($element, $this->items, true);
    }

    /**
     * @param string $path
     * @param string|null $name
     * @param string|null $icon
     * @param array $parameters
     * @return ActionCollection
     */
    public function add(string $path, string $name = null, string $icon = null, array $parameters = []):self
    {
        $element = $this->factory->get($path, $name, $icon, $parameters);
        $this->items[] = $element;

        return $this;
    }

    /**
     * @param int $key
     * @return Action
     */
    public function remove(int $key)
    {
        if (!isset($this->items[$key]) && !array_key_exists($key, $this->items)) {
            return null;
        }

        $removed = $this->items[$key];
        unset($this->items[$key]);

        return $removed;
    }

    /**
     * @param Action $element
     * @return bool
     */
    public function removeElement(Action $element):bool
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
     * @return Action
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
