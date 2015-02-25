<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Paginator;


use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Twig\TwigEngine;

/**
 * Paginator
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Paginator
{
    /** @var  int */
    protected $page;
    /** @var  int */
    protected $total;
    /** @var  int */
    protected $limit;
    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $templatePath;

    /**
     * @param QueryBuilder $queryBuilder
     * @param $page
     * @param $limit
     */
    function __construct(QueryBuilder $queryBuilder, $page, $limit)
    {
        $this->page = $page;
        $this->limit = $limit;

        $aliases = $queryBuilder->getRootAliases();
        $alias = array_values($aliases)[0];

        $cloneQueryBuilder = clone $queryBuilder;
        $from = $cloneQueryBuilder->getDQLPart('from');
        $cloneQueryBuilder->resetDQLParts();

        $newQueryBuilder = $cloneQueryBuilder
            ->select('count(' . $alias . '.id)')
            ->add('from', $from[0]);

        $this->total = $newQueryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Renders view
     *
     * @return string
     */
    public function render()
    {
        return $this->templating->render($this->getTemplatePath() . 'paginator.html.twig', [
            'currentPage' => $this->getCurrentPage(),
            'previousPage' => $this->getPreviousPage(),
            'lastPage' => $this->getLastPage(),
            'nextPage' => $this->getNextPage(),
        ]);
    }

    /**
     * Returns last page number
     *
     * @return float
     */
    public function getLastPage()
    {
        return ceil($this->total / $this->limit);
    }

    /**
     * Returns current page number
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->page;
    }

    /**
     * Returns previous page number
     *
     * @return int
     */
    public function getPreviousPage()
    {
        return ($this->page > 1 ? ($this->page - 1) : 1);
    }

    /**
     * Returns next page number
     *
     * @return float
     */
    public function getNextPage()
    {
        return ($this->page < $this->getLastPage() ? $this->getLastPage() + 1 : $this->getLastPage());
    }

    /**
     * @return TwigEngine
     */
    public function getTemplating()
    {
        return $this->templating;
    }

    /**
     * @param TwigEngine $templating
     */
    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }

    /**
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

}
