<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Paginator\ElasticSearch;

use Elastica\Query;
use Vardius\Bundle\ListBundle\Paginator\Paginator as BasePaginator;

/**
 * Paginator
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Paginator extends BasePaginator
{
    /** @var Query */
    protected $query;

    /**
     * @param Query $query
     * @param int $page
     * @param int $limit
     */
    function __construct(Query $query, int $page, int $limit)
    {
        $this->page = $page;
        $this->limit = $limit;
        $this->query = $query;
        $this->total = 0;
    }

    /**
     * @inheritDoc
     */
    public function paginate()
    {
        $this->query->setSize($this->limit);
        $this->query->setFrom(($this->page - 1) * $this->limit);

        return $this->query;
    }
}
