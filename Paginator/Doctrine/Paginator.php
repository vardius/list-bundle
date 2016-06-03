<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Paginator\Doctrine;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Vardius\Bundle\ListBundle\Paginator\Paginator as BasePaginator;

/**
 * Paginator
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Paginator extends BasePaginator
{
    /** @var QueryBuilder */
    protected $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     * @param int $page
     * @param int $limit
     */
    function __construct(QueryBuilder $queryBuilder, int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->queryBuilder = $queryBuilder;

        $aliases = $queryBuilder->getRootAliases();
        $alias = array_values($aliases)[0];

        $cloneQueryBuilder = clone $queryBuilder;
        $from = $cloneQueryBuilder->getDQLPart('from');

        $cloneQueryBuilder->resetDQLParts();

        //SQL Walkers error
        //http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/cookbook/dql-custom-walkers.html

        $newQueryBuilder = $cloneQueryBuilder
            ->select('count(' . $alias . '.id)')
            ->add('from', $from[0])
            ->setParameters([]);

        try {
            $this->total = $newQueryBuilder->getQuery()->getSingleScalarResult();
        } catch (NoResultException $e) {
            $this->total = 0;
        }
    }

    /**
     * @inheritDoc
     */
    public function paginate()
    {
        $offset = $this->limit * ($this->page - 1);
        $this->queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($this->limit);

        return $this->queryBuilder;
    }
}
