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
use Symfony\Component\HttpFoundation\Request;

/**
 * ListResultEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListResultEvent extends ListEvent
{
    /** @var array */
    protected $results;

    /**
     * @param string $routeName
     * @param QueryBuilder|\ModelCriteria $query
     * @param Request $request
     */
    function __construct($routeName, $query, Request $request)
    {
        parent::__construct($routeName, $query, $request);

        if ($query instanceof QueryBuilder) {
            $this->results = $query->getQuery()->getResult();
        } elseif ($query instanceof \ModelCriteria) {
            $results = $query->find();

            if ($results instanceof \PropelObjectCollection) {
                $this->results = $results->toArray();
            }
        }
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param array $results
     * @return ListResultEvent
     */
    public function setResults(array $results)
    {
        $this->results = $results;
        return $this;
    }
}
