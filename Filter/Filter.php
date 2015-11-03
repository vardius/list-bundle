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

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\ListBundle\Event\FilterEvent;
use Vardius\Bundle\ListBundle\Filter\Types\FilterType;

/**
 * Filter
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class Filter implements FilterInterface
{
    /** @var array */
    protected $options;
    /** @var array */
    private static $resolversByClass = array();
    /** @var  FilterType */
    protected $type;

    /**
     * @inheritDoc
     */
    public function __construct(FilterType $type, array $options = [])
    {
        $this->type = $type;
        $this->setOptions($options);
    }

    /**
     * @inheritDoc
     */
    public function apply(FilterEvent $event)
    {
        return $this->type->apply($event, $this->options);
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options = [])
    {
        $class = get_class($this);
        if (!isset(self::$resolversByClass[$class])) {
            self::$resolversByClass[$class] = new OptionsResolver();
            $this->type->configureOptions(self::$resolversByClass[$class]);
        }

        $this->options = self::$resolversByClass[$class]->resolve($options);
    }

    /**
     * @inheritDoc
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public static function clearOptionsConfig()
    {
        self::$resolversByClass = array();
    }

    /**
     * @return FilterType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param FilterType $type
     * @return Filter
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

}
