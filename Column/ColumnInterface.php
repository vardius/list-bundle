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

/**
 * ColumnInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ColumnInterface
{
    /**
     * Clear options array
     */
    public static function clearOptionsConfig();
    
    /**
     * @param $entity
     * @return mixed
     */
    public function getData($entity);

    /**
     * @return string|null
     */
    public function getLabel();

    /**
     * @return boolean
     */
    public function getSort();

    /**
     * @return array
     */
    public function getAttr();

    /**
     * Tells if column belongs to user interface
     *
     * @return bool
     */
    public function isUi();

}
