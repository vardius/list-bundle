<?php
/**
 * This file is part of the tipper package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Types\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Filter\Types\AbstractType;

/**
 * EntityType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class EntityType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('property', 'id');
        $resolver->setDefault('joinType', 'innerJoin');
        $resolver->setDefault('multiple', false);
        $resolver->addAllowedTypes('multiple', 'boolean');
        $resolver->addAllowedTypes('property', 'string');
        $resolver->addAllowedTypes('joinType', 'string');
        $resolver->addAllowedValues('joinType', ['leftJoin', 'innerJoin', 'join']);
    }

    /**
     * @inheritDoc
     */
    public function apply(FilterEvent $event, array $options)
    {
        $queryBuilder = $event->getQuery();
        if (!$queryBuilder instanceof QueryBuilder) {
            throw new \Exception('Vardius\Bundle\ListBundle\Filter\Types\EntityType supports only doctrine filters for now. To filter Propel or ElasticSearch Queries use callbacks or create your own FilterType classes');
        }

        $value = $event->getValue();
        if ($value instanceof ArrayCollection) {
            $value = $value->toArray();
        }

        if ($value && !empty($value)) {
            $field = empty($options['field']) ? $event->getField() : $options['field'];

            $queryBuilder->{$options['joinType']}($event->getAlias() . '.' . $field, $field);

            if ($options['multiple']) {
                $value = is_array($value) ? $value : [$value];
                $ids = [];
                foreach ($value as $entity){
                    $ids[] = $entity->getId();
                }
                $value = $ids;
                $queryBuilder->andWhere($field . '.' . $options['property'] . ' IN(:vardius_entity_' . $event->getField() . ')');
            } else {
                $queryBuilder->andWhere($field . '.' . $options['property'] . ' = :vardius_entity_' . $event->getField());
            }

            $queryBuilder->setParameter('vardius_entity_' . $event->getField(), $value);
        }

        return $queryBuilder;
    }
}
