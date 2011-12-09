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

 
require_once('include/OutboundEmail/OutboundEmail.php');

/**
 * @ticket 23140
 */
class Bug36329Test extends Sugar_PHPUnit_Framework_OutputTestCase
{
	var $save_query;
	var $current_language;

	public function setUp()
	{
		global $sugar_config;
		$this->save_query = isset($sugar_config['save_query']) ? true : false;
		$this->current_language = $GLOBALS['current_language'];

		global $current_user;
		$current_user = new User();
		$current_user->retrieve('1');

		global $mod_strings, $app_strings;
		$mod_strings = return_module_language('en_us', 'Accounts');
		$app_strings = return_application_language('en_us');

		$beanList = array();
		$beanFiles = array();
		require('include/modules.php');
		$GLOBALS['beanList'] = $beanList;
		$GLOBALS['beanFiles'] = $beanFiles;

		require('sugar_version.php');
		$GLOBALS['sugar_version'] = $sugar_version;
	}

	public function tearDown()
	{
	    global $sugar_config;
		if(!$this->save_query) {
		   unset($sugar_config['save_query']);
		}

		$GLOBALS['current_language'] = $this->current_language;
		//unset($GLOBALS['mod_strings']);
		//unset($GLOBALS['app_strings']);
		//unset($GLOBALS['beanList']);
		//unset($GLOBALS['beanFiles']);
	}

    public function test_populate_only_no_query()
    {
    	$GLOBALS['sugar_config']['save_query'] = 'populate_only';
    	$_REQUEST['module'] = 'Accounts';
    	$_REQUEST['action'] = 'Popup';
    	$_REQUEST['mode'] = 'single';
    	$_REQUEST['create'] = 'true';
    	$_REQUEST['metadata'] = 'undefined';
    	require_once('include/MVC/View/SugarView.php');
    	require_once('include/MVC/View/views/view.popup.php');
    	require_once('include/utils/layout_utils.php');
    	$popup = new ViewPopup();
    	$popup->module = 'Accounts';
    	require_once('modules/Accounts/Account.php');
    	$popup->bean = new account();
    	$this->expectOutputRegex("/Perform a search using the search form above/");
    	$popup->display();
    }


    public function test_populate_only_with_query()
    {
    	$GLOBALS['sugar_config']['save_query'] = 'populate_only';
    	global $app_strings;
    	$_REQUEST['module'] = 'Accounts';
    	$_REQUEST['action'] = 'Popup';
    	$_REQUEST['mode'] = 'single';
    	$_REQUEST['create'] = 'true';
    	$_REQUEST['metadata'] = 'undefined';
    	$_REQUEST['name_advanced'] = 'Test';
    	$_REQUEST['query'] = 'true';
    	require_once('include/MVC/View/SugarView.php');
    	require_once('include/MVC/View/views/view.popup.php');
    	require_once('include/utils/layout_utils.php');
    	$popup = new ViewPopup();
    	$popup->module = 'Accounts';
    	require_once('modules/Accounts/Account.php');
    	$popup->bean = new account();
    	// Negative regexp
    	$this->expectOutputNotRegex('/Perform a search using the search form above/');
    	$popup->display();
    }
}
