<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Filter\Types;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * FilterTypePool
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class FilterTypePool
{
    /** @var ArrayCollection */
    protected $types;

    /**
     * {@inheritdoc}
     */
    function __construct()
    {
        $this->types = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getTypes():ArrayCollection
    {
        return $this->types;
    }

    /**
     * @param FilterTypeInterface $type
     */
    public function addType(FilterTypeInterface $type)
    {
        $this->types->set(get_class($type), $type);
    }

    /**
     * @param string $class
     * @return FilterTypeInterface
     */
    public function getType(string $class)
    {
        return $this->types->get($class);
    }
}
