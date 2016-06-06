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

use Doctrine\ORM\QueryBuilder;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Filter\Types\AbstractType;

/**
 * NullType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class NullType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function apply(FilterEvent $event, array $options)
    {
        $queryBuilder = $event->getQuery();
        if (!$queryBuilder instanceof QueryBuilder) {
            throw new \Exception('Vardius\Bundle\ListBundle\Filter\Types\NullType supports only doctrine filters for now. To filter Propel or ElasticSearch Queries use callbacks or create your own FilterType classes');
        }

        $value = $event->getValue();
        if ($value) {
            $field = empty($options['field']) ? $event->getField() : $options['field'];

            $expression = $queryBuilder->expr();

            $queryBuilder->andWhere($expression->isNull($event->getAlias() . '.' . $field));
        }

        return $queryBuilder;
    }
}
