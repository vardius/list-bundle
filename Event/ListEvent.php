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
    protected $query;
    /** @var  Request */
    protected $request;

    /**
     * @param $routeName
     * @param mixed $query
     * @param Request $request
     */
    function __construct(string $routeName, $query, Request $request)
    {
        $this->routeName = $routeName;
        $this->query = $query;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getRouteName():string
    {
        return $this->routeName;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return Request
     */
    public function getRequest():Request
    {
        return $this->request;
    }

    /**
     * @param string $routeName
     * @return ListEvent
     */
    public function setRouteName(string $routeName):self
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * @param mixed $query
     * @return ListEvent
     */
    public function setQuery($query):self
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @param Request $request
     * @return ListEvent
     */
    public function setRequest(Request $request):self
    {
        $this->request = $request;
        return $this;
    }
}
