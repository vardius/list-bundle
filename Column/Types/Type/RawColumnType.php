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

/**
 * RawColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class RawColumnType extends CallableColumnType
{
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null)
    {
        $action = $this->getAction();

        $callback = null;
        if (array_key_exists('callback', $this->options)) {
            $callable = $this->options['callback'];

            if (is_callable($callable)) {
                $callback = call_user_func_array($callable, [$entity]);
            }
        }

        if ($entity !== null && $callback === null) {
            $property = $entity->{'get' . ucfirst($this->getProperty())}();

            if ($action !== null) {
                $action['parameters']['id'] = $entity->getId();
            }
        } elseif ($callback !== null) {
            $property = $callback;
        } else {
            throw new \InvalidArgumentException('Property or callback value have to be provided!');
        }

        return $this->templating->render($this->getView(), [
            'property' => $property,
            'action' => $action
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'raw';
    }
}
