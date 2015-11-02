<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Provider;

interface FilterProviderInterface
{
    /**
     * Provides filters for list view
     *
     * @return ArrayCollection
     */
    public function build();

}
