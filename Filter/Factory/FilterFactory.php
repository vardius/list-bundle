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

use Vardius\Bundle\ListBundle\Filter\Filter;
use Vardius\Bundle\ListBundle\Filter\Types\FilterType;
use Vardius\Bundle\ListBundle\Filter\Types\FilterTypePool;

/**
 * FilterFactory
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FilterFactory
{
    /** @var  FilterTypePool */
    protected $pool;

    /**
     * FilterFactory constructor.
     * @param FilterTypePool $pool
     */
    public function __construct(FilterTypePool $pool)
    {
        $this->pool = $pool;
    }

    /**
     * @param $type
     * @param array $options
     * @return Filter
     */
    public function get($type, array $options = [])
    {
        if (is_string($type)) {
            $type = $this->pool->getFilter($type);
        }

        if (!$type instanceof FilterType) {
            throw new \InvalidArgumentException(
                'The type mast be instance of Vardius\Bundle\ListBundle\Filter\Types\FilterType. '.$type.' given'
            );
        }

        return new Filter($type, $options);
    }

}
