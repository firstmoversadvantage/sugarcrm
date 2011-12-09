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

require_once 'modules/UpgradeWizard/SugarMerge/EditViewMerge.php';
require_once 'include/dir_inc.php';

class Bug44472Test extends Sugar_PHPUnit_Framework_TestCase  {

function setUp() {
   SugarTestMergeUtilities::setupFiles(array('Cases'), array('editviewdefs'), 'tests/modules/UpgradeWizard/SugarMerge/od_metadata_files/610');
   $this->useOutputBuffering = false;
}


function tearDown() {
   SugarTestMergeUtilities::teardownFiles();
}


function test620TemplateMetaMergeOnCases() 
{		
   require_once 'modules/UpgradeWizard/SugarMerge/EditViewMerge.php';
   $this->merge = new EditViewMerge();	
   $this->merge->merge('Cases', 'tests/modules/UpgradeWizard/SugarMerge/od_metadata_files/610/oob/modules/Cases/metadata/editviewdefs.php', 'modules/Cases/metadata/editviewdefs.php', 'custom/modules/Cases/metadata/editviewdefs.php');
   $this->assertTrue(file_exists('custom/modules/Cases/metadata/editviewdefs.php.suback.php'));
   require('custom/modules/Cases/metadata/editviewdefs.php');
   $this->assertTrue(isset($viewdefs['Cases']['EditView']['templateMeta']['form']), 'Assert that the form key is kept on the customized templateMeta section for Cases');
}

function test620TemplateMetaMergeOnMeetings() 
{		
   require_once 'modules/UpgradeWizard/SugarMerge/EditViewMerge.php';
   $this->merge = new EditViewMergeMock();	
   $this->merge->setModule('Meetings');
   $data = array();
   $data['Meetings'] = array('EditView'=>array('templateMeta'=>array('form')));
   $this->merge->setCustomData($data);
   $newData = array();
   $newData['Meetings'] = array('EditView'=>array('templateMeta'=>array()));
   $this->merge->setNewData($newData);
   $this->merge->testMergeTemplateMeta();
   $newData = $this->merge->getNewData();   
   $this->assertTrue(!isset($newData['Meetings']['EditView']['templateMeta']['form']), 'Assert that we do not take customized templateMeta section for Meetings');
}

function test620TemplateMetaMergeOnCalls() 
{		
   require_once 'modules/UpgradeWizard/SugarMerge/EditViewMerge.php';
   $this->merge = new EditViewMergeMock();	
   $this->merge->setModule('Calls');
   $data = array();
   $data['Calls'] = array('EditView'=>array('templateMeta'=>array('form')));
   $this->merge->setCustomData($data);   
   $newData = array();
   $newData['Calls'] = array('EditView'=>array('templateMeta'=>array()));
   $this->merge->setNewData($newData);
   $this->merge->testMergeTemplateMeta();
   
   $newData = $this->merge->getNewData();
   $this->assertTrue(!isset($newData['Calls']['EditView']['templateMeta']['form']), 'Assert that we do not take customized templateMeta section for Calls');
}

}

class EditViewMergeMock extends EditViewMerge
{
    function setModule($module)
    {
    	$this->module = $module;
    }
    
    function setCustomData($data)
    {
        $this->customData = $data;	
    }
    
    function setNewData($data)
    {
    	$this->newData = $data;
    }
    
    function getNewData()
    {
    	return $this->newData;
    }
    
    function testMergeTemplateMeta()
    {
    	$this->mergeTemplateMeta();
    }
}

?>