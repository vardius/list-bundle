<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Filter\Types\FilterType;
use Vardius\Bundle\ListBundle\Filter\Types\FilterTypeInterface;

/**
 * Filter
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Filter implements FilterInterface
{
    /** @var array */
    protected $options;
    /** @var  FilterType|callable */
    protected $type;

    /**
     * @inheritDoc
     */
    public function __construct($type, array $options = [])
    {
        $this->type = $type;
        if ($this->type instanceof FilterTypeInterface) {
            $this->setOptions($options);
        }
    }

    /**
     * @inheritDoc
     */
    public function apply(FilterEvent $event)
    {
        if (is_callable($this->type)) {
            return call_user_func_array($this->type, [$event]);
        }

        return $this->type->apply($event, $this->options);
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->type->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @inheritDoc
     */
    public function getOptions():array
    {
        return $this->options;
    }

    /**
     * @return FilterTypeInterface
     */
    public function getType():FilterTypeInterface
    {
        return $this->type;
    }

    /**
     * @param FilterTypeInterface $type
     * @return Filter
     */
    public function setType(FilterTypeInterface $type):self
    {
        $this->type = $type;

        return $this;
    }
}
