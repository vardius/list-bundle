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

use Symfony\Component\HttpFoundation\Request;

/**
 * ListDataEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListDataEvent
{
    /** @var  Request */
    protected $request;
    /** @var mixed */
    protected $data;

    /**
     * @param mixed $data
     * @param Request $request
     */
    function __construct($data, Request $request)
    {
        $this->data = $data;
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest():Request
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getRouteName():string
    {

        return $this->request->get('_route');
    }

    /**
     * @return int
     */
    public function getPage():int
    {

        return $this->request->get('page', 1);
    }

    /**
     * @return string|null
     */
    public function getColumn():string
    {

        return $this->request->get('column', null);
    }

    /**
     * asc|desc
     * @return string|null
     */
    public function getSort():string
    {
        return $this->request->get('sort', null);
    }

    /**
     * @return int|null
     */
    public function getLimit():int
    {
        return $this->request->get('limit', null);
    }
}
