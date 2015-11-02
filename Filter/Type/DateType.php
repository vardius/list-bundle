<?php
/**
 * This file is part of the tipper package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Type;

use Doctrine\ORM\QueryBuilder;

class DateType extends FilterType
{
    /**
     * @inheritDoc
     */
    public function apply($value, $alias, QueryBuilder $queryBuilder)
    {
        $expression = $queryBuilder->expr();

        $queryBuilder
            ->andWhere($expression->gte($alias.'.date', ':dateFrom'))
            ->setParameter('dateFrom', $value);

        return $queryBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'date';
    }

}