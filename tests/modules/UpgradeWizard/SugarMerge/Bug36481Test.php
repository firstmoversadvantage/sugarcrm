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

class Bug36481Test extends Sugar_PHPUnit_Framework_TestCase  {

var $ev_merge;
var $has_contacts_dir = false;
var $has_suback_file = false;

function setUp() {
   global $current_user;
   if(!isset($current_user)) {
   	  $current_user = SugarTestUserUtilities::createAnonymousUser();
   }
   if(!file_exists("custom/modules/Contacts/metadata")){
	  mkdir_recursive("custom/modules/Contacts/metadata", true);
   }
   
   if(file_exists('custom/modules/Contacts/metadata/editviewdefs.php')) {
   	  $this->has_contacts_dir = true;
   	  copy('custom/modules/Contacts/metadata/editviewdefs.php', 'custom/modules/Contacts/metadata/editviewdefs.php.bak');
   }
   
   $this->has_suback_file = file_exists('custom/modules/Contacts/metadata/editviewdefs.php.suback.php');
   
   copy('tests/modules/UpgradeWizard/SugarMerge/metadata_files/custom/modules/Contacts/metadata/editviewdefs.php', 'custom/modules/Contacts/metadata/editviewdefs.php');
}

function tearDown() {
	return;
   if(!$this->has_contacts_dir) {
   	  rmdir_recursive('custom/modules/Contacts');
   }  else if(file_exists('custom/modules/Contacts/metadata/editviewdefs.php.bak')) {
   	  copy('custom/modules/Contacts/metadata/editviewdefs.php.bak', 'custom/modules/Contacts/metadata/editviewdefs.php');
      unlink('custom/modules/Contacts/metadata/editviewdefs.php.bak');
      
      if(!$this->has_suback_file) {
   	     unlink('custom/modules/Contacts/metadata/editviewdefs.php.suback.php');
   	  }
   }
   

}

function test_contacts_editview_merge() {
   require_once('modules/UpgradeWizard/SugarMerge/EditViewMerge.php');	
   $this->ev_merge = new EditViewMerge();	
   $this->ev_merge->merge('Contacts', 'tests/modules/UpgradeWizard/SugarMerge/metadata_files/550/modules/Contacts/metadata/editviewdefs.php', 'modules/Contacts/metadata/editviewdefs.php', 'custom/modules/Contacts/metadata/editviewdefs.php');
   $this->assertTrue(file_exists('custom/modules/Contacts/metadata/editviewdefs.php.suback.php'));
   require('custom/modules/Contacts/metadata/editviewdefs.php');
   $fields = array();
   foreach($viewdefs['Contacts']['EditView']['panels'] as $panel) {
   	  foreach($panel as $row) {
   	  	 foreach($row as $col_key=>$col) {
   	  	 	$id = is_array($col) && isset($col['name']) ? $col['name'] : $col;
   	  	 	$fields[$id] = $col;
   	  	 }
   	  }
   }
   
   $this->assertTrue(isset($fields['test_c']), 'Assert that test_c custom field exists');
}


}

?>