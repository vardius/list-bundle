<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <support@vardius.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Provider;

/**
 * FilterProviderInterface
 *
 * @author Rafał Lorenz <support@vardius.dev>
 */
interface FilterProviderInterface
{
    /**
     * Provides filters for list view
     */
    public function build();
}
