<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Types;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class ColumnType implements ColumnTypeInterface
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver, $property, $templatePath)
    {
        $resolver->setDefaults(
            array(
                'property' => $property,
                'label' => $property,
                'sort' => false,
                'ui' => false,
                'attr' => [],
                'row_action' => [],
                'view' => $templatePath . $this->getName() . '.html.twig'
            )
        );
        $resolver->setAllowedTypes(
            array(
                'label' => 'string',
                'property' => 'string',
                'view' => 'string',
                'sort' => 'bool',
                'ui' => 'bool',
                'attr' => 'array',
                'row_action' => 'array',
            )
        );
    }

}
