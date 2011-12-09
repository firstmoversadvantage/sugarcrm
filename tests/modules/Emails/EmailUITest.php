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

 
require_once('modules/Emails/EmailUI.php');

class EmailUITest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_folders = null;
    
    public function setUp()
    {
        global $current_user;
        $this->_user = SugarTestUserUtilities::createAnonymousUser();
        $this->_user->is_admin = 1;
        $GLOBALS['current_user'] = $this->_user;
        $this->eui = new EmailUIMock();

        $this->_folders = array();
		
		$beanList = array();
		$beanFiles = array();
		require('include/modules.php');
		$GLOBALS['beanList'] = $beanList;
		$GLOBALS['beanFiles'] = $beanFiles;
    }
    
    public function tearDown()
    {
        $GLOBALS['db']->query("DELETE FROM folders_subscriptions WHERE assigned_user_id='{$GLOBALS['current_user']->id}'");
        foreach ($this->_folders as $f) {
            $GLOBALS['db']->query("DELETE FROM folders_subscriptions WHERE folder_id='{$f}'");
            $GLOBALS['db']->query("DELETE FROM folders WHERE id='{$f}'");
        }
        
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        
        unset($GLOBALS['beanList']);
        unset($GLOBALS['beanFiles']);
        $GLOBALS['db']->query("DELETE FROM folders_subscriptions WHERE assigned_user_id='{$this->_user->id}'");
            
        foreach ($this->_folders as $f) {
            $GLOBALS['db']->query("DELETE FROM folders_subscriptions WHERE folder_id='{$f}'");
            $GLOBALS['db']->query("DELETE FROM folders WHERE id='{$f}'");
        }
    }

    /**
     * Save a SugarFolder 
     */
    public function testSaveNewFolder()
    {
        $newFolderName = "UNIT_TEST";
        $rs = $this->eui->saveNewFolder($newFolderName,'Home',0);
        $newFolderID = $rs['id'];
        $this->_folders[] = $newFolderID;
        
        $sf = new SugarFolder();
        $sf->retrieve($newFolderID);
        $this->assertEquals($newFolderName, $sf->name);
        
    }

    /**
     * Save the user preference for list view order per IE account.
     *
     */
    public function testSaveListViewSortOrder()
    {
        $tmpId = create_guid();
        $folderName = "UNIT_TEST";
        $sortBy = 'last_name';
        $dir = "DESC";
        $rs = $this->eui->saveListViewSortOrder($tmpId,$folderName,$sortBy,$dir);
        
        //Check against the saved preferences.
        $prefs = unserialize($GLOBALS['current_user']->getPreference('folderSortOrder', 'Emails'));
        $this->assertEquals($sortBy, $prefs[$tmpId][$folderName]['current']['sort']);
        $this->assertEquals($dir, $prefs[$tmpId][$folderName]['current']['direction']);
        
        
    }
    public function testGetRelatedEmail()
    {
    	
    	$account = new Account();
    	$account->name = "emailTestAccount";
    	$account->save(false);

    	$relatedBeanInfo = array('related_bean_id' => $account->id,  "related_bean_type" => "Accounts");
    	
    	//First pass should return a blank query as are no related items
    	$qArray = $this->eui->getRelatedEmail("LBL_DROPDOWN_LIST_ALL", array(), $relatedBeanInfo);
    	$this->assertEquals("", $qArray['query']);

    	//Now create a related Contact
    	$contact = new Contact();
    	$contact->name = "emailTestContact";
    	$contact->account_id = $account->id;
    	$contact->account_name = $account->name;
    	$contact->email1 = "test@test.com";
    	$contact->save(false);

    	//Now we should get a result
        $qArray = $this->eui->getRelatedEmail("LBL_DROPDOWN_LIST_ALL", array(), $relatedBeanInfo);
        $r = $account->db->limitQuery($qArray['query'], 0, 25, true);
        $person = array();
        $a = $account->db->fetchByAssoc($r);
        $person['bean_id'] = $a['id'];
        $person['bean_module'] = $a['module'];
        $person['email'] = $a['email_address'];

        //Cleanup
    	$GLOBALS['db']->query("DELETE FROM accounts WHERE id= '{$account->id}'");
    	$GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$contact->id}'");

        $this->assertEquals("test@test.com", $person['email']);
    }
    
    /**
     * @ticket 29521
     */
    public function testLoadQuickCreateModules()
    {
        $qArray = $this->eui->_loadQuickCreateModules();

        $this->assertEquals(array('Bugs','Cases','Contacts', 'Leads', 'Tasks'), $qArray);
    }

    /**
     * @ticket 29521
     */
    public function testLoadCustomQuickCreateModulesCanMergeModules()
    {
        if (file_exists('custom/modules/Emails/metadata/qcmodulesdefs.php')) {
            copy('custom/modules/Emails/metadata/qcmodulesdefs.php','custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak');
        }
        sugar_mkdir("custom/modules/Emails/metadata/",null,true);
        file_put_contents(
            'custom/modules/Emails/metadata/qcmodulesdefs.php',
            '<?php $QCModules[] = "Users"; ?>'
            );
        
        $qArray = $this->eui->_loadQuickCreateModules();

        if (file_exists('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak')) {
            copy('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak','custom/modules/Emails/metadata/qcmodulesdefs.php');
            unlink('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak');
        }
        else {
            unlink('custom/modules/Emails/metadata/qcmodulesdefs.php');
        }
        
        $this->assertEquals(array('Bugs','Cases','Contacts', 'Leads', 'Tasks', 'Users'), $qArray);
    }

    /**
     * @ticket 29521
     */
    public function testLoadQuickCreateModulesInvalidModule()
    {
        if (file_exists('custom/modules/Emails/metadata/qcmodulesdefs.php')) {
            copy('custom/modules/Emails/metadata/qcmodulesdefs.php','custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak');
        }
        sugar_mkdir("custom/modules/Emails/metadata/",null,true);
        file_put_contents(
            'custom/modules/Emails/metadata/qcmodulesdefs.php',
            '<?php $QCModules[] = "EmailUIUnitTest"; ?>'
            );
        
        $qArray = $this->eui->_loadQuickCreateModules();

        if (file_exists('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak')) {
            copy('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak','custom/modules/Emails/metadata/qcmodulesdefs.php');
            unlink('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak');
        }
        else {
            unlink('custom/modules/Emails/metadata/qcmodulesdefs.php');
        }
        
        $this->assertEquals(array('Bugs','Cases','Contacts', 'Leads', 'Tasks'), $qArray);
    }

    /**
     * @ticket 29521
     */
    public function testLoadQuickCreateModulesCanOverrideDefaultModules()
    {
        if (file_exists('custom/modules/Emails/metadata/qcmodulesdefs.php')) {
            copy('custom/modules/Emails/metadata/qcmodulesdefs.php','custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak');
        }
        sugar_mkdir("custom/modules/Emails/metadata/",null,true);
        file_put_contents(
            'custom/modules/Emails/metadata/qcmodulesdefs.php',
            '<?php $QCModules = array("Users"); ?>'
            );
        
        $qArray = $this->eui->_loadQuickCreateModules();

        if (file_exists('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak')) {
            copy('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak','custom/modules/Emails/metadata/qcmodulesdefs.php');
            unlink('custom/modules/Emails/metadata/qcmodulesdefs.php.test.bak');
        }
        else {
            unlink('custom/modules/Emails/metadata/qcmodulesdefs.php');
        }
        
        $this->assertEquals(array("Users"), $qArray);
    }
}

class EmailUIMock extends EmailUI
{
    public function _loadQuickCreateModules()
    {
        return parent::_loadQuickCreateModules();
    }
}