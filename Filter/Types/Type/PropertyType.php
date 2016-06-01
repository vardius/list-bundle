<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Types\Type;

use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Filter\Types\FilterType;

/**
 * PropertyType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class PropertyType extends FilterType
{
    /**
     * @inheritDoc
     */
    public function apply(FilterEvent $event, array $options)
    {
        $queryBuilder = $event->getQuery();
        $value = $event->getValue();

        if ($value) {
            $field = empty($options['field']) ? $event->getField() : $options['field'];

            $queryBuilder
                ->andWhere($event->getAlias() . '.' . $field . ' = :vardius_property_' . $event->getField())
                ->setParameter('vardius_property_' . $event->getField(), $value);

        }

        return $queryBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'property';
    }

}