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
    protected $driver;
    /** @var boolean */
    protected $paginator;
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param int $limit
     * @param string $driver
     * @param boolean $paginator
     * @param ContainerInterface $container
     */
    function __construct($limit, $driver, $paginator, ContainerInterface $container)
    {
        $this->limit = $limit;
        $this->driver = $driver;
        $this->paginator = $paginator;
        $this->container = $container;
    }

    /**
     * @return ListView
     */
    public function get()
    {
        $listView = new ListView($this->limit, $this->driver, $this->paginator, $this->container);

        return $listView;
    }
}
