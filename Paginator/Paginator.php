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

use Symfony\Bridge\Twig\TwigEngine;

/**
 * Paginator
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class Paginator implements PaginatorInterface
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
     * {@inheritdoc}
     */
    public function render():string
    {
        return $this->templating->render($this->getTemplatePath() . 'paginator.html.twig', [
            'currentPage' => $this->getCurrentPage(),
            'previousPage' => $this->getPreviousPage(),
            'lastPage' => $this->getLastPage(),
            'nextPage' => $this->getNextPage(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPage():float
    {
        return ceil($this->total / $this->limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage():int
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousPage():int
    {
        return ($this->page > 1 ? ($this->page - 1) : 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getNextPage():float
    {
        return ($this->page < $this->getLastPage() ? $this->page + 1 : $this->page);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplating():TwigEngine
    {
        return $this->templating;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplating(TwigEngine $templating):PaginatorInterface
    {
        $this->templating = $templating;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplatePath():string
    {
        return $this->templatePath;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplatePath(string $templatePath):PaginatorInterface
    {
        $this->templatePath = $templatePath;
        return $this;
    }
}
