<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Types;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * ColumnTypePool
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ColumnTypePool
{
    /** @var ArrayCollection */
    protected $types;

    /**
     * {@inheritdoc}
     */
    function __construct()
    {
        $this->types = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getTypes():ArrayCollection
    {
        return $this->types;
    }

    /**
     * @param ColumnTypeInterface $type
     */
    public function addType(ColumnTypeInterface $type)
    {
        $this->types->set(get_class($type), $type);
    }

    /**
     * @param string $class
     * @return ColumnTypeInterface
     */
    public function getType(string $class)
    {
        if ($this->types->containsKey($class)) {
            return $this->types[$class];
        }

        return null;
    }
}
