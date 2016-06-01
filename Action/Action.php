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
class Action implements ActionInterface
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
     * @inheritDoc
     */
    function __construct($path, $name = null, $icon = null, $parameters = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->icon = $icon;
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @inheritDoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
