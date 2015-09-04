<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Types;

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
        'sort',
        'url'
    ];
    /** @var  string */
    protected $property;
    /** @var TwigEngine */
    protected $templating;
    /** @var string */
    protected $templatePath = 'VardiusListBundle:Column\\Type:';

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
        $label = $this->getProperty();

        if (array_key_exists('label', $this->options)) {

            $label = $this->options['label'];
        }

        return strtoupper($label);
    }

    /**
     * {@inheritdoc}
     */
    public function getSort()
    {
        $sort = false;
        if (array_key_exists('sort', $this->options)) {

            $sort = $this->options['sort'];
            $sort = is_bool($sort) ? $sort : false;
        }

        return $sort;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction()
    {
        $action = null;
        if (array_key_exists('url', $this->options)) {

            $url = $this->options['url'];
            if (!empty($url)) {
                $path = array_key_exists('path', $url) ? $url['path'] : null;
                $parameters = array_key_exists('parameters', $url) ? $url['parameters'] : [];

                $action = [
                    'path' => $path,
                    'parameters' => $parameters,
                ];
            }
        }

        return $action;
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

    /**
     * {@inheritdoc}
     */
    public function isUi()
    {
        return false;
    }

}
