<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Types\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\ListBundle\Column\Types\ColumnType;

/**
 * RawColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class RawColumnType extends ColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null, array $options = [])
    {
        $callable = $options['callback'];
        $property = null;
        if (is_callable($callable)) {
            $property = call_user_func_array($callable, [$entity]);
        } else {
            $method = ucfirst($options['property']);
            if (method_exists($entity, 'get' . $method)) {
                $property = $entity->{'get' . $method}();
            } elseif (method_exists($entity, 'is' . $method)) {
                $property = $entity->{'is' . $method}();
            }
        }

        $action = $options['row_action'];
        if (is_array($action) && !empty($action) && $entity !== null) {
            $action['parameters']['id'] = $entity->getId();
        }

        return [
            'property' => $property,
            'action' => $action
        ];
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver, $property, $templatePath)
    {
        parent::configureOptions($resolver, $property, $templatePath);

        $resolver->setDefault('callback', null);
        $resolver->setAllowedTypes('callback', ['closure', 'null', 'array']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'raw';
    }
}
