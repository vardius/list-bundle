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
    public function render();

    /**
     * Returns last page number
     *
     * @return float
     */
    public function getLastPage();

    /**
     * Returns current page number
     *
     * @return int
     */
    public function getCurrentPage();

    /**
     * Returns previous page number
     *
     * @return int
     */
    public function getPreviousPage();

    /**
     * Returns next page number
     *
     * @return float
     */
    public function getNextPage();

    /**
     * @return TwigEngine
     */
    public function getTemplating();

    /**
     * @param TwigEngine $templating
     */
    public function setTemplating($templating);

    /**
     * @return string
     */
    public function getTemplatePath();

    /**
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath);

}
