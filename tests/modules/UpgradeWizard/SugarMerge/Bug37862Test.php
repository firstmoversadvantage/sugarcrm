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

class Bug37862Test extends Sugar_PHPUnit_Framework_TestCase  {

var $merge;
var $has_dir;
var $modules;

function setUp() {
   $this->modules = array('Contacts');
   $this->has_dir = array();
   
   foreach($this->modules as $module) {
	   if(!file_exists("custom/modules/{$module}/metadata")){
		  mkdir_recursive("custom/modules/{$module}/metadata", true);
	   }
	   
	   if(file_exists("custom/modules/{$module}")) {
	   	  $this->has_dir[$module] = true;
	   }
	   
	   $files = array('searchdefs');
	   foreach($files as $file) {
	   	   if(file_exists("custom/modules/{$module}/metadata/{$file}")) {
		   	  copy("custom/modules/{$module}/metadata/{$file}.php", "custom/modules/{$module}/metadata/{$file}.php.bak");
		   }
		   
		   if(file_exists("custom/modules/{$module}/metadata/{$file}.php.suback.php")) {
		      copy("custom/modules/{$module}/metadata/{$file}.php.suback.php", "custom/modules/{$module}/metadata/{$file}.php.suback.bak");
		   }
		   
		   if(file_exists("tests/modules/UpgradeWizard/SugarMerge/od_metadata_files/custom/modules/{$module}/metadata/{$file}.php")) {
		   	  copy("tests/modules/UpgradeWizard/SugarMerge/od_metadata_files/custom/modules/{$module}/metadata/{$file}.php", "custom/modules/{$module}/metadata/{$file}.php");
		   }
	   } //foreach
   } //foreach
}


function tearDown() {

   foreach($this->modules as $module) {
	   if(!$this->has_dir[$module]) {
	   	  rmdir_recursive("custom/modules/{$module}");
	   }  else {
	   	   $files = array('searchdefs');
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


function test_contacts_searchdefs_merge() {	
   require('custom/modules/Contacts/metadata/searchdefs.php');
   $original_basic_fields = array();

   //echo var_export($searchdefs, true);
   foreach($searchdefs['Contacts']['layout']['basic_search'] as $col_key=>$col) {
      	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
      	if(!empty($id) && !is_array($id)) {
   	  	   $original_basic_fields[$id] = $col;
   	  	}
   }
   
   $original_advanced_fields = array();
   foreach($searchdefs['Contacts']['layout']['advanced_search'] as $col_key=>$col) {
      	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
      	if(!empty($id)) {
      		$original_advanced_fields[$id] = $col;
      	}
   }   
   
	
   require_once 'modules/UpgradeWizard/SugarMerge/SearchMerge.php';		
   $this->merge = new SearchMerge();	
   $this->merge->merge('Contacts', 'tests/modules/UpgradeWizard/SugarMerge/metadata_files/551/modules/Contacts/metadata/searchdefs.php', 'modules/Contacts/metadata/searchdefs.php', 'custom/modules/Contacts/metadata/searchdefs.php');
   $this->assertTrue(file_exists('custom/modules/Contacts/metadata/searchdefs.php.suback.php'));
   require('custom/modules/Contacts/metadata/searchdefs.php');
   $fields = array();

   //echo var_export($searchdefs, true);
   foreach($searchdefs['Contacts']['layout']['basic_search'] as $col_key=>$col) {
      	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
      	if(!empty($id) && !is_array($id)) {
   	  	   $fields[$id] = $col;
   	  	}
   }
   
   //echo var_export($original_basic_fields, true);
   //echo var_export($fields, true);
   //echo var_export(array_diff(array_keys($fields), array_keys($original_basic_fields)), true);
   
   
   $this->assertTrue(count($fields) == 5, "Assert that there are 5 fields in the basic_search layout for Contacts metadata");
   $this->assertTrue(count(array_diff(array_keys($fields), array_keys($original_basic_fields))) == 0, 'Assert that there is no difference between original basic search and merged basic search Array');
   
  
   $fields = array();
   foreach($searchdefs['Contacts']['layout']['advanced_search'] as $col_key=>$col) {
      	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
      	if(!empty($id)) {
      		$fields[$id] = $col;
      	}
   }
   
   $this->assertTrue(count($fields) == 16, "Assert that there are 18 fields in the advanced_search layout for Contacts metadata");
   $this->assertTrue(count(array_diff(array_keys($fields), array_keys($original_advanced_fields))) == 0, 'Assert that there is no difference between original advanced search and merged advanced search Array');
}


}
?>