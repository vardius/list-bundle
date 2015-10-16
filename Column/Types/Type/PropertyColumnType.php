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

use Vardius\Bundle\ListBundle\Column\Types\ColumnType;

/**
 * PropertyColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class PropertyColumnType extends ColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null)
    {
        $action = $this->getAction();
        $property = $this->getProperty();

        if ($entity !== null) {
            $property = $entity->{'get' . ucfirst($this->getProperty())}();

            if ($action !== null) {
                $action['parameters']['id'] = $entity->getId();
            }
        }

        return $this->templating->render($this->getView(), [
            'property' => $property,
            'action' => $action
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'property';
    }
}
