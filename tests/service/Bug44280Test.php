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


/**
 * @group bug44280
 */
class Bug44280Test extends Sugar_PHPUnit_Framework_TestCase
{
	public $_user = null;
	public $_soapClient = null;
	public $_session = null;
	public $_sessionId = null;
    public $accnt1;
    public $accnt2;
    public $cont1;
    public $cont2;

	

	public function setUp() 
    {
    	$this->_soapClient = new nusoapclient($GLOBALS['sugar_config']['site_url'].'/service/v2/soap.php',false,false,false,false,false,600,600);

        $this->_user = SugarTestUserUtilities::createAnonymousUser();
        $this->_user->status = 'Active';
        $this->_user->is_admin = 1;
        $this->_user->save();
        $GLOBALS['current_user'] = $this->_user;

       
        
		 
    }

    /**
     * Remove anything that was used during this test
     *
     */
    public function tearDown() {
    	SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);

        unset($this->_user);
        unset($this->_soapClient);
        unset($this->_session);
        unset($this->_sessionId);

        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->cont1->id}'");
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->cont2->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts_contacts WHERE contact_id= '{$this->cont1->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts_contacts WHERE contact_id= '{$this->cont2->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts WHERE id= '{$this->accnt1->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts WHERE id= '{$this->accnt2->id}'");

        unset($this->accnt1); unset($this->accnt2);
        unset($this->cont1); unset($this->cont2);

    }

    public function createAccount($name,$user_id) {
        $account = new Account();
		$account->id = uniqid();
        $account->name = $name;
        $account->assigned_user_id = $user_id;
        $account->new_with_id = true;
        $account->disable_custom_fields = true;
        $account->save();

        return $account;
    }

    public function createContact($first_name, $last_name, $email){
        $contact = new Contact();
		$contact->id = uniqid();
        $contact->first_name = $first_name;
        $contact->last_name = $last_name;
        $contact->email1 = $email;
        $contact->new_with_id = true;
        $contact->disable_custom_fields = true;
        $contact->save();

        return $contact;
    }
    
    public function testSetEntries() {
    	$this->_login();

        // first create two accounts with identical account names
        $this->accnt1 = $this->createAccount("sugar_account_name","sugarUser1");
        $this->accnt2 = $this->createAccount("sugar_account_name","sugarUser2");

        // now creating two contacts and relate them to the above accounts

        $this->cont1 = $this->createContact("first1", "last1", "adsf@asdf.com");
        $this->cont2 = $this->createContact("first2", "last2", "adsf@asdf.com");

         // this will be used in set_entries call
        $accounts_list=array( 'session'=>$this->_sessionId, 'module_name' => 'Accounts',
				   'name_value_lists' => array(
                                        array(
                                           array('name'=>'id','value'=>$this->accnt1->id),
                                           array('name'=>'first_name','value'=>$this->accnt1->name),
                                           array('name'=>'account_id','value'=>$this->accnt1->id),
                                           array('name'=>'team_id','value'=>'1'),
                                           array('name'=>'soap_dts_c','value'=>'2011-06-02 17:37:49'),
                                           array('name'=>'contactid_4d_c','value'=>'123456'),
                                           array('name'=>'phone_work','value'=>'1234567890'),
                                           array('name'=>'title','value'=>''),
                                       ),
                                        array(
                                           array('name'=>'id','value'=>$this->accnt2->id),
                                           array('name'=>'first_name','value'=>$this->accnt2->name),
                                           array('name'=>'account_id','value'=>$this->accnt2->id),
                                           array('name'=>'team_id','value'=>'1'),
                                           array('name'=>'soap_dts_c','value'=>'2011-06-02 16:37:49'),
                                           array('name'=>'contactid_4d_c','value'=>'999991'),
                                           array('name'=>'phone_work','value'=>'987654321'),
                                           array('name'=>'title','value'=>''),
                                       )
                                        )
                                       );
        // add the accounts
         $result = $this->_soapClient->call('set_entries', $accounts_list);

        // add the contacts & set the relationship to account
        $contacts_list = array( 'session'=>$this->_sessionId, 'module_name' => 'Contacts',
				   'name_value_lists' => array(
                                        array(
                                           array('name'=>'last_name','value'=>$this->cont1->last_name),
                                           array('name'=>'email','value'=>$this->cont1->email1),
                                           array('name'=>'first_name','value'=>$this->cont1->first_name),
                                           array('name'=>'id','value'=>$this->cont1->id),
                                           array('name'=>'account_name','value'=>$this->accnt1->name),
                                           array('name'=>'account_id','value'=>$this->accnt1->id),


                                       ),
                                        array(
                                            array('name'=>'last_name','value'=>$this->cont2->last_name),
                                            array('name'=>'email','value'=>$this->cont2->email1),
                                            array('name'=>'first_name','value'=>$this->cont2->first_name),
                                            array('name'=>'id','value'=>$this->cont2->id),
                                            array('name'=>'account_name','value'=>$this->accnt2->name),
                                            array('name'=>'account_id','value'=>$this->accnt2->id),

                                       )
                                        )
                                       );


        $result2 = $this->_soapClient->call('set_entries', $contacts_list);

         // lets check first relationship
        $query1 = "SELECT account_id FROM accounts_contacts WHERE contact_id='{$this->cont1->id}'";
        $cont1_account_result = $GLOBALS['db']->query($query1,true,"");
        $row1 = $GLOBALS['db']->fetchByAssoc($cont1_account_result);
        if(isset($row1) ){

            $this->assertEquals($this->accnt1->id, $row1["account_id"], "check first account-contact relationship");

          }


          // lets check second relationship
        $query2 = "SELECT account_id FROM accounts_contacts WHERE contact_id='{$this->cont2->id}'";
        $cont2_account_result = $GLOBALS['db']->query($query2,true,"");
        $row2 = $GLOBALS['db']->fetchByAssoc($cont2_account_result);
        if(isset($row2) ){

            $this->assertEquals($this->accnt2->id, $row2["account_id"], "check second account-contact relationship");

          }

         
    }  
    

    /**
     * Attempt to login to the soap server
     *
     * @return $set_entry_result - this should contain an id and error.  The id corresponds
     * to the session_id.
     */
    public function _login(){
		global $current_user;  	
    	$result = $this->_soapClient->call('login',
            array('user_auth' => 
                array('user_name' => $current_user->user_name,
                    'password' => $current_user->user_hash,
                    'version' => '1.0'),
                'application_name' => 'SoapTest')
            );
         $this->_sessionId = $result['id'];
		return $result;
    }
    

	
}
?>