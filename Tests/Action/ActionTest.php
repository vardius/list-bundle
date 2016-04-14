<?php
/**
 * This file is part of the vardius/list-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\ListBundle\Action;

/**
 * ActionTest
 *
 * @author Szymon Kunowski <szymon.kunowski@gmail.com>
 */
class ActionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetNameReturnString()
    {
        $action = new Action("path", "name", "icon", []);
        $this->assertEquals("name", $action->getName());
    }

    public function testGetIconReturnString()
    {
        $action = new Action("path", "name", "icon", []);
        $this->assertEquals("icon", $action->getIcon());
    }

    public function testGetPathReturnString()
    {
        $action = new Action("path", "name", "icon", []);
        $this->assertEquals("path", $action->getPath());
    }

    public function testGetParametersReturnString()
    {
        $action = new Action("path", "name", "icon", []);
        $this->assertEquals([], $action->getParameters());
    }
}
