<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Data;

/**
 * Interface DataProviderInterface
 * @package Vardius\Bundle\ListBundle\Data
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface DataProviderInterface
{
    /**
     * @param $data
     * @param $query
     * @return array
     */
    function getQuery($data, $query = null): array;

    /**
     * @param $query
     * @param string|null $alias
     * @param string|null $column
     * @param string|null $sort
     * @param array $ids
     * @return mixed
     */
    function applyQueries($query, $alias = null, string $column = null, string $sort = null, array $ids = []);
}
