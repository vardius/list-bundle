<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Type;


/**
 * ColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class AbstractColumnType implements ColumnTypeInterface
{
    /** @var array */
    protected $options = [];
    /** @var  string */
    protected $name;

    /**
     * @param mixed $entity
     * @return mixed
     */
    abstract public function getData($entity = null);

    /** array */
    function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function applyOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}