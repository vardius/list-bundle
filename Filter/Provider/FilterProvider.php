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
use Vardius\Bundle\ListBundle\Filter\Types\FilterType;

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
     * @param ActionFactory $actionFactory
     */
    public function __construct(FilterFactory $factory)
    {
        $this->factory = $factory;
        $this->filters = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $field
     * @param string|callable|FilterType $type
     * @param array $options
     * @return $this
     */
    protected function addFilter($field, $type, array $options = [])
    {
        if (is_string($type) || $type instanceof FilterType) {
            $filter = $this->factory->get($type, $options);
        } elseif (is_callable($type)) {
            $filter = $type;
        }else{
            throw new \InvalidArgumentException(
                'Expected argument of type "callable", "string" or class Vardius\Bundle\ListBundle\Filter\Types\FilterType, '.get_class(
                    $filter
                ).' given'
            );
        }

        $this->filters->set($field, $filter);

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    protected function removeFilter($key)
    {
        $this->filters->remove($key);

        return $this;
    }

}
