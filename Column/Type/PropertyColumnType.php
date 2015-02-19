<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Type;


/**
 * PropertyColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class PropertyColumnType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null)
    {
        $property = $this->getProperty();

        if ($entity !== null) {
            $property = $entity->get{ucfirst($this->getProperty())}();
        }

        return $this->templating->render($this->getView(), [
            'property' => $property,
            'isDate' => ($property instanceof \DateTime),
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