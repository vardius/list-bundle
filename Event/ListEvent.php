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

use Elastica\Query;
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
    /** @var mixed */
    protected $queryBuilder;
    /** @var  Request */
    protected $request;

    /**
     * @param $routeName
     * @param mixed $query
     * @param Request $request
     */
    function __construct($routeName, $query, Request $request)
    {
        $this->routeName = $routeName;
        $this->queryBuilder = $query;
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
     * @return mixed
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

    /**
     * @param string $routeName
     * @return ListEvent
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * @param mixed $query
     * @return ListEvent
     */
    public function setQueryBuilder($query)
    {
        $this->queryBuilder = $query;
        return $this;
    }

    /**
     * @param Request $request
     * @return ListEvent
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }
}
