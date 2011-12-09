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

class Bug39057Test extends Sugar_PHPUnit_Framework_TestCase  {

var $merge;

function setUp() {
   SugarTestMergeUtilities::setupFiles(array('Opportunities'), array('listviewdefs'), 'tests/modules/UpgradeWizard/SugarMerge/od_metadata_files');
}


function tearDown() {
   SugarTestMergeUtilities::teardownFiles();
}


function test_listviewdefs_merge() {			
   require('custom/modules/Opportunities/metadata/listviewdefs.php');
   $original_fields = array();
   $original_displayed_fields = array();
   foreach($listViewDefs['Opportunities'] as $col_key=>$col) {
   	  	$original_fields[$col_key] = $col;
   	  	if(isset($col['default']) && $col['default']) {
   	  	   $original_displayed_fields[$col_key] = $col;
   	  	}
   }

   require_once 'modules/UpgradeWizard/SugarMerge/ListViewMerge.php';		
   $this->merge = new ListViewMerge();	
   $this->merge->merge('Opportunities', 'tests/modules/UpgradeWizard/SugarMerge/od_metadata_files/554/modules/Opportunities/metadata/listviewdefs.php', 'modules/Opportunities/metadata/listviewdefs.php', 'custom/modules/Opportunities/metadata/listviewdefs.php');
   $this->assertTrue(file_exists('custom/modules/Opportunities/metadata/listviewdefs.php.suback.php'));
   require('custom/modules/Opportunities/metadata/listviewdefs.php');
   $fields = array();
   $displayed_fields = array();
   foreach($listViewDefs['Opportunities'] as $col_key=>$col) {
   	  	$fields[$col_key] = $col;
   	  	if(isset($col['default']) && $col['default']) {
   	  	   $displayed_fields[$col_key] = $col;
   	  	}
   } 
   
   //echo var_export($displayed_fields, true);
   
   $this->assertTrue(isset($original_displayed_fields['AMOUNT_USDOLLAR']['label']));
   $this->assertTrue(isset($displayed_fields['AMOUNT_USDOLLAR']['label']));
   //This tests to ensure that the label value is the same from the custom file even though in the new
   //file we changed the label value, we should preserve the custom value
   if(isset($original_displayed_fields['AMOUNT_USDOLLAR']['label']) && isset($displayed_fields['AMOUNT_USDOLLAR']['label']))
   {
   	  $this->assertNotEquals($original_displayed_fields['AMOUNT_USDOLLAR']['label'], $displayed_fields['AMOUNT_USDOLLAR']['label']);
   }
}


}
?>