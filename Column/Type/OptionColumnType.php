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
 * OptionColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class OptionColumnType extends AbstractColumnType
{
    /**
     * @param mixed $entity
     * @return string
     */
    public function getData($entity = null)
    {
        if ($entity !== null) {
            return '<input type="checkbox" class="list-option-' . $this->getName() . '" name="' . $this->getName() . '" value="' . $entity->getId() . '" />';
        }

        return '<input type="checkbox" class="list-option-' . $this->getName() . '" name="' . $this->getName() . '" value="" />';
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'option';
    }
}