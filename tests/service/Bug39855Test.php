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

 
require_once('include/nusoap/nusoap.php');
require_once('modules/Cases/Case.php');
require_once('modules/Accounts/Account.php');


/**
 * @group bug39234
 */
class Bug39855Test extends Sugar_PHPUnit_Framework_TestCase
{
	public $_user = null;
	public $_case1 = null;
	public $_case2 = null;
	public $_acc = null;
	public $_soapClient = null;
	public $_session = null;
	public $_sessionId = '';
    /**
     * Create test user
     *
     */
	public function setUp() 
    {
    	
        $this->markTestSkipped('Skipping for now while investigating');    	
        //setup test portal user
    	$this->_setupTestUser();
    	$this->_soapClient = new nusoapclient($GLOBALS['sugar_config']['site_url'].'/soap.php',false,false,false,false,false,600,600);
    	$this->_login();
    	
    	//setup test account
		$account = new Account();
        $account->name = 'test account for bug 39855';
        $account->assigned_user_id = 'SugarUser';
        $account->save();
        $this->_acc = $account;
    	
    	//setup test cases
		$case1 = new aCase();
        $case1->name = 'test case for bug 39855 ASDF';
        $case1->account_id = $this->_acc->id;
        $case1->status = 'New';
        $case1->save();
        $this->_case1 = $case1;

        $case2 = new aCase();
		//$account->id = 'a_'.$unid;
        $case2->name = 'test case for bug 39855 QWER';
        $case2->account_id = $this->_acc->id;
        $case2->status = 'Rejected';
        $case2->save();
        $this->_case2 = $case2;
        
        
    }

    /**
     * Remove anything that was used during this test
     *
     */
    public function tearDown() {
    	global $soap_version_test_accountId, $soap_version_test_opportunityId, $soap_version_test_contactId;
        $this->_tearDownTestUser();
        $this->_user = null;
        $this->_sessionId = '';
        $GLOBALS['db']->query("DELETE FROM cases WHERE name like 'test case for bug 39855%'");
        $GLOBALS['db']->query("DELETE FROM accounts WHERE name like 'test account for bug 39855%'");
        
        unset($this->_case1);
        unset($this->_case2);
        unset($this->_acc1);
        
    }	
    
    public function testGetEntry() {
    	//test retrieving a case by id
    	$result =  $this->_soapClient->call('portal_get_entry',array('session'=>$this->_sessionId,'module_name'=>'Cases','id'=>$this->_case1->id ,'select_field'=>array('case_number','status', 'name','description')));
		$this->assertTrue($result['entry_list'][0]['id'] == $this->_case1->id,'portal_get_entry was not able to retrieve a case record by id');

    	$result =  $this->_soapClient->call('portal_logout',array('session' => $this->_sessionId));
    }
    
     public function testGetEntryList() {
    	$w = " name LIKE 'test case for bug 39855 %' ";
    	
    	$result =  $this->_soapClient->call('portal_get_entry_list',array('session'=>$this->_sessionId,'module_name'=>'Cases','where'=>$w ,'', 'select_field'=>array('case_number','status', 'name','description')));
		$this->assertTrue($result['result_count'] > 1,'portal_get_entry_list was not able to retrieve both cases using the following where clause: '.$w);

    } 
    
	/**********************************
     * HELPER PUBLIC FUNCTIONS
     **********************************/
    
    /**
     * Attempt to login to the soap server
     *
     * @return $set_entry_result - this should contain an id and error.  The id corresponds
     * to the session_id.
     */
    public function _login(){
		global $current_user;  	
    	$result = $this->_soapClient->call('portal_login',
            array('user_auth' => 
                array('user_name' => $this->_user->user_name,
                    'password' => $this->_user->user_hash, 
                    'version' => '.01'), 
                	'user_name' =>'portal',
                'application_name' => 'SoapTestPortal')
            );
        $this->_sessionId = $result['id'];
		return $result;
		
    }
    
 /**
     * Create a test portal user
     *
     */
	public function _setupTestUser() {
        $this->_user = SugarTestUserUtilities::createAnonymousUser();
        $this->_user->status = 'Active';
        $this->_user->portal_only = 1;
        $this->_user->save();
    }
    

        
    /**
     * Remove user created for test
     *
     */
	public function _tearDownTestUser() {
       SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
    }
	
}
?>