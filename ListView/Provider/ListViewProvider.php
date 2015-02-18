<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\ListView\Provider;


use Symfony\Component\Form\AbstractType;
use Vardius\Bundle\ListBundle\ListView\Factory\ListViewFactory;

/**
 * ListViewProvider
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class ListViewProvider implements ListViewProviderInterface
{
    /** @var ListViewFactory */
    protected $listViewFactory;
    /** @var  AbstractType */
    protected $filterFormType;

    /**
     * @param ListViewFactory $listViewFactory
     */
    function __construct(ListViewFactory $listViewFactory)
    {
        $this->listViewFactory = $listViewFactory;
    }
}