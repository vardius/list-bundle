<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * ColumnTypePass
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ColumnTypePass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('vardius_list.column_type_pool')) {
            return;
        }

        $definition = $container->getDefinition(
            'vardius_list.column_type_pool'
        );

        $columnTypes = $container->findTaggedServiceIds(
            'vardius_list.column_type'
        );
        foreach ($columnTypes as $id => $columnType) {
            $definition->addMethodCall(
                'addType',
                [new Reference($id)]
            );
        }
    }
}
