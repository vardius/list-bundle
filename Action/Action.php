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
    protected $type;
    /** @var  string */
    protected $icon;

    /**
     * @param string $path
     * @param string $name
     * @param string $type
     * @param string $icon
     */
    function __construct($path, $name = null, $type = 'row', $icon = null)
    {
        $this->name = $name;
        $this->path = $path;
        $this->type = $type;
        $this->icon = $icon;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
}