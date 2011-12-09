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

class Bug37461Test extends Sugar_PHPUnit_Framework_TestCase  {

var $merge;
var $has_dir;
var $modules;

function setUp() {
   $this->modules = array('Accounts');
   $this->has_dir = array();
   
   foreach($this->modules as $module) {
	   if(!file_exists("custom/modules/{$module}/metadata")){
		  mkdir_recursive("custom/modules/{$module}/metadata", true);
	   }
	   
	   if(file_exists("custom/modules/{$module}")) {
	   	  $this->has_dir[$module] = true;
	   }
	   
	   $files = array('searchdefs', 'listviewdefs');
	   foreach($files as $file) {
	   	   if(file_exists("custom/modules/{$module}/metadata/{$file}")) {
		   	  copy("custom/modules/{$module}/metadata/{$file}.php", "custom/modules/{$module}/metadata/{$file}.php.bak");
		   }
		   
		   if(file_exists("custom/modules/{$module}/metadata/{$file}.php.suback.php")) {
		      copy("custom/modules/{$module}/metadata/{$file}.php.suback.php", "custom/modules/{$module}/metadata/{$file}.php.suback.bak");
		   }
		   
		   if(file_exists("tests/modules/UpgradeWizard/SugarMerge/siupgrade_metadata_files/custom/modules/{$module}/metadata/{$file}.php")) {
		   	  copy("tests/modules/UpgradeWizard/SugarMerge/siupgrade_metadata_files/custom/modules/{$module}/metadata/{$file}.php", "custom/modules/{$module}/metadata/{$file}.php");
		   }
	   } //foreach
   } //foreach
}


function tearDown() {

   foreach($this->modules as $module) {
	   if(!$this->has_dir[$module]) {
	   	  rmdir_recursive("custom/modules/{$module}");
	   }  else {
	   	   $files = array('searchdefs', 'listviewdefs');
		   foreach($files as $file) {
		      if(file_exists("custom/modules/{$module}/metadata/{$file}.php.bak")) {
		      	 copy("custom/modules/{$module}/metadata/{$file}.php.bak", "custom/modules/{$module}/metadata/{$file}.php");
	             unlink("custom/modules/{$module}/metadata/{$file}.php.bak");
		      } else if(file_exists("custom/modules/{$module}/metadata/{$file}.php")) {
		      	 unlink("custom/modules/{$module}/metadata/{$file}.php");
		      }
		      
		   	  if(file_exists("custom/modules/{$module}/metadata/{$module}.php.suback.bak")) {
		      	 copy("custom/modules/{$module}/metadata/{$file}.php.suback.bak", "custom/modules/{$module}/metadata/{$file}.php.suback.php");
	             unlink("custom/modules/{$module}/metadata/{$file}.php.suback.bak");
		      } else if(file_exists("custom/modules/{$module}/metadata/{$file}.php.suback.php")) {
		      	 unlink("custom/modules/{$module}/metadata/{$file}.php.suback.php");
		      }  
		   }
	   }
   } //foreach
}


function test_accounts_searchdefs_merge() {	
   require_once 'modules/UpgradeWizard/SugarMerge/SearchMerge.php';		
   $this->merge = new SearchMerge();	
   $this->merge->merge('Accounts', 'tests/modules/UpgradeWizard/SugarMerge/siupgrade_metadata_files/551/modules/Accounts/metadata/searchdefs.php', 'modules/Accounts/metadata/searchdefs.php', 'custom/modules/Accounts/metadata/searchdefs.php');
   $this->assertTrue(file_exists('custom/modules/Accounts/metadata/searchdefs.php.suback.php'));
   require('custom/modules/Accounts/metadata/searchdefs.php');
   $fields = array();
   
   //echo var_export($searchdefs, true);
   
   foreach($searchdefs['Accounts']['layout']['basic_search'] as $col_key=>$col) {
      	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
      	if(!empty($id) && !is_array($id)) {
   	  	   $fields[$id] = $col;
   	  	}
   }
   
   
   $this->assertTrue(count($fields) == 6, "Assert that there are 6 fields in the basic_search layout for Accounts metadata");
   
   $fields = array();
   foreach($searchdefs['Accounts']['layout']['advanced_search'] as $col_key=>$col) {
      	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
      	if(!empty($id)) {
      		$fields[$id] = $col;
      	}
   }
   $this->assertTrue(count($fields) == 18, "Assert that there are 18 fields in the advanced_search layout for Accounts metadata");
}


function test_accounts_listviewdefs_merge() {	
   require('custom/modules/Accounts/metadata/listviewdefs.php');
   $original_fields = array();
   $original_displayed_fields = array();
   foreach($listViewDefs['Accounts'] as $col_key=>$col) {
   	  	$original_fields[$col_key] = $col;
   	  	if(isset($col['default']) && $col['default']) {
   	  	   $original_displayed_fields[$col_key] = $col;
   	  	}
   }
	
   //echo var_export($original_displayed_fields, true);
   
   require_once 'modules/UpgradeWizard/SugarMerge/ListViewMerge.php';		
   $this->merge = new ListViewMerge();	
   $this->merge->merge('Accounts', 'tests/modules/UpgradeWizard/SugarMerge/siupgrade_metadata_files/551/modules/Accounts/metadata/listviewdefs.php', 'modules/Accounts/metadata/listviewdefs.php', 'custom/modules/Accounts/metadata/listviewdefs.php');
   $this->assertTrue(file_exists('custom/modules/Accounts/metadata/listviewdefs.php.suback.php'));
   require('custom/modules/Accounts/metadata/listviewdefs.php');
   $fields = array();
   $displayed_fields = array();
   foreach($listViewDefs['Accounts'] as $col_key=>$col) {
   	  	$fields[$col_key] = $col;
   	  	if(isset($col['default']) && $col['default']) {
   	  	   $displayed_fields[$col_key] = $col;
   	  	}
   }
  
   //echo var_export($displayed_fields, true);
   //echo var_export($listViewDefs['Accounts'], true);
   
   $this->assertTrue(count($displayed_fields) == count($original_displayed_fields), "Assert that there are the same number of fields displayed in the listview layout for Accounts metadata");
   $this->assertTrue(isset($displayed_fields['NAME']), "Assert that NAME field is present");
   $this->assertTrue(isset($displayed_fields['BILLING_ADDRESS_CITY']), "Assert that BILLING_ADDRESS_CITY field is present");
   $this->assertTrue(isset($displayed_fields['BILLING_ADDRESS_STATE']), "Assert that BILLING_ADDRESS_CITY field is present");
   $this->assertTrue(isset($displayed_fields['TEAM_NAME']), "Assert that TEAM_NAME (removed in 6.0 OOTB) field is present");
   $this->assertTrue(!isset($displayed_fields['BILLING_ADDRESS_COUNTRY']), "Assert that BILLING_ADDRESS_COUNTRY (added in 6.0 OOTB) field is not present");
}


}
?>