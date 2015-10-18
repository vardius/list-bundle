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
 * DateColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DateColumnType extends ColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null, array $options = [])
    {
        $callable = $options['callback'];
        if (is_callable($callable)) {
            $property = call_user_func_array($callable, [$entity]);
        } else {
            $property = $entity->{'get' . ucfirst($options['property'])}();
        }

        $action = $options['row_action'];
        if (is_array($action) && !empty($action) && $entity !== null) {
            $action['parameters']['id'] = $entity->getId();
        }

        return [
            'property' => $property,
            'action' => $action,
            'format' => $options['date_format'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver, $property, $templatePath)
    {
        parent::configureOptions($resolver, $property, $templatePath);

        $resolver->setDefault('date_format', null);
        $resolver->setAllowedTypes('date_format', ['string', 'null']);
        $resolver->setDefault('callback', null);
        $resolver->setAllowedTypes('callback', ['closure', 'null']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'date';
    }
}
