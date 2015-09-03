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
 * PropertyColumnType
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class PropertyColumnType extends AbstractColumnType
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
        $property = $this->getProperty();
        $action = null;

        if ($entity !== null) {
            $property = $entity->{'get' . ucfirst($this->getProperty())}();

            $url = $this->options['url'];

            $path = array_key_exists('path', $url) ? $url['path'] : null;
            $parameters = array_key_exists('parameters', $url) ? $url['parameters'] : [];
            $parameters['id'] = $entity->getId();

            $action = $this->actionFactory->get($path, null, null, $parameters);
        }

        return $this->templating->render($this->getView(), [
            'property' => $property,
            'isDate' => ($property instanceof \DateTime),
            'format' => $this->options['date_format'],
            'action' => $action,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, ['date_format', 'url']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'property';
    }
}
