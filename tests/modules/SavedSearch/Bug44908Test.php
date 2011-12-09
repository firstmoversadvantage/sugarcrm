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

require_once('modules/MySettings/StoreQuery.php');

class Bug44908Test extends Sugar_PHPUnit_Framework_TestCase 
{
	
    public function testAdvancedSearchWithCommaSeparatedBugNumbers()
    {
    	$_REQUEST = array();
    	$storeQuery = new StoreQuery();
	    $query['action'] = 'index';
	    $query['module'] = 'Bugs';
	    $query['orderBy'] = 'BUG_NUMBER';
	    $query['sortOrder'] = 'ASC';
	    $query['query'] = 'true';
	    $query['searchFormTab'] = 'advanced_search';
	    $query['showSSDIV'] = 'no';
	    $query['bug_number_advanced'] = '1,2,3,4,5';
	    $query['name_advanced'] = '';
	    $query['status_advanced'][] = 'Assigned';
	    $query['favorites_only_advanced'] = '0';
	    $query['search_module'] = 'Bug';
	    $query['saved_search_action'] = 'save';
	    $query['displayColumns'] = 'BUG_NUMBER|NAME|STATUS|TYPE|PRIORITY|FIXED_IN_RELEASE_NAME|ASSIGNED_USER_NAME';
    	$storeQuery->query = $query;
    	$storeQuery->populateRequest();
    	$this->assertEquals('1,2,3,4,5', $_REQUEST['bug_number_advanced'], "Assert that bug search string 1,2,3,4,5 was not formatted");
    }
    
}