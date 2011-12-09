<?php
/*********************************************************************************
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2011 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/

 
require_once 'include/SugarObjects/SugarRegistry.php';

class SugarRegistryTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_old_reporting = null;
    private $_old_globals = null;

    public function setUp() 
    {
        $this->_old_reporting = error_reporting(E_ALL);
        $this->_old_globals = $GLOBALS;
        unset($GLOBALS);
    }

    public function tearDown() 
    {
        error_reporting($this->_old_reporting);
        $GLOBALS = $this->_old_globals;
        unset($this->_old_globals);
    }

    public function testGetInstanceReturnsAnInstanceOfSugarRegistry() 
    {
        $this->assertTrue(SugarRegistry::getInstance() instanceOf SugarRegistry,'Returned object is not a SugarRegistry instance');
    }

    public function testGetInstanceReturnsSameObject() 
    {
        $one = SugarRegistry::getInstance();
        $two = SugarRegistry::getInstance();
        $this->assertSame($one, $two);
    }

    public function testParameterPassedToGetInstanceSpecifiesInstanceName() 
    {
        $foo1 = SugarRegistry::getInstance('foo');
        $foo2 = SugarRegistry::getInstance('foo');
        $this->assertSame($foo1, $foo2);

        $bar = SugarRegistry::getInstance('bar');
        $this->assertNotSame($foo1, $bar);
    }

    public function testCanSetAndGetValues() 
    {
        $random = rand(100, 200);
        $r = SugarRegistry::getInstance();
        $r->integer = $random;
        $this->assertEquals($random, $r->integer);
        $this->assertEquals($random, SugarRegistry::getInstance()->integer);
    }

    public function testIssetReturnsTrueFalse() 
    {
        $r = SugarRegistry::getInstance();
        $this->assertFalse(isset($r->foo));
        $this->assertFalse(isset(SugarRegistry::getInstance()->foo));

        $r->foo = 'bar';
        $this->assertTrue(isset($r->foo));
        $this->assertTrue(isset(SugarRegistry::getInstance()->foo));
    }

    public function testUnsetRemovesValueFromRegistry() 
    {
        $r = SugarRegistry::getInstance();
        $r->foo = 'bar';
        unset($r->foo);
        $this->assertFalse(isset($r->foo));
        $this->assertFalse(isset(SugarRegistry::getInstance()->foo));
    }

    public function testReturnsNullOnAnyUnknownValue() 
    {
        $r = SugarRegistry::getInstance();
        $this->assertNull($r->unknown);
        $this->assertNull(SugarRegistry::getInstance()->unknown);
    }

    public function testAddToGlobalsPutsRefsToAllRegistryObjectsInGlobalSpace() 
    {
        $r = SugarRegistry::getInstance();
        $r->foo = 'bar';

        $this->assertFalse(isset($GLOBALS['foo']), 'sanity check');
        $r->addToGlobals();
        $this->assertTrue(isset($GLOBALS['foo']));
    }
}

