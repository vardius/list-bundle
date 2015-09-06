<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Types\Type;

use Vardius\Bundle\ListBundle\Column\Types\AbstractColumnType;

/**
 * CallableColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CallableColumnType extends AbstractColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null)
    {
        $callback = null;

        if (array_key_exists('callback', $this->options)) {
            $callable = $this->options['callback'];

            if (is_callable($callable)) {
                $callback = call_user_func_array($callable, [$entity]);
            }
        }

        return $this->templating->render($this->getView(), [
            'property' => $callback,
            'action' => $this->getAction()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, ['callback']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'callable';
    }
}
