<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Paginator\Factory;

use Doctrine\ORM\QueryBuilder;
use Elastica\Query;
use Symfony\Bridge\Twig\TwigEngine;
use Vardius\Bundle\ListBundle\Paginator\Doctrine\Paginator as DoctrinePaginator;
use Vardius\Bundle\ListBundle\Paginator\ElasticSearch\Paginator as ElasticSearchPaginator;
use Vardius\Bundle\ListBundle\Paginator\PaginatorInterface;
use Vardius\Bundle\ListBundle\Paginator\Propel\Paginator as PropelPaginator;

/**
 * PaginatorFactory
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class PaginatorFactory
{
    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $templatePath;

    /**
     * @param TwigEngine $templating
     * @param string $templatePath
     */
    function __construct(TwigEngine $templating, $templatePath = 'VardiusListBundle:Paginator:')
    {
        $this->templating = $templating;
        $this->templatePath = $templatePath;
    }

    /**
     * @param mixed $query
     * @param $page
     * @param $limit
     * @return PaginatorInterface
     */
    public function get($query, $page, $limit)
    {
        if ($query instanceof QueryBuilder) {
            $paginator = new DoctrinePaginator($query, $page, $limit);
        } elseif ($query instanceof \ModelCriteria) {
            $paginator = new PropelPaginator($query, $page, $limit);
        } elseif ($query instanceof Query) {
            $paginator = new ElasticSearchPaginator($query, $page, $limit);
        } else {
            throw new \InvalidArgumentException(
                'Expected argument of type "\Doctrine\ORM\QueryBuilder", "\ModelCriteria" or "\Elastica\Query", ' . get_class($query) . ' given'
            );
        }

        $paginator->setTemplatePath($this->templatePath);
        $paginator->setTemplating($this->templating);

        return $paginator;
    }
}
