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

 
require_once('include/SugarFolders/SugarFolders.php');
require_once('modules/InboundEmail/InboundEmail.php');

/**
 * @ticket 33404
 */
class AutoCreateImportFolderTest extends Sugar_PHPUnit_Framework_TestCase
{
	var $folder_id = null;
	var $folder_obj = null;
	var $ie = null;
    var $_user = null;
    
    
	public function setUp()
    {
        global $current_user, $currentModule;

        $this->_user = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['current_user'] = $this->_user;
        
		$this->folder = new SugarFolder(); 
		$this->ie = new InboundEmail();
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        
        $GLOBALS['db']->query("DELETE FROM folders WHERE id='{$this->folder_id}'");
        
        unset($this->ie);
    }
    
	function testAutoImportFolderCreation(){
	    global $current_user;
	   
    	$this->ie->name = "Sugar Test";
    	$this->folder_id = $this->ie->createAutoImportSugarFolder();
	    $this->folder_obj = new SugarFolder();
	    $this->folder_obj->retrieve($this->folder_id);
		
		$this->assertEquals($this->ie->name, $this->folder_obj->name, "Could not create folder for Inbound Email auto folder creation" );
    	$this->assertEquals(0, $this->folder_obj->has_child, "Could not create folder for Inbound Email auto folder creation" );
        $this->assertEquals(1, $this->folder_obj->is_group, "Could not create folder for Inbound Email auto folder creation" );
        $this->assertEquals($this->_user->id, $this->folder_obj->assign_to_id, "Could not create folder for Inbound Email auto folder creation" );
        
	}
}
?>