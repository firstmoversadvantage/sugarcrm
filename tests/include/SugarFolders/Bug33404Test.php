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

/**
 * @ticket 33404
 */
class Bug33404Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $folder = null;
    var $_user = null;
    
    
	public function setUp()
    {
        global $current_user, $currentModule;

        $this->_user = SugarTestUserUtilities::createAnonymousUser();
		$this->folder = new SugarFolder(); 
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        
        $GLOBALS['db']->query("DELETE FROM folders_subscriptions WHERE assigned_user_id='{$this->_user->id}'");
        
        unset($this->folder);
    }
    
	function testInsertFolderSubscription(){
	    global $current_user;
	   
	    $id1 = create_guid();
	    $id2 = create_guid();
	    
	    $this->folder->insertFolderSubscription($id1,$this->_user->id);
	    $this->folder->insertFolderSubscription($id2,$this->_user->id);
	    
	    $result = $GLOBALS['db']->query("SELECT count(*) as cnt FROM folders_subscriptions where assigned_user_id='{$this->_user->id}'");
		$rs = $GLOBALS['db']->fetchByAssoc($result);
		
		$this->assertEquals(2, $rs['cnt'], "Could not insert folder subscriptions properly" );
    }
    
    
    
    function testClearSubscriptionsForFolder()
    {
        global $current_user;
	   
        $random_user_id1 = create_guid();
        $random_user_id2 = create_guid();
        $random_user_id3 = create_guid();
        
	    $folderID = create_guid();
	    
	    $this->folder->insertFolderSubscription($folderID,$random_user_id1);
        $this->folder->insertFolderSubscription($folderID,$random_user_id2);
        $this->folder->insertFolderSubscription($folderID,$random_user_id3);
	    
        $result1 = $GLOBALS['db']->query("SELECT count(*) as cnt FROM folders_subscriptions where folder_id='{$folderID}' ");
		$rs1 = $GLOBALS['db']->fetchByAssoc($result1);
        $this->assertEquals(3, $rs1['cnt'], "Could not clear folder subscriptions, test setup failed while inserting folder subscriptionss");
        
        //Test deletion of subscriptions.
        $this->folder->clearSubscriptionsForFolder($folderID);
	    $result = $GLOBALS['db']->query("SELECT count(*) as cnt FROM folders_subscriptions where folder_id='{$folderID}' ");
		$rs = $GLOBALS['db']->fetchByAssoc($result);
	 
		$this->assertEquals(0, $rs['cnt'], "Could not clear folder subscriptions");
    }
}
?>