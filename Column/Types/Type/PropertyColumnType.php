<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Types\Type;

use Vardius\Bundle\ListBundle\Column\Types\ColumnType;

/**
 * PropertyColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class PropertyColumnType extends ColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null, array $options = [])
    {
        $property = null;
        if ($entity !== null) {
            $method = ucfirst($options['property']);
            if (method_exists($entity, 'get' . $method)) {
                $property = $entity->{'get' . $method}();
            } elseif (method_exists($entity, 'is' . $method)) {
                $property = $entity->{'is' . $method}();
            }
        }

        $action = $options['row_action'];
        if (is_array($action) && !empty($action) && $entity !== null && method_exists($entity, 'getId')) {
            $action['parameters']['id'] = $entity->getId();
        }

        return [
            'property' => $property,
            'action' => $action
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'property';
    }
}
