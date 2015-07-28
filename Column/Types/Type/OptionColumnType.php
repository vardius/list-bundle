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

use Vardius\Bundle\ListBundle\Column\Types\AbstractColumnType;

/**
 * OptionColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class OptionColumnType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null)
    {
        return $this->templating->render($this->getView(), [
            'option' => $this,
            'entity' => $entity,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'option';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->templating->render($this->getView(), [
            'option' => $this,
        ]);
    }
}
