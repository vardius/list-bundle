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
     * Allowed types of actions
     * @var array
     */
    protected static $allowedTypes = ['row', 'global'];

    /**
     * @param string $path
     * @param string $name
     * @param string $type
     * @param string $icon
     * @return Action
     */
    public function get($path, $name = null, $type = 'row', $icon = null)
    {
        if (!in_array($type, self::$allowedTypes)) {
            throw new \InvalidArgumentException('The type "' . $type . '" does not exist. Known types are: ", ' . implode(",", self::$allowedTypes) . '"');
        }

        if ($name === null && $icon === null) {
            throw new \InvalidArgumentException('One of the parameters (name or icon) has to be provided');
        }

        return new Action($path, $name, $type, $icon);
    }
}