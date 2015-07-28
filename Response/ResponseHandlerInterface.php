<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Response;

/**
 * ResponseHandlerInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ResponseHandlerInterface
{
    /**
     * @param $view
     * @param $params
     * @return string
     */
    public function renderView($view, $params);
}