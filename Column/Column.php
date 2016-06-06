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
use Vardius\Bundle\ListBundle\Column\Types\ColumnTypeInterface;

/**
 * Column
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Column implements ColumnInterface
{
    /** @var  ColumnTypeInterface */
    protected $type;
    /** @var  array */
    protected $options;
    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $templatePath = 'VardiusListBundle:Column\\Type:';

    /**
     * @param string $property
     * @param ColumnTypeInterface $type
     * @param array $options
     */
    function __construct(string $property, ColumnTypeInterface $type, array $options = [], TwigEngine $templating)
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
    public function getData($entity, string $responseType = 'html')
    {
        $data = $this->type->getData($entity, $this->options);
        if ($responseType === 'html') {
            return $this->templating->render(
                $this->options['view'],
                $data,
                $this->options
            );
        }

        return array_key_exists('property', $data) ? $data['property'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel():string
    {
        return $this->options['label'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSort():bool
    {
        return $this->options['sort'];
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty():string
    {
        return $this->options['property'];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttr():array
    {
        return $this->options['attr'];
    }

    public function isUi():bool
    {
        return $this->options['ui'];
    }
}
