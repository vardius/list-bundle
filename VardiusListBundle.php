<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vardius\Bundle\ListBundle\DependencyInjection\Compiler\ColumnTypePass;
use Vardius\Bundle\ListBundle\DependencyInjection\Compiler\FilterTypePass;

class VardiusListBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ColumnTypePass());
        $container->addCompilerPass(new FilterTypePass());
    }
}
