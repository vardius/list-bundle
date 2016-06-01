<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Data\Provider\ElasticSearch;

use Elastica\Filter\Terms;
use Elastica\Query;
use Elastica\Query\Filtered;
use Vardius\Bundle\ListBundle\Data\DataProviderInterface;

/**
 * Class DataProvider
 * @package Vardius\Bundle\ListBundle\Data\Provider\ElasticSearch
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DataProvider implements DataProviderInterface
{
    /**
     * @param Filtered $data
     * @param Filtered|null $query
     * @return array
     */
    public function getQuery($data, $query = null)
    {
        $data = $query !== null ? $query : $data;
        if ($data instanceof Filtered) {
            $alias = null;
            $query = $data;
        } else {
            throw new \InvalidArgumentException(
                'Expected argument of type "Elastica\Query\Filtered", ' . get_class($data) . ' given'
            );
        }

        return [
            'query' => $query,
            'alias' => $alias
        ];
    }

    /**
     * @param Filtered $query
     * @param string|null $alias
     * @param string|null $column
     * @param string|null $sort
     * @param array $ids
     * @return mixed
     */
    public function applyQueries($query, $alias, $column, $sort, $ids = [])
    {
        if (!$query instanceof Filtered) {
            throw new \InvalidArgumentException(
                'Expected argument of type "Elastica\Query\Filtered", ' . get_class($query) . ' given'
            );
        }

        if (!empty($ids)) {
            /** @var \Elastica\Filter\Bool $filter */
            $filter = $query->getFilter();
            $idsFilter = new Terms();
            $idsFilter->setTerms('id', $ids);
            $filter->addMust($idsFilter);
            $query->setFilter($filter);
        }

        $newQuery = new Query();
        $newQuery->setQuery($query);
        $query = $newQuery;
        unset($newQuery);

        if ($column !== null && $sort !== null) {
            $query->addSort([$column => ['order' => strtolower($sort)]]);
        }
        unset($sort);

        if (!empty($this->order)) {
            foreach ($this->order as $sort => $order) {
                if ($column !== $sort) {
                    $query->addSort([$sort => ['order' => strtolower($order)]]);
                }
            }
        }

        return $query;
    }
}
