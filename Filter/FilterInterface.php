<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface
{
    /**
     * Filter body, method is invoked when the filter is called from lsit view
     *
     * @param string $value
     * @param string $alias
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function apply($value, $alias, QueryBuilder $queryBuilder);

    /**
     * Clear options array
     */
    public static function clearOptionsConfig();

    /**
     * Returns configuration array
     *
     * @return array
     */
    public function getOptions();

    /**
     * Set the configuration array
     *
     * @param array $options
     */
    public function setOptions(array $options = []);

}
