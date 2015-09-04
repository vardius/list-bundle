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
 * ColumnTypeInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ColumnTypeInterface
{
    /**
     * Returns column data
     *
     * @param mixed $entity
     * @return mixed
     */
    public function getData($entity = null);

    /**
     * Sets template engine
     *
     * @param TwigEngine $templating
     */
    public function setTemplateEngine(TwigEngine $templating);

    /**
     * Return template engine
     *
     * @return TwigEngine
     */
    public function getTemplateEngine();

    /**
     * Returns options array
     *
     * @return array
     */
    function getOptions();

    /**
     * Add options
     *
     * @param array $options
     */
    public function setOptions($options);

    /**
     * Returns column property
     *
     * @return string
     */
    public function getProperty();

    /**
     * Sets column property
     *
     * @param string $property
     */
    public function setProperty($property);

    /**
     * Get column label, from array if set or property
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get column sorting options, true if enable
     *
     * @return boolean
     */
    public function getSort();

    /**
     * Get column action
     *
     * @return array
     */
    public function getAction();

    /**
     * Returns column type name
     *
     * @return string
     */
    public function getName();

    /**
     * Returns view template path
     *
     * @return string
     */
    public function getTemplatePath();

    /**
     * Sets views template path
     *
     * @param string $templatePath
     */
    public function setTemplatePath($templatePath);

    /**
     * Returns view for column type (templatePath + name)
     *
     * @return string
     */
    public function getView();

    /**
     * Tells if column belongs to user interface
     *
     * @return bool
     */
    public function isUi();
}
