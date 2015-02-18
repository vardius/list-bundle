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
     * The list.filter event is thrown each time an list is filtered
     *
     * The event listener receives an
     * Vardius\Bundle\ListBundle\Event\FilterEvent instance.
     *
     * @var string
     */
    const FILTER = 'list.filter';

    /**
     * The list.pre_query_builder event is thrown each time when query builder is created
     *
     * The event listener receives an
     * Vardius\Bundle\ListBundle\Event\ListEvent instance.
     *
     * @var string
     */
    const PRE_QUERY_BUILDER = 'list.pre_query_builder';

    /**
     * The list.post_query_builder event is thrown each before query results is returned
     *
     * The event listener receives an
     * Vardius\Bundle\ListBundle\Event\ListEvent instance.
     *
     * @var string
     */
    const POST_QUERY_BUILDER = 'list.post_query_builder';
}