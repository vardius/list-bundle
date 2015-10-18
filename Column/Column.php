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
use Symfony\Component\OptionsResolver\OptionsResolver;
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
    /** @var  array */
    protected $options;
    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $templatePath = 'VardiusListBundle:Column\\Type:';

    /**
     * @param string $property
     * @param ColumnType $type
     * @param array $options
     */
    function __construct($property, ColumnType $type, array $options = [], TwigEngine $templating)
    {
        $this->type = $type;
        $this->templating = $templating;

        $resolver = new OptionsResolver();
        $this->type->configureOptions($resolver, $property, $this->templatePath);

        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getData($entity)
    {
        return $this->templating->render(
            $this->options['view'],
            $this->type->getData($entity, $this->options),
            $this->options
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->options['label'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSort()
    {
        return $this->options['sort'];
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty()
    {
        return $this->options['property'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttr()
    {
        return $this->options['attr'];
    }

    public function isUi()
    {
        return $this->options['ui'];
    }

}
