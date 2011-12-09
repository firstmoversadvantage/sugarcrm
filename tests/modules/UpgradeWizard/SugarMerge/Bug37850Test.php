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

class Bug37850Test extends Sugar_PHPUnit_Framework_TestCase  {

var $merge;

function setUp() {
   SugarTestMergeUtilities::setupFiles(array('Leads'), array('detailviewdefs'), 'tests/modules/UpgradeWizard/SugarMerge/od_metadata_files');
}


function tearDown() {
   SugarTestMergeUtilities::teardownFiles();
}


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
	
   $this->assertTrue(isset($pre_upgrade_fields['created_by']), 'Assert that the created_by index existed in 551 metadata file');
   $this->assertTrue(!isset($pre_upgrade_fields['date_entered']), 'Assert that the date_entered did not exist in 551 metadata file');
   
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
   
   $this->assertTrue(!isset($new_fields['created_by']), 'Assert that the created_by index does not exists in the merged metadata file');
   $this->assertTrue(isset($new_fields['date_entered']), 'Assert that the date_entered field now exists in the merged metadata file');
}




}

?>