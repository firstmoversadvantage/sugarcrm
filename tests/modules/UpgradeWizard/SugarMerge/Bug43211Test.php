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

require_once 'include/dir_inc.php';

class Bug43211Test extends Sugar_PHPUnit_Framework_TestCase  {
	
var $merge;

function setUp() {
   SugarTestMergeUtilities::setupFiles(array('Leads', 'Accounts'), array('searchdefs'), 'tests/modules/UpgradeWizard/SugarMerge/metadata_files');
}


function tearDown() {
   SugarTestMergeUtilities::teardownFiles();
}

function test_leads_searchdefs_merge() {	
   require_once 'modules/UpgradeWizard/SugarMerge/SearchMerge.php';		
   $this->merge = new SearchMerge();
   $this->merge->merge('Leads', 'tests/modules/UpgradeWizard/SugarMerge/metadata_files/600/modules/Leads/metadata/searchdefs.php', 'modules/Leads/metadata/searchdefs.php', 'custom/modules/Leads/metadata/searchdefs.php');
   $this->assertTrue(file_exists('custom/modules/Leads/metadata/searchdefs.php.suback.php'));
   require('custom/modules/Leads/metadata/searchdefs.php');
   
   //Here's the main test... check to see that maxColumns has been changed to 3
   $this->assertEquals($searchdefs['Leads']['templateMeta']['maxColumns'], '3', 'Assert that maxColumns remains set to 3 for Leads module'); 
   $fields = array();
   foreach($searchdefs['Leads']['layout']['basic_search'] as $col_key=>$col) {
      	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
        if(!empty($id) && !is_array($id)) {
   	  	   $fields[$id] = $col;
   	  	}
   }
  
   $this->assertTrue(count($fields) == 3, "Assert that there are 3 fields in the basic_search layout for Leads metadata");
   $this->assertTrue(isset($fields['search_name']), "Assert that search_name field is present");
   $this->assertTrue(isset($fields['team_name']), "Assert that team_name field is present");
   $this->assertTrue(isset($fields['current_user_only']), "Assert that current_user_only field is present");
   $this->assertFalse(isset($fields['open_only']), "Assert that 620 OOTB open_only field is not added since there was a customization");
   
   $this->assertEquals($searchdefs['Leads']['templateMeta']['maxColumnsBasic'], $searchdefs['Leads']['templateMeta']['maxColumns'], 'Assert that maxColumnsBasic is set to value of maxColumns');   
}


function test_accounts_searchdefs_merge() {	
   require_once 'modules/UpgradeWizard/SugarMerge/SearchMerge.php';		
   $this->merge = new SearchMerge();
   $this->merge->merge('Accounts', 'tests/modules/UpgradeWizard/SugarMerge/metadata_files/600/modules/Accounts/metadata/searchdefs.php', 'modules/Accounts/metadata/searchdefs.php', 'custom/modules/Accounts/metadata/searchdefs.php');
   $this->assertTrue(file_exists('custom/modules/Accounts/metadata/searchdefs.php.suback.php'));
   require('custom/modules/Accounts/metadata/searchdefs.php');
   //echo var_export($searchdefs['Accounts'], true);
   
   //Here's the main test... check to see that maxColumns is still 3 since Accounts is not a module with maxColumn altered OOTB
   $this->assertEquals($searchdefs['Accounts']['templateMeta']['maxColumns'], '3', 'Assert that maxColumns is still 3 for Accounts module'); 
   $fields = array();
   foreach($searchdefs['Accounts']['layout']['basic_search'] as $col_key=>$col) {
      	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
        if(!empty($id) && !is_array($id)) {
   	  	   $fields[$id] = $col;
   	  	}
   }
  
   $this->assertTrue(count($fields) == 3, "Assert that there are 3 fields in the basic_search layout for Leads metadata");
   $this->assertTrue(isset($fields['name']), "Assert that name field is present");
   $this->assertTrue(isset($fields['created_by_name']), "Assert that created_by_name field is present");
   $this->assertTrue(isset($fields['current_user_only']), "Assert that current_user_only field is present");
   $this->assertFalse(isset($fields['open_only']), "Assert that 620 OOTB open_only field is not added since there was a customization");
   
   $this->assertEquals($searchdefs['Accounts']['templateMeta']['maxColumnsBasic'], $searchdefs['Accounts']['templateMeta']['maxColumns'], 'Assert that maxColumnsBasic is set to value of maxColumns');
}

}
?>