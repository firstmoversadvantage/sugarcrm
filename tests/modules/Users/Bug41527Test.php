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

 
require_once 'modules/Users/User.php';

class Bug41527Test extends Sugar_PHPUnit_Framework_TestCase
{
    public $_default_max_tabs_set = false;
    public $_default_max_tabs = '';
    public $_max_tabs_test = 666;

    public function setUp() 
    {
        $this->_default_max_tabs_set == isset($GLOBALS['sugar_config']['default_max_tabs']);
        if ($this->_default_max_tabs_set) {
            $this->_default_max_tabs = $GLOBALS['sugar_config']['default_max_tabs'];
        }

        $beanList = array();
        $beanFiles = array();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['current_user']->is_admin = '1';
        $GLOBALS['sugar_config']['default_max_tabs'] = $this->_max_tabs_test;
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
        $GLOBALS['request_string'] = '';
    }

    public function tearDown() 
    {
        if ($this->_default_max_tabs_set) {
            $GLOBALS['sugar_config']['default_max_tabs'] = $this->_default_max_tabs;
        } else {
            unset($GLOBALS['sugar_config']['default_max_tabs']);
        }
        unset($GLOBALS['beanFiles']);
        unset($GLOBALS['beanList']);
        unset($GLOBALS['current_user']);
        unset($GLOBALS['app_list_strings']);
        unset($GLOBALS['app_strings']);
        unset($GLOBALS['request_string']);
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        SugarTestContactUtilities::removeAllCreatedContacts();
    }

    public function testUsingDefaultMaxTabsForOptionsValues() 
    {
        global $current_user, $locale, $sugar_config;
        
        ob_start();
        $_REQUEST['module'] = 'Users';
        require('modules/Users/EditView.php');
        $html = ob_get_clean();

        $this->assertRegExp('/<select name="user_max_tabs".*<option label="' . $this->_max_tabs_test . '" value="' . $this->_max_tabs_test . '".*>' . $this->_max_tabs_test . '<\/option>.*<\/select>/ms', $html);
    }
    
    /**
     * @ticket 42719
     */
    public function testAllowSettingMaxTabsTo10WhenSettingIsLessThan10() 
    {
        global $current_user, $locale, $sugar_config;
        
        $GLOBALS['sugar_config']['default_max_tabs'] = 7;
        
        ob_start();
        $_REQUEST['module'] = 'Users';
        require('modules/Users/EditView.php');
        $html = ob_get_clean();

        $this->assertRegExp('/<select name="user_max_tabs".*<option label="10" value="10".*>10<\/option>.*<\/select>/ms', $html);
    }

    /**
     * @ticket 42719
     */
    public function testUsersDefaultMaxTabsSettingHonored() 
    {
        global $current_user, $locale, $sugar_config;
        
        $current_user->setPreference('max_tabs', 3, 0, 'global');
        
        ob_start();
        $_REQUEST['module'] = 'Users';
        $_REQUEST['record'] = $current_user->id;
        require('modules/Users/EditView.php');
        $html = ob_get_clean();
        
        $this->assertRegExp('/<select name="user_max_tabs".*<option label="3" value="3" selected="selected">3<\/option>.*<\/select>/ms', $html);
    }
}

