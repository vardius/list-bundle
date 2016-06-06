<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use Vardius\Bundle\ListBundle\Filter\Factory\FilterFactory;
use Vardius\Bundle\ListBundle\Filter\Types\FilterTypeInterface;

/**
 * FilterProvider
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class FilterProvider implements FilterProviderInterface
{
    /** @var ArrayCollection */
    protected $filters;
    /** @var  FilterFactory */
    protected $factory;

    /**
     * ActionsProvider constructor.
     * @param FilterFactory $factory
     */
    public function __construct(FilterFactory $factory)
    {
        $this->factory = $factory;
        $this->filters = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getFilters():ArrayCollection
    {
        return $this->filters;
    }

    /**
     * @param string $field
     * @param mixed $type
     * @param array $options
     * @return FilterProvider
     */
    protected function addFilter(string $field, $type, array $options = []):self
    {
        if (is_string($type) || $type instanceof FilterTypeInterface) {
            $filter = $this->factory->get($type, $options);
        } elseif (is_callable($type)) {
            $filter = $type;
        } else {
            throw new \InvalidArgumentException(
                'Expected argument of type "callable", "string" or class Vardius\Bundle\ListBundle\Filter\Types\FilterTypeInterface, ' . get_class(
                    $type
                ) . ' given'
            );
        }

        $this->filters->set($field, $filter);

        return $this;
    }

    /**
     * @param string $key
     * @return FilterProvider
     */
    protected function removeFilter(string $key):self
    {
        $this->filters->remove($key);

        return $this;
    }
}
