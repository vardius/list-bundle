<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\ListView\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Vardius\Bundle\ListBundle\ListView\ListView;

/**
 * ListViewFactory
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListViewFactory
{
    /** @var int */
    protected $limit;
    /** @var string */
    protected $title;
    /** @var  EventDispatcherInterface */
    protected $dispatcher;
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param $limit
     * @param $title
     * @param ContainerInterface $container
     */
    function __construct($limit, $title, ContainerInterface $container)
    {
        $this->limit = $limit;
        $this->title = $title;
        $this->container = $container;
        $this->dispatcher = $this->container->get('event_dispatcher');
    }

    /**
     * @return ListView
     */
    public function get()
    {
        $listView = new ListView($this->container, $this->limit, $this->title, $this->dispatcher);

        return $listView;
    }
}
