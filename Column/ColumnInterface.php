<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column;

use Vardius\Bundle\ListBundle\Column\Types\AbstractColumnType;

/**
 * ColumnInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ColumnInterface
{
    /**
     * @return string
     */
    public function getProperty();

    /**
     * @param $entity
     * @return mixed
     */
    public function getData($entity);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @return AbstractColumnType
     */
    public function getType();

    /**
     * @return string|null
     */
    public function getLabel();

    /**
     * @return boolean
     */
    public function getSort();

    /**
     * Tells if column belongs to user interface
     *
     * @return bool
     */
    public function isUi();

}
