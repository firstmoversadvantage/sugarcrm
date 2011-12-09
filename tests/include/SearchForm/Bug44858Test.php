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


require_once "modules/Accounts/Account.php";
require_once "include/Popups/PopupSmarty.php";
require_once "include/SearchForm/SearchForm2.php";

class Bug44858Test extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
	{
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        //$this->useOutputBuffering = true;
	}

	public function tearDown()
	{
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        SugarTestAccountUtilities::removeAllCreatedAccounts();
	}
    
    /**
     * @ticket 44858
     */
    public function testGeneratedWhereClauseDoesNotHaveValueOfFieldNotSetInSearchForm()
    {
        //test to check that if value of a dropdown field is already set in REQUEST object (from any form such as mass update form instead of search form)
        //i.e. search is made on empty string, but REQUEST object gets value of that dropdown field from some other form on the same page
        //then on clicking serach button, value of that field should not be used as filter in where clause
        $this->markTestSkipped('This test should actually check that the $whereArray is indeed populated');
        return;
        
    	//array to simulate REQUEST object
    	$requestArray['module'] = 'Accounts';
    	$requestArray['action'] = 'index';
    	$requestArray['searchFormTab'] = 'basic_search';
    	$requestArray['account_type'] = 'Analyst'; //value of a dropdown field set in REQUEST object
    	$requestArray['query'] = 'true';
    	$requestArray['button']  = 'Search';
    	$requestArray['globalLinksOpen']='true';
    	$requestArray['current_user_only_basic'] = 0;
    	
    	$account = SugarTestAccountUtilities::createAccount();
    	$searchForm = new SearchForm($account,'Accounts');
    	
    	require 'modules/Accounts/vardefs.php';
    	require 'modules/Accounts/metadata/SearchFields.php';
    	require 'modules/Accounts/metadata/searchdefs.php';
        $searchForm->searchFields = $searchFields[$searchForm->module]; 
        $searchForm->searchdefs = $searchdefs[$searchForm->module];                          
    	$searchForm->populateFromArray($requestArray,'basic_search',false);
    	$whereArray = $searchForm->generateSearchWhere(true, $account->module_dir);
    	//echo var_export($whereArray, true);
    	$this->assertEquals(0, count($whereArray));

    }
}
