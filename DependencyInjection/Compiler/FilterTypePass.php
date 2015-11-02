<?php
/**
 * This file is part of the tipper package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class FilterTypePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('vardius_list.filter_type_pool')) {
            return;
        }

        $definition = $container->getDefinition(
            'vardius_list.filter_type_pool'
        );

        $columnTypes = $container->findTaggedServiceIds(
            'vardius_list.filter_type'
        );
        foreach ($columnTypes as $id => $columnType) {
            $definition->addMethodCall(
                'addType',
                [new Reference($id)]
            );
        }
    }
}
