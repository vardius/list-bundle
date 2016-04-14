<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\View;

/**
 * RendererInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface RendererInterface
{
    /**
     * @param $view
     * @param $params
     * @return string
     */
    public function renderView($view, $params);
}
