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
use Elastica\Query;
use Symfony\Bridge\Twig\TwigEngine;

/**
 * PaginatorInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface PaginatorInterface
{
    /**
     * Paginate the results
     *
     * @return mixed
     */
    public function paginate();

    /**
     * Renders view
     *
     * @return string
     */
    public function render():string;

    /**
     * Returns last page number
     *
     * @return float
     */
    public function getLastPage():float;

    /**
     * Returns current page number
     *
     * @return int
     */
    public function getCurrentPage():int;

    /**
     * Returns previous page number
     *
     * @return int
     */
    public function getPreviousPage():int;

    /**
     * Returns next page number
     *
     * @return float
     */
    public function getNextPage():float;

    /**
     * @return TwigEngine
     */
    public function getTemplating():TwigEngine;

    /**
     * @param TwigEngine $templating
     * @return PaginatorInterface
     */
    public function setTemplating(TwigEngine $templating):self;

    /**
     * @return string
     */
    public function getTemplatePath():string;

    /**
     * @param string $templatePath
     * @return PaginatorInterface
     */
    public function setTemplatePath(string $templatePath):self;
}
