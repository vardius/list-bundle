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
    /**
     * {@inheritdoc}
     */
    public function getData($entity = null)
    {
        $action = null;
        $format = array_key_exists('date_format', $this->options) ? $this->options['date_format'] : null;

        $property = $this->getProperty();

        if ($entity !== null) {
            $property = $entity->{'get' . ucfirst($this->getProperty())}();

            $url = array_key_exists('url', $this->options) ? $this->options['url'] : [];
            if (!empty($url)) {
                $path = array_key_exists('path', $url) ? $url['path'] : null;
                $parameters = array_key_exists('parameters', $url) ? $url['parameters'] : [];
                $parameters['id'] = $entity->getId();

                $action = [
                    'path' => $path,
                    'parameters' => $parameters,
                ];
            }
        }

        return $this->templating->render($this->getView(), [
            'property' => $property,
            'isDate' => ($property instanceof \DateTime),
            'format' => $format,
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
