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


class Bug43653Test extends Sugar_PHPUnit_Framework_OutputTestCase
{
    public function setUp()
    {
		$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
		if(file_exists($GLOBALS['sugar_config']['cache_dir']. 'modules/unified_search_modules.php'))
		{
			copy($GLOBALS['sugar_config']['cache_dir']. 'modules/unified_search_modules.php', $GLOBALS['sugar_config']['cache_dir']. 'modules/unified_search_modules.php.bak');
			unlink($GLOBALS['sugar_config']['cache_dir']. 'modules/unified_search_modules.php');
		}

    	if(file_exists('custom/modules/unified_search_modules_display.php'))
		{
			copy('custom/modules/unified_search_modules_display.php', 'custom/modules/unified_search_modules_display.php.bak');
			unlink('custom/modules/unified_search_modules_display.php');
		}
    }

    public function tearDown()
    {
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);

		if(file_exists($GLOBALS['sugar_config']['cache_dir']. 'modules/unified_search_modules.php.bak'))
		{
			copy($GLOBALS['sugar_config']['cache_dir']. 'modules/unified_search_modules.php.bak', $GLOBALS['sugar_config']['cache_dir']. 'modules/unified_search_modules.php');
			unlink($GLOBALS['sugar_config']['cache_dir']. 'modules/unified_search_modules.php.bak');
		}

    	if(file_exists('custom/modules/unified_search_modules_display.php.bak'))
		{
			copy('custom/modules/unified_search_modules_display.php.bak', 'custom/modules/unified_search_modules_display.php');
			unlink('custom/modules/unified_search_modules_display.php.bak');
		}

		SugarTestTaskUtilities::removeAllCreatedTasks();
		SugarTestAccountUtilities::removeAllCreatedAccounts();
    }

	public function testFisrtUnifiedSearchWithoutUserPreferences()
	{
		 //Enable the Tasks, Accounts and Contacts modules
    	 require_once('modules/Home/UnifiedSearchAdvanced.php');
    	 $_REQUEST = array();
    	 $_REQUEST['enabled_modules'] = 'Tasks,Accounts,Contacts';
    	 $unifiedSearchAdvanced = new UnifiedSearchAdvanced();
    	 $unifiedSearchAdvanced->saveGlobalSearchSettings();

    	 $_REQUEST = array();
    	 $_REQUEST['advanced'] = 'false';
    	 $unifiedSearchAdvanced->query_stirng = 'blah';

         $unifiedSearchAdvanced->search();
    	 global $current_user;
    	 $users_modules = $current_user->getPreference('globalSearch', 'search');
    	 $this->assertTrue(!empty($users_modules), 'Assert we have set the user preferences properly');
    	 $this->assertTrue(isset($users_modules['Tasks']), 'Assert that we have added the Tasks module');
    	 $this->assertEquals(count($users_modules), 3, 'Assert that we have 3 modules in user preferences for global search');
	}

}

?>