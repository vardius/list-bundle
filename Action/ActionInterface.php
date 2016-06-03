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
    function __construct(string $path, string $name = null, string $icon = null, array $parameters = []);

    /**
     * @return string
     */
    public function getName():string;

    /**
     * @param string $name
     * @return ActionInterface
     */
    public function setName(string $name):self;

    /**
     * @return string
     */
    public function getPath():string;

    /**
     * @param string $path
     * @return ActionInterface
     */
    public function setPath(string $path):self;

    /**
     * @return string
     */
    public function getIcon():string;

    /**
     * @param string $icon
     * @return ActionInterface
     */
    public function setIcon(string $icon):self;

    /**
     * @return array
     */
    public function getParameters(): array;

    /**
     * @param array $parameters
     * @return ActionInterface
     */
    public function setParameters(array $parameters):self;
}
