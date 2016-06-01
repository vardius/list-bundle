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
 * ActionInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ActionInterface
{
    /**
     * @param string $path
     * @param string $name
     * @param string $icon
     * @param array $parameters
     */
    function __construct($path, $name = null, $icon = null, $parameters = []);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getIcon();

    /**
     * @param string $icon
     */
    public function setIcon($icon);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param array $parameters
     */
    public function setParameters($parameters);
}
