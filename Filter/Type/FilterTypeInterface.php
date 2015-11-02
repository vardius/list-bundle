<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Type;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FilterTypeInterface
{
    /**
     * Filter body, method is invoked when the filter is called from list view
     *
     * @param string $value
     * @param string $alias
     * @param QueryBuilder $queryBuilder
     * @return QueryBuilder
     */
    public function apply($value, $alias, QueryBuilder $queryBuilder);

    /**
     * Adjust the configuration of the options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Returns filter type name
     *
     * @return string
     */
    public function getName();

}
