<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 23.11.15
 * Time: 11:29
 */

namespace Vardius\Bundle\ListBundle\Action;


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
