<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Type;


/**
 * CallableColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CallableColumnType extends AbstractColumnType
{
    /** @var array */
    protected $options = ['method'];

    /**
     * @param mixed $entity
     * @return mixed|string
     */
    public function getData($entity = null)
    {
        $callable = $this->options['method'];
        if (is_callable($callable)) {
            return call_user_func_array($callable, [$entity]);
        }

        return $this->getName();
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        return 'callable';
    }
}