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

use Elastica\Filter\BoolFilter;
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
     * @inheritDoc
     */
    public function getQuery($data, $query = null):array
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
     * @inheritDoc
     */
    public function applyQueries($query, $alias = null, string $column = null, string $sort = null, array $ids = [])
    {
        if (!$query instanceof Filtered) {
            throw new \InvalidArgumentException(
                'Expected argument of type "Elastica\Query\Filtered", ' . get_class($query) . ' given'
            );
        }

        if (!empty($ids)) {
            /** @var BoolFilter $filter */
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
