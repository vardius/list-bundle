<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Data\Provider\Doctrine;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Vardius\Bundle\ListBundle\Data\DataProviderInterface;

/**
 * Class DataProvider
 * @package Vardius\Bundle\ListBundle\Data\Provider\Doctrine
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DataProvider implements DataProviderInterface
{
    /**
     * @param QueryBuilder|EntityRepository $data
     * @param QueryBuilder|null $query
     * @return array
     */
    public function getQuery($data, $query = null)
    {
        $data = $query !== null ? $query : $data;
        if ($data instanceof EntityRepository) {
            $alias = $data->getClassName();
            $query = $data->createQueryBuilder($alias);
        } elseif ($data instanceof QueryBuilder) {
            $query = $data;
            $aliases = $query->getRootAliases();
            $alias = array_values($aliases)[0];
        } else {
            throw new \InvalidArgumentException(
                'Expected argument of type "EntityRepository, QueryBuilder", ' . get_class($data) . ' given'
            );
        }

        return [
            'query' => $query,
            'alias' => $alias
        ];
    }

    /**
     * @param QueryBuilder $query
     * @param string|null $alias
     * @param string|null $column
     * @param string|null $sort
     * @param array $ids
     * @return mixed
     */
    public function applyQueries($query, $alias, $column, $sort, $ids = [])
    {
        if (!$query instanceof QueryBuilder) {
            throw new \InvalidArgumentException(
                'Expected argument of type "QueryBuilder", ' . get_class($query) . ' given'
            );
        }

        if ($column !== null && $sort !== null) {
            $query->addOrderBy($alias . '.' . $column, strtoupper($sort));
        }
        unset($sort);

        if (!empty($this->order)) {
            foreach ($this->order as $sort => $order) {
                if ($column !== $sort) {
                    $query->addOrderBy($alias . '.' . $sort, strtoupper($order));
                }
            }
        }

        if (!empty($ids)) {
            $query
                ->andWhere($alias . '.id IN (:ids)')
                ->setParameter('ids', $ids);
        }

        return $query;
    }
}
