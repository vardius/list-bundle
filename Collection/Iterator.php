<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Collection;

/**
 * Class AbstractCollection
 * @package Vardius\Bundle\ListBundle\Collection
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Iterator implements \Iterator
{
    /** @var array */
    protected $items = [];

    /**
     * Iterator constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        return next($this->items);
    }

    /**
     * @inheritDoc
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * @inheritDoc
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return key($this->items);
    }

    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        $key = key($this->items);
        return ($key !== NULL && $key !== FALSE);
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        reset($this->items);
    }
}
