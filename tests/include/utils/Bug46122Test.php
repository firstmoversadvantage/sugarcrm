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
require_once('include/MVC/View/SugarView.php');

class Bu46122Test extends Sugar_PHPUnit_Framework_TestCase
{
    var $hasCustomModulesLogicHookFile = false;
    var $hasCustomContactLogicHookFile = false;
    var $modulesHookFile = 'custom/modules/logic_hooks.php';
    var $contactsHookFile = 'custom/modules/Contacts/logic_hooks.php';

    public function setUp()
    {
        //Setup mock logic hook files
        if(file_exists($this->modulesHookFile))
        {
            $this->hasCustomModulesLogicHookFile = true;
            copy($this->modulesHookFile, $this->modulesHookFile.'.bak');
        } else {
            write_array_to_file("test", array(), $this->modulesHookFile);
        }

        if(file_exists($this->contactsHookFile))
        {
            $this->hasCustomContactLogicHookFile = true;
            copy($this->contactsHookFile, $this->contactsHookFile.'.bak');
        } else {
            write_array_to_file("test", array(), $this->contactsHookFile);
        }

        $this->useOutputBuffering = false;
        LogicHook::refreshHooks();
    }

    public function tearDown()
    {
        //Remove the custom logic hook files
        if($this->hasCustomModulesLogicHookFile && file_exists($this->modulesHookFile.'.bak'))
        {
            copy($this->modulesHookFile.'.bak', $this->modulesHookFile);
            unlink($this->modulesHookFile.'.bak');
        } else if(file_exists($this->modulesHookFile)) {
            unlink($this->modulesHookFile);
        }

        if($this->hasCustomContactLogicHookFile && file_exists($this->contactsHookFile.'.bak'))
        {
            copy($this->contactsHookFile.'.bak', $this->contactsHookFile);
            unlink($this->contactsHookFile.'.bak');
        } else if(file_exists($this->contactsHookFile)) {
            unlink($this->contactsHookFile);
        }
        unset($GLOBALS['logic_hook']);
    }

    public function testSugarViewProcessLogicHookWithModule()
    {
        $GLOBALS['logic_hook'] = new LogicHookMock();
        $hooks = $GLOBALS['logic_hook']->getHooks('Contacts');
        $sugarViewMock = new SugarViewMock();
        $sugarViewMock->module = 'Contacts';
        $sugarViewMock->process();
        $expectedHookCount = isset($hooks['after_ui_frame']) ? count($hooks['after_ui_frame']) : 0;
        $this->assertEquals($expectedHookCount, $GLOBALS['logic_hook']->hookRunCount, 'Assert that two logic hook files were run');
    }


    public function testSugarViewProcessLogicHookWithoutModule()
    {
        $GLOBALS['logic_hook'] = new LogicHookMock();
        $hooks = $GLOBALS['logic_hook']->getHooks('');
        $sugarViewMock = new SugarViewMock();
        $sugarViewMock->module = '';
        $sugarViewMock->process();
        $expectedHookCount = isset($hooks['after_ui_frame']) ? count($hooks['after_ui_frame']) : 0;
        $this->assertEquals($expectedHookCount, $GLOBALS['logic_hook']->hookRunCount, 'Assert that one logic hook file was run');
    }
}

class SugarViewMock extends SugarView
{
    var $options = array();
    //no-opt methods we override
    function _trackView() {}
    function renderJavascript() {}
    function _buildModuleList() {}
    function preDisplay() {}
    function displayErrors() {}
    function display() {}
}

class LogicHookMock extends LogicHook
{
    var $hookRunCount = 0;

    function process_hooks($hook_array, $event, $arguments)
    {
        if($event == 'after_ui_frame')
        {
            $this->hookRunCount++;
        }
    }
}

?>