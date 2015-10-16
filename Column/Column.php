<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column;

use Symfony\Bridge\Twig\TwigEngine;
use Vardius\Bundle\ListBundle\Column\Types\ColumnType;

/**
 * Column
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Column implements ColumnInterface
{
    /** @var  ColumnType */
    protected $type;

    /**
     * @param string $property
     * @param ColumnType $type
     * @param array $options
     */
    function __construct($property, ColumnType $type, array $options = [], TwigEngine $templating)
    {
        $this->type = $type;
        $this->type->setOptions($options);
        $this->type->setProperty($property);
        $this->type->setTemplateEngine($templating);
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty()
    {
        return $this->type->getProperty();
    }

    /**
     * {@inheritdoc}
     */
    public function getData($entity)
    {
        return $this->getType()->getData($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->type->getOptions();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->type->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getSort()
    {
        return $this->type->getSort();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttr()
    {
        return $this->type->getAttr();
    }

    /**
     * {@inheritdoc}
     */
    public function isUi()
    {
        return $this->type->isUi();
    }

}
