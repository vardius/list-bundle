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
 * PropertyColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class PropertyColumnType extends AbstractColumnType
{
    /**
     * @param mixed $entity
     * @return string
     */
    public function getData($entity = null)
    {
        if ($entity !== null) {
            return $entity->get{ucfirst($this->getName())}();
        }

        return $this->getName();
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'property';
    }
}