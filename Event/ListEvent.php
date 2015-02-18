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


use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\Event;

/**
 * ListEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListEvent extends Event
{
    /** @var  string */
    protected $routeName;
    /** @var QueryBuilder */
    protected $queryBuilder;

    /**
     * @param $routeName
     * @param QueryBuilder $queryBuilder
     */
    function __construct($routeName, QueryBuilder $queryBuilder)
    {
        $this->routeName = $routeName;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return mixed
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }
}