<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Action\Factory;


use Vardius\Bundle\ListBundle\Action\Action;

/**
 * ActionFactory
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ActionFactory
{
    /**
     * @param string $path
     * @param string $name
     * @param string $icon
     * @param array $parameters
     * @return Action
     */
    public function get($path, $name = null, $icon = null, $parameters = [])
    {
        if ($name === null && $icon === null) {
            throw new \InvalidArgumentException('One of the arguments (name or icon) has to be provided');
        }

        if (!is_array($parameters)) {
            throw new \InvalidArgumentException('The $parameters mast be an array. ' . $parameters . ' given');
        }

        return new Action($path, $name, $icon, $parameters);
    }
}