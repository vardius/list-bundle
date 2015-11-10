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
use Symfony\Component\HttpFoundation\Request;

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
    /** @var  Request */
    protected $request;

    /**
     * @param $routeName
     * @param QueryBuilder $queryBuilder
     * @param Request $request
     */
    function __construct($routeName, QueryBuilder $queryBuilder, Request $request)
    {
        $this->routeName = $routeName;
        $this->queryBuilder = $queryBuilder;
        $this->request = $request;
    }

    /**
     * @return string
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

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
