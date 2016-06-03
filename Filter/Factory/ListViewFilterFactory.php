<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Vardius\Bundle\ListBundle\Filter\ListViewFilter;

/**
 * ListViewFilterFactory
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ListViewFilterFactory
{
    /** @var  ContainerInterface */
    protected $container;

    /**
     * ListViewFilterFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $formType
     * @param callable|string $filter
     * @return ListViewFilter
     */
    public function get($formType, $filter):ListViewFilter
    {
        if (is_string($filter)) {
            $filterProvider = $this->container->get($filter);
            $filterProvider->build();
            $filter = $filterProvider->getFilters();
        }

        return new ListViewFilter($formType, $filter);
    }
}
