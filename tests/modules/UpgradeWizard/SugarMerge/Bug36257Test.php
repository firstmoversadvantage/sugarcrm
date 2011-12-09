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

class Bug36257Test extends Sugar_PHPUnit_Framework_TestCase  {


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
	   
	   $files = array('editviewdefs');
	   foreach($files as $file) {
	   	   if(file_exists("custom/modules/{$module}/metadata/{$file}")) {
		   	  copy("custom/modules/{$module}/metadata/{$file}.php", "custom/modules/{$module}/metadata/{$file}.php.bak");
		   }
		   
		   if(file_exists("custom/modules/{$module}/metadata/{$file}.php.suback.php")) {
		      copy("custom/modules/{$module}/metadata/{$file}.php.suback.php", "custom/modules/{$module}/metadata/{$file}.php.suback.bak");
		   }
		   
		   if(file_exists("tests/modules/UpgradeWizard/SugarMerge/metadata_files/custom/modules/{$module}/metadata/{$file}.php")) {
		   	  copy("tests/modules/UpgradeWizard/SugarMerge/metadata_files/custom/modules/{$module}/metadata/{$file}.php", "custom/modules/{$module}/metadata/{$file}.php");
		   }
	   } //foreach
   } //foreach
}


function tearDown() {

   foreach($this->modules as $module) {
	   if(!$this->has_dir[$module]) {
	   	  rmdir_recursive("custom/modules/{$module}");
	   }  else {
	   	   $files = array('editviewdefs');
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


function test_textarea_fields_on_contacts_editview_merge() {		
   require_once 'modules/UpgradeWizard/SugarMerge/EditViewMerge.php';
   $this->merge = new EditViewMerge();	
   $this->merge->merge('Contacts', 'tests/modules/UpgradeWizard/SugarMerge/metadata_files/551/modules/Contacts/metadata/editviewdefs.php', 'modules/Contacts/metadata/editviewdefs.php', 'custom/modules/Contacts/metadata/editviewdefs.php');
   $this->assertTrue(file_exists('custom/modules/Contacts/metadata/editviewdefs.php.suback.php'));
   require('custom/modules/Contacts/metadata/editviewdefs.php');
   $fields = array();
   $panels = array();
   
   foreach($viewdefs['Contacts']['EditView']['panels'] as $panel_key=>$panel) {
   	  $panels[$panel_key] = $panel_key;
   	  foreach($panel as $row) {
   	  	 foreach($row as $col_key=>$col) {
   	  	 	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
   	  	 	if(!empty($id) && !is_array($id)) {
   	  	 	   $fields[$id] = $col;
   	  	 	}
   	  	 } //foreach
   	  } //foreach
   } //foreach
   
   $this->assertTrue(!isset($fields['description']['displayParams']), 'Assert that a regular description field has displayParams attribute removed');
   $this->assertTrue(isset($fields['custom_description_c']['displayParams']), 'Assert that a customized description field has displayParams attribute preserved');
   $this->assertTrue($fields['custom_description_c']['displayParams']['rows'] == 10, 'Assert that the displayParams->rows attribute is 10');
   $this->assertTrue($fields['custom_description_c']['displayParams']['cols'] == 100, 'Assert that the displayParams->cols attribute is 100');
   
}

}
?>