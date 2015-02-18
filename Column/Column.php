<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column;

use Vardius\Bundle\ListBundle\Column\Type\AbstractColumnType;

/**
 * Column
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Column
{
    /** @var  string */
    protected $name;
    /** @var  AbstractColumnType */
    protected $type;
    /** @var  array */
    protected $options;

    /**
     * @param string $name
     * @param AbstractColumnType $type
     * @param array $options
     */
    function __construct($name, AbstractColumnType $type, array $options = [])
    {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;

        $this->type->applyOptions($this->options);
        $this->type->setName($this->name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->getType()->getData();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return AbstractColumnType
     */
    public function getType()
    {
        return $this->type;
    }

}