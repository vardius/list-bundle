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
    function __construct(string $path, string $name = null, string $icon = null, array $parameters = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->icon = $icon;
        $this->parameters = $parameters;
    }

    /**
     * @inheritDoc
     */
    public function getName():string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name):self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath():string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path):self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIcon():string
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function setIcon(string $icon):self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParameters():array
    {
        return $this->parameters;
    }

    /**
     * @inheritDoc
     */
    public function setParameters(array $parameters):self
    {
        $this->parameters = $parameters;
        return $this;
    }
}
