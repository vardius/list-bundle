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
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param ColumnTypeInterface $type
     */
    public function addType(ColumnTypeInterface $type)
    {
        $this->types->set($type->getName(), $type);
    }

    /**
     * @param string $id
     * @return AbstractColumnType
     */
    public function getType($id)
    {
        if ($this->types->containsKey($id)) {
            return $this->types[$id];
        }

        return null;
    }
}