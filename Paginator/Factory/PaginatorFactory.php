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
use Symfony\Bridge\Twig\TwigEngine;
use Vardius\Bundle\ListBundle\Paginator\PaginatorInterface;
use Vardius\Bundle\ListBundle\Paginator\Doctrine\Paginator as DoctrinePaginator;
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
     * @param QueryBuilder|\ModelCriteria $queryBuilder
     * @param $page
     * @param $limit
     * @return PaginatorInterface
     */
    public function get($queryBuilder, $page, $limit)
    {
        if ($queryBuilder instanceof QueryBuilder) {
            $paginator = new DoctrinePaginator($queryBuilder, $page, $limit);
        } elseif ($queryBuilder instanceof \ModelCriteria) {
            $paginator = new PropelPaginator($queryBuilder, $page, $limit);
        } else {
            throw new \InvalidArgumentException(
                'Expected argument of type "QueryBuilder or ModelCriteria", ' . get_class($queryBuilder) . ' given'
            );
        }

        $paginator->setTemplatePath($this->templatePath);
        $paginator->setTemplating($this->templating);

        return $paginator;
    }
}
