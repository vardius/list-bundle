<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\ListView\Provider;


use Vardius\Bundle\ListBundle\ListView\ListView;

/**
 * ListViewProviderInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ListViewProviderInterface
{
    /**
     * Provides list view
     *
     * @return ListView
     */
    public function buildListView();
}