<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Action;


/**
 * Action
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Action
{
    /** @var  string */
    protected $name;
    /** @var  string */
    protected $path;
    /** @var  string */
    protected $icon;
    /** @var  array */
    protected $parameters;

    /**
     * @param string $path
     * @param string $name
     * @param string $icon
     * @param array $parameters
     */
    function __construct($path, $name = null, $icon = null, $parameters = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->icon = $icon;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}