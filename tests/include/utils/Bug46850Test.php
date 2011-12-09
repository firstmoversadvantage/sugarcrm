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


require_once('include/utils/LogicHook.php');

class Bu46850Test extends Sugar_PHPUnit_Framework_TestCase
{
    protected $renames = array();
    protected $deletes = array();

    protected $hook = array(
        'test_logic_hook' => array(array(1, 'test_logic_hook', __FILE__, 'LogicHookTest', 'testLogicHook')),
    );

    public function setUp()
    {
        LogicHookTest::$called = false;
        unset($GLOBALS['logic_hook']);
        $GLOBALS['logic_hook'] = LogicHook::initialize();
        LogicHook::refreshHooks();
    }

    public function tearDown()
    {
        foreach($this->renames as $file) {
            rename($file.".bak", $file);
        }
        foreach($this->deletes as $file) {
            unlink($file);
        }
        unset($GLOBALS['logic_hook']);
        LogicHook::refreshHooks();
    }

    protected function saveHook($file)
    {
        if(file_exists($file)) {
            rename($file, $file.".bak");
            $this->renames[] = $file;
        } else {
            $this->deletes[] = $file;
        }
    }

    public function getModules()
    {
        return array(
            array(''),
            array('Contacts'),
            array('Accounts'),
        );
    }

    /**
     * @dataProvider getModules
     */
    public function testHooksDirect($module)
    {
        $dir = rtrim("custom/modules/$module", "/");
        $file = "$dir/logic_hooks.php";
        $this->saveHook($file);
        if(!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        write_array_to_file('hook_array', $this->hook, $file);
        $GLOBALS['logic_hook']->getHooks($module, true); // manually refresh
        $GLOBALS['logic_hook']->call_custom_logic($module, 'test_logic_hook');
        $this->assertTrue(LogicHookTest::$called);
    }

    /**
     * @dataProvider getModules
     */
    public function testHooksExtDirect($module)
    {
        if(empty($module)) {
            $dir = "custom/application/Ext/LogicHooks";
        } else {
            $dir = "custom/modules/$module/Ext/LogicHooks";
        }
        if(!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $file = "$dir/logichooks.ext.php";
        $this->saveHook($file);
        write_array_to_file('hook_array', $this->hook, $file);
        $GLOBALS['logic_hook']->getHooks($module, true); // manually refresh
        $GLOBALS['logic_hook']->call_custom_logic($module, 'test_logic_hook');
        $this->assertTrue(LogicHookTest::$called);
    }

    /**
     * @dataProvider getModules
     */
    public function testHooksUtils($module)
    {
        $dir = rtrim("custom/modules/$module", "/");
        $file = "$dir/logic_hooks.php";
        if(!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $this->saveHook($file);
        check_logic_hook_file($module, 'test_logic_hook', $this->hook['test_logic_hook'][0]);
        $GLOBALS['logic_hook']->getHooks($module, true); // manually refresh
        $GLOBALS['logic_hook']->call_custom_logic($module, 'test_logic_hook');
        $this->assertTrue(LogicHookTest::$called);
    }


    /**
     * @dataProvider getModules
     */
    public function testGeHookArray($module)
    {
        $dir = rtrim("custom/modules/$module", "/");
        $file = "$dir/logic_hooks.php";
        if(!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $this->saveHook($file);
        check_logic_hook_file($module, 'test_logic_hook', $this->hook['test_logic_hook'][0]);
        $array = get_hook_array($module);
        $this->assertEquals($this->hook, $array);
    }
}

class LogicHookTest {
    public static $called = false;
    function testLogicHook() {
        self::$called = true;
    }
}
