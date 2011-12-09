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

 
require_once 'modules/Home/UnifiedSearchAdvanced.php';
require_once 'modules/Contacts/Contact.php';
require_once 'include/utils/layout_utils.php';

/**
 * @ticket 34125
 */
class UnifiedSearchAdvancedTest extends Sugar_PHPUnit_Framework_OutputTestCase
{
    protected $_contact = null;
    private $_hasUnifiedSearchModulesConfig = false;
    private $_hasUnifiedSearchModulesDisplay = false;
    
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $unid = uniqid();
        $contact = new Contact();
        $contact->id = 'l_'.$unid;
        $contact->first_name = 'Greg';
        $contact->last_name = 'Brady';
        $contact->new_with_id = true;
        $contact->save();
        $this->_contact = $contact;
        
        if(file_exists('cache/modules/unified_search_modules.php'))
        {
        	$this->_hasUnifiedSearchModulesConfig = true;
        	copy('cache/modules/unified_search_modules.php', 'cache/modules/unified_search_modules.php.bak');
        	unlink('cache/modules/unified_search_modules.php');
        }

        if(file_exists('custom/modules/unified_search_modules_display.php'))
        {
        	$this->_hasUnifiedSearchModulesDisplay = true;
        	copy('custom/modules/unified_search_modules_display.php', 'custom/modules/unified_search_modules_display.php.bak');
        	unlink('custom/modules/unified_search_modules_display.php');
        }        
        
	}

	public function tearDown()
	{
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->_contact->id}'");
        unset($this->_contact);
        
        if($this->_hasUnifiedSearchModulesConfig)
        {
        	copy('cache/modules/unified_search_modules.php.bak', 'cache/modules/unified_search_modules.php');
        	unlink('cache/modules/unified_search_modules.php.bak');
        } else {
        	unlink('cache/modules/unified_search_modules.php');
        }
        
        if($this->_hasUnifiedSearchModulesDisplay)
        {
        	copy('custom/modules/unified_search_modules_display.php.bak', 'custom/modules/unified_search_modules_display.php');
        	unlink('custom/modules/unified_search_modules_display.php.bak');
        } else {
        	unlink('custom/modules/unified_search_modules_display.php');
        }
	}

	public function testSearchByFirstName()
	{
		global $mod_strings, $modListHeader, $app_strings, $beanList, $beanFiles;
		require('config.php');
		require('include/modules.php');
		$modListHeader = $moduleList;
    	$_REQUEST['query_string'] = $this->_contact->first_name;
    	$_REQUEST['module'] = 'Home';
		$usa = new UnifiedSearchAdvanced();
		$usa->search();
		$this->expectOutputRegex("/{$this->_contact->first_name}/");
    }

	public function testSearchByFirstAndLastName()
	{
		global $mod_strings, $modListHeader, $app_strings, $beanList, $beanFiles;
		require('config.php');
		require('include/modules.php');
		$_REQUEST['query_string'] = $this->_contact->first_name.' '.$this->_contact->last_name;
    	$_REQUEST['module'] = 'Home';
		$usa = new UnifiedSearchAdvanced();
		$usa->search();
		$this->expectOutputRegex("/{$this->_contact->first_name}/");
    }
    
    public function testUserPreferencesSearch()
    {
		global $mod_strings, $modListHeader, $app_strings, $beanList, $beanFiles;
		require('config.php');
		require('include/modules.php');
  	
    	$usa = new UnifiedSearchAdvanced();
    	$_REQUEST['enabled_modules'] = 'Accounts,Contacts';
    	$usa->saveGlobalSearchSettings();
    	
    	$_REQUEST = array();
		$_REQUEST['query_string'] = $this->_contact->first_name.' '.$this->_contact->last_name;
    	$_REQUEST['module'] = 'Home';      	
    	$usa->search();
    	global $current_user;
    	$modules = $current_user->getPreference('globalSearch', 'search');
    	$this->assertEquals(count($modules), 2, 'Assert that there are two modules in the user preferences as defined from the global search');
    	$this->assertTrue(isset($modules['Accounts']) && isset($modules['Contacts']), 'Assert that the Accounts and Contacts modules have been added');    	
    }
}

