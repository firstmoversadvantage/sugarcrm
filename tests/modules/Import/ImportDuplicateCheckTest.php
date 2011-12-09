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


require_once 'modules/Import/ImportDuplicateCheck.php';

class ImportDuplicateCheckTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $beanList = array();
        $beanFiles = array();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $app_strings = array();
        require('include/language/en_us.lang.php');
        $GLOBALS['app_strings'] = $app_strings;
    }
    
    public function tearDown() 
    {
        unset($GLOBALS['beanList']);
        unset($GLOBALS['beanFiles']);
        unset($GLOBALS['app_strings']);
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    public function testGetDuplicateCheckIndexesWithEmail()
    {
        $focus = loadBean('Contacts');
        
        $idc     = new ImportDuplicateCheck($focus);
        $indexes = $idc->getDuplicateCheckIndexes();
        
        foreach ( $focus->getIndices() as $key => $index ) {
            if ($key != 'id') $this->assertTrue(isset($indexes[$index['name']]),"{$index['name']} should be in the list");
        }
        
        $this->assertTrue(isset($indexes['special_idx_email1']));
        $this->assertTrue(isset($indexes['special_idx_email2']));
    }
    
    public function testGetDuplicateCheckIndexesNoEmail()
    {
        $focus = loadBean('Calls');
        
        $idc     = new ImportDuplicateCheck($focus);
        $indexes = $idc->getDuplicateCheckIndexes();
        
        foreach ( $focus->getIndices() as $key => $index ) {
            if ($key != 'id') $this->assertTrue(isset($indexes[$index['name']]));
        }
        
        $this->assertFalse(isset($indexes['special_idx_email1']));
        $this->assertFalse(isset($indexes['special_idx_email2']));
    }
    
    public function testIsADuplicateRecord()
    {
        $last_name = 'FooBar'.date("YmdHis");
        
        $focus = loadBean('Contacts');
        $focus->last_name = $last_name;
        $id = $focus->save(false);
        
        $focus = loadBean('Contacts');
        $focus->last_name = $last_name;
        
        $idc = new ImportDuplicateCheck($focus);
        
        $this->assertTrue($idc->isADuplicateRecord(array('idx_contacts_del_last::last_name')));
        
        $focus->mark_deleted($id);
    }
    
    public function testIsADuplicateRecordEmail()
    {
        $email = date("YmdHis").'@foobar.com';
        
        $focus = loadBean('Contacts');
        $focus->email1 = $email;
        $id = $focus->save(false);
        
        $focus = loadBean('Contacts');
        $focus->email1 = $email;
        
        $idc = new ImportDuplicateCheck($focus);
        
        $this->assertTrue($idc->isADuplicateRecord(array('special_idx_email1')));
        
        $focus->mark_deleted($id);
    }
    
    public function testIsADuplicateRecordNotFound()
    {
        $last_name = 'BadFooBar'.date("YmdHis");
        
        $focus = loadBean('Contacts');
        $focus->last_name = $last_name;
        
        $idc = new ImportDuplicateCheck($focus);
        
        $this->assertFalse($idc->isADuplicateRecord(array('idx_contacts_del_last::'.$last_name)));
    }
    
    public function testIsADuplicateRecordEmailNotFound()
    {
        $email = date("YmdHis").'@badfoobar.com';
        
        $focus = loadBean('Contacts');
        $focus->email1 = $email;
        
        $idc = new ImportDuplicateCheck($focus);
        
        $this->assertFalse($idc->isADuplicateRecord(array('special_idx_email1')));
    }

    //make sure exclusion array is respected when displaying the list of available indexes for dupe checking
    public function testExcludeIndexesFromDupeCheck()
    {
        //create the bean to test on
        $focus = loadBean('Contacts');

        //create the importDuplicateCheck object and get the list of duplicateCheckIndexes
        $idc = new ImportDuplicateCheck($focus);

        //get the list of importable indexes
        $indexes = $import_indexes = $focus->getIndices();


        //grab any custom indexes if they exist
        if($focus->hasCustomFields()){
            $custmIndexes = $focus->db->helper->get_indices($focus->table_name.'_cstm');
            $indexes = array_merge($custmIndexes,$indexes);
        }

        //get list indexes to be displayed
        $dupe_check_indexes = $idc->getDuplicateCheckIndexedFiles();

        //Make sure that the indexes used for dupe checking honors the exclusion array.  At a minimum, all beans will have
        //their id and teamset indexes excluded.
        $this->assertTrue(count($indexes) > count($dupe_check_indexes), 'Indexes specified for exclusion are not getting excluded from getDuplicateCheckIndexedFiles()');
    }


    //make sure only selected indexes are checked for dupes
    public function testCompareOnlySelectedIndexesFromDupeCheck()
    {
        //create a bean, values, populate and save
        $focus = loadBean('Contacts');
        $focus->first_name = 'first '.date("YmdHis");
        $focus->last_name = 'last '.date("YmdHis");
        $focus->assigned_user_id = '1';
        $focus->save();


        //create the importDuplicateCheck object and get the list of duplicateCheckIndexes
        $idc = new ImportDuplicateCheck($focus);

        //we are going to test agains the first name, last name, full name, and assigned to indexes
        //to prove that only selected indexes are being used.

        //lets do a straight dupe check with the same bean on first name, should return true
        $this->assertTrue($idc->isADuplicateRecord(array('idx_cont_last_first::first_name')),'simulated check against first name index (idx_cont_last_first::first_name) failed  (returned false instead of true).');

        //now lets test on full name index should also return true
        $this->assertTrue($idc->isADuplicateRecord(array('full_name::full_name')),'first simulated check against full name index (full_name::full_name) failed (returned false instead of true).  This check means BOTH first AND last name must match.');

        //now lets remove the first name and redo the check, should return false
        $focus->first_name = '';
        $idc = new ImportDuplicateCheck($focus);
        $this->assertFalse($idc->isADuplicateRecord(array('idx_cont_last_first::first_name')),'simulated check against first name index (idx_cont_last_first::first_name) failed (returned true instead of false).  This is wrong because we removed the first name so there should be no match.');

        //lets retest on full name index should return false now as first AND last do not match the original
        $this->assertFalse($idc->isADuplicateRecord(array('full_name::full_name')),'second simulated check against full name index (full_name::full_name) failed (returned true instead of false).  This check means BOTH first AND last name must match and is wrong because we removed the first name so there should be no match.');

        //now lets rename the contact and test on assigned user, should return true
        $focus->first_name = 'first '.date("YmdHis");
        $focus->last_name = 'last '.date("YmdHis");
        $idc = new ImportDuplicateCheck($focus);
        $this->assertTrue($idc->isADuplicateRecord(array('idx_del_id_user::assigned_user_id')),'simulated check against assigned user index (idx_del_id_user::assigned_user_id) failed (returned false instead of true).  This is wrong because we have not changed this field and it should remain a duplicate');

        //we're done, lets delete the focus bean now
        $focus->mark_deleted($focus->id);

    }



}
