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

 
require_once 'modules/Import/UsersLastImport.php';

class UsersLastImportTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_importModule;
    private $_importRecordCount;
    private $_importIds;
    private $_usersLastImport;
    private $_usersLastImportIds;
    
    public function setUp() 
    {
        $beanList = array();
        $beanFiles = array();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->_importModule = 'Notes';
        $this->_importObject = 'Note';
        $this->_importRecordCount = 3;
        $this->_importIds = array();
        $this->_usersLastImport = new UsersLastImport();
        $this->_addImportedRecords();
    }
    
    public function tearDown() 
    {
        $focus = $this->_loadBean($this->_importModule);
        $GLOBALS['db']->query(
            'DELETE FROM ' . $focus->table_name . ' 
                WHERE id IN (\'' . 
                    implode("','",$this->_importIds) . '\')');
        $GLOBALS['db']->query(
            'DELETE FROM users_last_import 
                WHERE id IN (\'' . 
                    implode("','",$this->_usersLastImportIds) . '\')');
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        unset($GLOBALS['beanList']);
        unset($GLOBALS['beanFiles']);
    }
    
    private function _loadBean()
    {
        return loadBean($this->_importModule);
    }
    
    private function _addImportedRecords()
    {
        for ( $i = 0; $i < $this->_importRecordCount; $i++ ) {
            $focus = $this->_loadBean($this->_importModule);
            $focus->name = "record $i";
            $focus->save();
            $this->_importIds[$i] = $focus->id;
            
            $last_import = new UsersLastImport();
            $last_import->assigned_user_id = $GLOBALS['current_user']->id;
            $last_import->import_module = $this->_importModule;
            $last_import->bean_type = $this->_importObject;
            $last_import->bean_id = $this->_importIds[$i];
            $this->_usersLastImportIds[] = $last_import->save();
        }
    }
    
    public function testMarkDeletedByUserId()
    {
        $this->_usersLastImport->mark_deleted_by_user_id($GLOBALS['current_user']->id);
        
        $query = "SELECT * FROM users_last_import 
                    WHERE assigned_user_id = '{$GLOBALS['current_user']->id}'";
        
        $result = $GLOBALS['db']->query($query);
       
        $this->assertFalse($GLOBALS['db']->fetchByAssoc($result),'There should not be any records in the table now');
    }
    
    public function testUndo()
    {
        $this->assertTrue(
            $this->_usersLastImport->undo(
                $this->_importModule
                )
            );
        
        $focus = $this->_loadBean($this->_importModule);
        
        $query = "SELECT * FROM {$focus->table_name}
                    WHERE id IN ('" . 
                        implode("','",$this->_importIds) . "')";
        
        $result = $GLOBALS['db']->query($query);
        
        $this->assertFalse($GLOBALS['db']->fetchByAssoc($result),'There should not be any records in the table now');
    }
    
    /**
     * @ticket 21828
     */
    public function testUndoRemovedAddedEmailAddresses()
    {
        $time = date('Y-m-d H:i:s');
        $unid = uniqid();
        
        $focus = new Account();
        $focus->id = "Account_".$unid;
        
        $last_import = new UsersLastImport();
        $last_import->assigned_user_id = $GLOBALS['current_user']->id;
        $last_import->import_module = 'Accounts';
        $last_import->bean_type = 'Account';
        $last_import->bean_id = $focus->id;
        $last_import->save();
        
        $this->email_addr_bean_rel_id = 'email_addr_bean_rel_'.$unid;
        $this->email_address_id = 'email_address_id_'.$unid;
        $GLOBALS['db']->query("insert into email_addr_bean_rel (id , email_address_id, bean_id, bean_module, primary_address, date_created , date_modified) values ('{$this->email_addr_bean_rel_id}', '{$this->email_address_id}', '{$focus->id}', 'Accounts', 1, '$time', '$time')");
				
        $GLOBALS['db']->query("insert into email_addresses (id , email_address, email_address_caps, date_created, date_modified) values ('{$this->email_address_id}', 'test@g.com', 'TEST@G.COM', '$time', '$time')");

        // setup
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        
        $this->assertTrue(
            $last_import->undo(
                $last_import->import_module
                )
            );
        
        // teardown
        unset($GLOBALS['beanList']);
        unset($GLOBALS['beanFiles']);
    	
    	$result = $GLOBALS['db']->query("SELECT * FROM email_addr_bean_rel where id = '{$this->email_addr_bean_rel_id}'");
		$rows = $GLOBALS['db']->fetchByAssoc($result);
    	$this->assertFalse($rows);
    	
    	$result = $GLOBALS['db']->query("SELECT * FROM email_addresses where id = '{$this->email_address_id}'");
		$rows = $GLOBALS['db']->fetchByAssoc($result);
    	$this->assertFalse($rows);
        
        $GLOBALS['db']->query("DELETE FROM users_last_import WHERE id = '{$last_import->id}'");
    }
    
    public function testUndoById()
    {
        $this->assertTrue(
            $this->_usersLastImport->undoById(
                $this->_usersLastImportIds[0]
                )
            );
        
        $focus = $this->_loadBean($this->_importModule);
        
        $query = "SELECT * FROM {$focus->table_name}
                    WHERE id = '{$this->_importIds[0]}'";
        
        $result = $GLOBALS['db']->query($query);
        
        $this->assertFalse($GLOBALS['db']->fetchByAssoc($result),'There should not be any records in the table now');

    }
    
    public function testGetBeansByImport()
    {
        foreach ( UsersLastImport::getBeansByImport('Notes') as $objectName )
            $this->assertEquals($objectName,'Note');
    }
}
