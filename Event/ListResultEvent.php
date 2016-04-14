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
     * @param QueryBuilder|\ModelCriteria $queryBuilder
     * @param Request $request
     */
    function __construct($routeName, $queryBuilder, Request $request)
    {
        parent::__construct($routeName, $queryBuilder, $request);

        if ($queryBuilder instanceof QueryBuilder) {
            $this->results = $queryBuilder->getQuery()->getResult();
        } elseif ($queryBuilder instanceof \ModelCriteria) {
            $results = $queryBuilder->find();

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
