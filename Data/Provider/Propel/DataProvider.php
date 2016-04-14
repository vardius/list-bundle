<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Data\Provider\Propel;

use Vardius\Bundle\ListBundle\Data\DataProviderInterface;

/**
 * Class DataProvider
 * @package Vardius\Bundle\ListBundle\Data\Provider\Propel
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DataProvider implements DataProviderInterface
{
    /**
     * @param \ModelCriteria $data
     * @param \ModelCriteria|null $query
     * @return array
     */
    public function getQuery($data, $query = null)
    {
        $data = $query !== null ? $query : $data;
        if ($data instanceof \ModelCriteria) {
            $alias = null;
            $query = $data;
        } else {
            throw new \InvalidArgumentException(
                'Expected argument of type "EntityRepository, QueryBuilder or ModelCriteria", ' . get_class($data) . ' given'
            );
        }

        return [
            'query' => $query,
            'alias' => $alias
        ];
    }

    /**
     * @param \ModelCriteria $query
     * @param string|null $alias
     * @param string|null $column
     * @param string|null $sort
     * @param array $ids
     * @return mixed
     */
    public function applyQueries($query, $alias, $column, $sort, $ids = [])
    {
        if (!$query instanceof \ModelCriteria) {
            throw new \InvalidArgumentException(
                'Expected argument of type "ModelCriteria", ' . get_class($query) . ' given'
            );
        }

        if ($column !== null && $sort !== null) {
            $query->orderBy($column, $sort);
        }
        unset($sort);

        if (!empty($this->order)) {
            foreach ($this->order as $sort => $order) {
                if ($column !== $sort) {
                    $query->orderBy($sort, $order);
                }
            }
        }

        if (!empty($ids)) {
            $query->add('id', $ids, \Criteria::IN);
        }

        return $query;
    }
}
