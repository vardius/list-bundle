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

/**
 * AbstractType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class AbstractType implements FilterTypeInterface
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'field' => '',
        ]);
        $resolver->setAllowedTypes('field', 'string');
    }
}
