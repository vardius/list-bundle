<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Data\Factory;

use Vardius\Bundle\ListBundle\Data\DataProviderInterface;
use Vardius\Bundle\ListBundle\Data\Provider\{
    Doctrine\DataProvider as DoctrineDataProvider, ElasticSearch\DataProvider as ElasticDataProvider, Propel\DataProvider as PropelDataProvider
};

/**
 * Class DataProviderFactory
 * @package Vardius\Bundle\ListBundle\Data\Factory
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class DataProviderFactory
{
    public function get($dbDriver):DataProviderInterface
    {
        switch ($dbDriver) {
            case 'propel':
                return new PropelDataProvider();
            case 'elasticsearch':
                return new ElasticDataProvider();
            default:
                return new DoctrineDataProvider();
        }
    }
}
