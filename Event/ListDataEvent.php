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


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
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
    /** @var EntityRepository|QueryBuilder|\ModelCriteria */
    protected $data;

    /**
     * @param EntityRepository|QueryBuilder|\ModelCriteria $data
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
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return EntityRepository|QueryBuilder|\ModelCriteria
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {

        return $this->request->get('_route');
    }

    /**
     * @return int
     */
    public function getPage()
    {

        return $this->request->get('page', 1);
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {

        return $this->request->get('column', null);
    }

    /**
     * asc|desc
     * @return string
     */
    public function getSort()
    {
        return $this->request->get('sort', null);
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->request->get('limit', null);
    }
}
