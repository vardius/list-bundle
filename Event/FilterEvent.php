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

/**
 * FilterEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FilterEvent
{
    /** @var mixed */
    protected $query;
    /** @var string|null */
    protected $alias;
    /** @var string */
    protected $field;
    /** @var string */
    protected $value;

    /**
     * FilterEvent constructor.
     * @param mixed $query
     * @param string $alias
     * @param string $field
     * @param string $value
     */
    public function __construct($query, string $alias = null, string $field, string $value)
    {
        $this->query = $query;
        $this->alias = $alias;
        $this->field = $field;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string|null
     */
    public function getAlias():string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getValue():string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getField():string
    {
        return $this->field;
    }
}
