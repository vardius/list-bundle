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
 * CallableColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CallableColumnType extends ColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null, array $options = [])
    {
        $callback = null;
        $callable = $options['callback'];

        if (is_callable($callable)) {
            $callback = call_user_func_array($callable, [$entity]);
        }

        $action = $options['row_action'];
        if (is_array($action) && !empty($action) && $entity !== null) {
            $action['parameters']['id'] = $entity->getId();
        }

        return [
            'property' => $callback,
            'action' => $action
        ];
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver, $property, $templatePath)
    {
        parent::configureOptions($resolver, $property, $templatePath);

        $resolver->setAllowedTypes('callback', ['closure', 'null']);
        $resolver->setRequired('callback');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'callable';
    }
}
