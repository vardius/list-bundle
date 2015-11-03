<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Event;


/**
 * ListEvents
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
final class ListEvents
{
    /**
     * The vardius_list.filter event is thrown each time an list is filtered
     *
     * The event listener receives an
     * Vardius\Bundle\ListBundle\Event\ListFilterEvent instance.
     *
     * @var string
     */
    const FILTER = 'vardius_list.filter';

    /**
     * The vardius_list.pre_query_builder event is thrown each time when query builder is created
     *
     * The event listener receives an
     * Vardius\Bundle\ListBundle\Event\ListEvent instance.
     *
     * @var string
     */
    const PRE_QUERY_BUILDER = 'vardius_list.pre_query_builder';

    /**
     * The vardius_list.post_query_builder event is thrown each before query results is returned
     *
     * The event listener receives an
     * Vardius\Bundle\ListBundle\Event\ListEvent instance.
     *
     * @var string
     */
    const POST_QUERY_BUILDER = 'vardius_list.post_query_builder';
}
