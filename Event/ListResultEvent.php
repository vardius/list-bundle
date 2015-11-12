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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;

/**
 * ListResultEvent
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListResultEvent extends ListEvent
{
    /** @var ArrayCollection */
    protected $results;

    /**
     * @param string $routeName
     * @param QueryBuilder $queryBuilder
     * @param Request $request
     * @param array $results
     */
    function __construct($routeName, QueryBuilder $queryBuilder, Request $request, array $results)
    {
        parent::__construct($routeName, $queryBuilder, $request);

        $this->results = new ArrayCollection($results);
    }

    /**
     * @return ArrayCollection
     */
    public function getResults()
    {
        return $this->results;
    }
}
