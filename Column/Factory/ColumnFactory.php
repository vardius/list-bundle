<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Factory;


use Symfony\Bridge\Twig\TwigEngine;
use Vardius\Bundle\ListBundle\Column\Column;
use Vardius\Bundle\ListBundle\Column\Type\AbstractColumnType;
use Vardius\Bundle\ListBundle\Column\Type\ColumnTypePool;

/**
 * ColumnFactory
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ColumnFactory
{
    /** @var  ColumnTypePool */
    protected $columnTypePool;
    /** @var TwigEngine */
    protected $templating;

    /**
     * @param ColumnTypePool $columnTypePool
     */
    function __construct(ColumnTypePool $columnTypePool, TwigEngine $templating)
    {
        $this->columnTypePool = $columnTypePool;
        $this->templating = $templating;
    }

    /**
     * @param string $property
     * @param $type
     * @param array $options
     * @return Column
     */
    public function get($property, $type, array $options = [])
    {
        if (is_string($type)) {
            $type = $this->columnTypePool->getType($type);
        }

        if (!$type instanceof AbstractColumnType) {
            throw new \InvalidArgumentException('The $type mast be instance of AbstractColumnType. ' . $type . ' given');
        }

        $typOptions = $type->getOptions();
        foreach ($options as $key => $option) {
            if (!in_array($key, $typOptions)) {
                throw new \InvalidArgumentException('The option "' . $key . '" does not exist. Known options are: ", ' . implode(",", $typOptions) . '"');
            }
        }

        return new Column($property, $type, $options, $this->templating);
    }
}
