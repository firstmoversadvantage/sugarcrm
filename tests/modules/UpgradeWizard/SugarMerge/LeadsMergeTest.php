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

class LeadsMergeTest extends Sugar_PHPUnit_Framework_TestCase  {

var $merge;
var $has_dir;
var $modules;

function setUp() {
   $this->modules = array('Leads');
   $this->has_dir = array();
   
   foreach($this->modules as $module) {
	   if(!file_exists("custom/modules/{$module}/metadata")){
		  mkdir_recursive("custom/modules/{$module}/metadata", true);
	   }
	   
	   if(file_exists("custom/modules/{$module}")) {
	   	  $this->has_dir[$module] = true;
	   }
	   
	   $files = array('detailviewdefs', 'editviewdefs');
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
	   	   $files = array('detailviewdefs', 'editviewdefs');
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

/*
function test_600_leads_detailview_merge() {		
   $this->assertTrue(file_exists('custom/modules/Leads/metadata/detailviewdefs.php'));	
   require('custom/modules/Leads/metadata/detailviewdefs.php');
   $pre_upgrade_fields = array();
   $pre_upgrade_panels = array();
   foreach($viewdefs['Leads']['DetailView']['panels'] as $panel_key=>$panel) {
   	  $pre_upgrade_panels[$panel_key] = $panel_key;
   	  foreach($panel as $row) {
   	  	 foreach($row as $col_key=>$col) {
   	  	 	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
   	  	 	if(!empty($id) && !is_array($id)) {
   	  	 	   $pre_upgrade_fields[$id] = $col;
   	  	 	}
   	  	 }
   	  }
   } 	
	
   require_once('modules/UpgradeWizard/SugarMerge/DetailViewMerge.php');
   $this->merge = new DetailViewMerge();	
   $this->merge->merge('Leads', 'tests/modules/UpgradeWizard/SugarMerge/metadata_files/551/modules/Leads/metadata/detailviewdefs.php', 'modules/Leads/metadata/detailviewdefs.php', 'custom/modules/Leads/metadata/detailviewdefs.php');
   $this->assertTrue(file_exists('custom/modules/Leads/metadata/detailviewdefs.php.suback.php'));
   require('custom/modules/Leads/metadata/detailviewdefs.php');
   $fields = array();
   $new_fields = array();
   foreach($viewdefs['Leads']['DetailView']['panels'] as $panel) {
   	  foreach($panel as $row) {
   	  	 foreach($row as $col_key=>$col) {
   	  	 	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
   	  	 	$fields[$id] = $col;
   	  	    if(!empty($id) && !isset($pre_upgrade_fields[$id])) {
   	  	 	   $new_fields[$id] = $id;
   	  	 	}   	  	 	
   	  	 }
   	  }
   }
   
   //echo var_export($new_fields, true);
   //echo var_export($viewdefs['Leads']['DetailView']['panels'], true);
   $this->assertTrue(count($new_fields) == 1 && isset($new_fields['website']), 'Assert that website was the only field added');
   $this->assertTrue(isset($fields['website']), 'Assert that website field was added');
   
   $panel_keys = array_keys($viewdefs['Leads']['DetailView']['panels']);
   $end_key = end($panel_keys);
   
   $end_row = end(array_keys($viewdefs['Leads']['DetailView']['panels'][$end_key]));
   $this->assertTrue($viewdefs['Leads']['DetailView']['panels'][$end_key][$end_row][0] == 'website', 'Assert that website field was added to new space in new row');
}
*/

function test_600_leads_editview_merge() {		
	
   $this->assertTrue(file_exists('custom/modules/Leads/metadata/editviewdefs.php'));	
   require('custom/modules/Leads/metadata/editviewdefs.php');
   $pre_upgrade_fields = array();
   $pre_upgrade_panels = array();
   foreach($viewdefs['Leads']['EditView']['panels'] as $panel_key=>$panel) {
   	foreach($panel as $row) {  	 
	   	foreach($row as $col_key=>$col) {
	   	  	 	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
	   	  	 	if(!empty($id) && !is_array($id)) {
	   	  	 	   $pre_upgrade_fields[$id] = $col;
	   	  	 	}
	   	}
   	}
   } 	
	
   require_once('modules/UpgradeWizard/SugarMerge/EditViewMerge.php');
   $this->merge = new EditViewMerge();	
   $this->merge->merge('Leads', 'tests/modules/UpgradeWizard/SugarMerge/metadata_files/551/modules/Leads/metadata/editviewdefs.php', 'modules/Leads/metadata/editviewdefs.php', 'custom/modules/Leads/metadata/editviewdefs.php');
   $this->assertTrue(file_exists('custom/modules/Leads/metadata/editviewdefs.php.suback.php'));
   require('custom/modules/Leads/metadata/editviewdefs.php');
   $fields = array();
   $new_fields = array();
   foreach($viewdefs['Leads']['EditView']['panels'] as $panel) {
   	  foreach($panel as $row) {
   	  	 foreach($row as $col_key=>$col) {
   	  	 	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
   	  	 	
   	  	 	if(empty($id) || !is_string($id)) {
   	  	 	   continue;
   	  	 	}
   	  	 	
   	  	 	$fields[$id] = $col;
   	  	    if(!isset($pre_upgrade_fields[$id])) {
   	  	 	   $new_fields[$id] = $id;
   	  	 	}   	  	 	
   	  	 }
   	  }
   }
   
   //echo var_export($new_fields, true);
   //echo var_export($viewdefs['Leads']['EditView'], true);
   $this->assertTrue(count($new_fields) == 1 && isset($new_fields['website']), 'Assert that website was the only field added');
   $this->assertTrue(isset($fields['website']), 'Assert that website field was added');
   $end = end(array_keys($viewdefs['Leads']['EditView']['panels']['lbl_description_information']));
   $this->assertTrue(isset($viewdefs['Leads']['EditView']['panels']['lbl_description_information'][$end][0]) && ($viewdefs['Leads']['EditView']['panels']['lbl_description_information'][$end][0] == 'website'), 'Assert that website field was added to new space in row on lbl_description_information panel');
}


}

?>