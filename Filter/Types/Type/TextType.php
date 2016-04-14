<?php
/**
 * This file is part of the tipper package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Types\Type;

use Doctrine\ORM\QueryBuilder;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Filter\Types\FilterType;

/**
 * TextType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class TextType extends FilterType
{
    /**
     * @inheritDoc
     */
    public function apply(FilterEvent $event, array $options)
    {
        $queryBuilder = $event->getQueryBuilder();
        $value = $event->getValue();

        if ($value) {
            $field = empty($options['field']) ? $event->getField() : $options['field'];

            $expression = $queryBuilder->expr();

            $queryBuilder
                ->andWhere($expression->like($event->getAlias() . '.' . $field, ':vardius_text_' . $field))
                ->setParameter('vardius_text_' . $field, $value);
        }

        return $queryBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'text';
    }

}