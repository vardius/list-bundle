<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Paginator\Propel;

use Doctrine\ORM\NoResultException;
use Vardius\Bundle\ListBundle\Paginator\Paginator as BasePaginator;

/**
 * Paginator
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Paginator extends BasePaginator
{
    /** @var \ModelCriteria */
    protected $query;

    /**
     * @param \ModelCriteria $query
     * @param int $page
     * @param int $limit
     */
    function __construct(\ModelCriteria $query, int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->query = $query;

        $cloneQuery = clone $query;
        $cloneQuery->clear();

        try {
            $this->total = $cloneQuery->count();
        } catch (NoResultException $e) {
            $this->total = 0;
        }
    }

    /**
     * @inheritDoc
     */
    public function paginate()
    {
        $this->query->paginate($this->page, $this->limit);

        return $this->query;
    }
}
