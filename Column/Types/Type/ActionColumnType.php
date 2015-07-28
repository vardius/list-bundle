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

use Vardius\Bundle\ListBundle\Action\Factory\ActionFactory;
use Vardius\Bundle\ListBundle\Column\Types\AbstractColumnType;

/**
 * ActionColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ActionColumnType extends AbstractColumnType
{
    protected $actionFactory;

    function __construct(ActionFactory $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($entity = null)
    {
        $items = [];

        if (array_key_exists('actions', $this->options)) {
            $actions = $this->options['actions'];

            foreach ($actions as $action) {
                $path = array_key_exists('path', $action) ? $action['path'] : null;
                $name = array_key_exists('name', $action) ? $action['name'] : null;
                $icon = array_key_exists('icon', $action) ? $action['icon'] : null;
                $parameters = array_key_exists('parameters', $action) ? $action['parameters'] : [];
                $parameters['id'] = $entity->getId();

                $items[] = $this->actionFactory->get($path, $name, $icon, $parameters);
            }
        }

        return $this->templating->render($this->getView(), [
            'actions' => $items,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, ['actions']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'action';
    }
}
