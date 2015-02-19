<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Paginator;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Doctrine\ORM\QueryBuilder;


/**
 * Paginator
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Paginator
{
    protected $queryBuilder;
    function __construct(ListDataEvent $event, QueryBuilder $queryBuilder, $limit, $offset)
    {
        $page = $event->getPage();

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $column = $event->getColumn();
        if ($column !== null) {
            $queryBuilder->orderBy('d.' . $column, strtoupper($event->getSort()));
        }
    }

    public function getTotal()
    {
    }
}