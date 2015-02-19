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


use Symfony\Bridge\Twig\TwigEngine;

/**
 * ColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class AbstractColumnType implements ColumnTypeInterface
{
    /** @var array */
    protected $options = [
        'label',
    ];
    /** @var  string */
    protected $property;

    /** @var  string */
    protected $templating;

    protected $templatePath = 'VardiusListBundle:Column\\type:';

    /**
     * {@inheritdoc}
     */
    public function setTemplateEngine(TwigEngine $templating)
    {
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateEngine()
    {
        return $this->templating;
    }

    /**
     * {@inheritdoc}
     */
    function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        if (array_key_exists('label', $this->options)) {

            return $this->options['label'];
        }

        return $this->getProperty();
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getView()
    {
        return $this->getTemplatePath() . $this->getName() . '.html.twig';
    }
}