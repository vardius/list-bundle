<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Column\Types;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ColumnTypeInterface
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
interface ColumnTypeInterface
{
    /**
     * Returns column data
     *
     * @param mixed $entity
     * @param array $options
     * @return array
     */
    public function getData($entity = null, array $options = []):array;

    /**
     * Configure options array
     *
     * @param OptionsResolver $resolver
     * @param $property
     * @param $templatePath
     * @return mixed
     */
    public function configureOptions(OptionsResolver $resolver, $property, $templatePath);
}
