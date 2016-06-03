<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Types;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\ListBundle\Event\FilterEvent;

/**
 * FilterTypeInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface FilterTypeInterface
{
    /**
     * Filter body, method is invoked when the filter is called from list view
     *
     * @param FilterEvent $event
     * @param array $options
     * @return mixed
     */
    public function apply(FilterEvent $event, array $options);

    /**
     * Adjust the configuration of the options
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver);

    /**
     * Returns filter type name
     *
     * @return string
     */
    public function getName():string;
}
