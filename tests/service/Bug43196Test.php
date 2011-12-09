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
 * @group bug43196
 */
class Bug43196Test extends Sugar_PHPUnit_Framework_TestCase
{
	public $_soapClient = null;
	
	public function setUp() 
    {
        $this->_soapClient = new nusoapclient($GLOBALS['sugar_config']['site_url'].'/soap.php',false,false,false,false,false,600,600);
        
        $beanList = array();
        $beanFiles = array();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['current_user']->status = 'Active';
        $GLOBALS['current_user']->is_admin = 1;
        $GLOBALS['current_user']->save();
    }

    public function tearDown() 
    {
        foreach ( SugarTestContactUtilities::getCreatedContactIds() as $id ) {
            $GLOBALS['db']->query("DELETE FROM accounts_contacts WHERE contact_id = '{$id}'");
        }
        SugarTestContactUtilities::removeAllCreatedContacts();
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        
        unset($GLOBALS['beanList']);
        unset($GLOBALS['beanFiles']);
    }	
    
    public function testGetEntryWhenContactHasMultipleAccountRelationshipsWorks() 
    {
        $contact = SugarTestContactUtilities::createContact();
        $account1 = SugarTestAccountUtilities::createAccount();
        $account2 = SugarTestAccountUtilities::createAccount();
        
        $contact->load_relationship('accounts');
        $contact->accounts->add($account1->id);
        $contact->accounts->add($account2->id);
        
        $this->_login();
        
        $parameters = array(
            'session' => $this->_sessionId,
            'module_name' => 'Contacts',
            'query' => "contacts.id = '{$contact->id}'",
            'order_by' => '',
            'offset' => 0,
            'select_fields' => array('id', 'account_id', 'account_name'),
            'max_results' => 250,
            'deleted' => 0,
            );
            
        $result = $this->_soapClient->call('get_entry_list',$parameters);
        
        $account_names = array($account1->name, $account2->name);
        $account_ids = array($account1->id, $account2->id);
        /*
        $this->assertEquals($result['entry_list'][0]['name_value_list'][1]['value'],$account1->name);
        $this->assertEquals($result['entry_list'][0]['name_value_list'][2]['value'],$account1->id);
        $this->assertEquals($result['entry_list'][1]['name_value_list'][1]['value'],$account2->name);
        $this->assertEquals($result['entry_list'][1]['name_value_list'][2]['value'],$account2->id);
        */
        $this->assertTrue(in_array($result['entry_list'][0]['name_value_list'][1]['value'], $account_names));
        $this->assertTrue(in_array($result['entry_list'][1]['name_value_list'][1]['value'], $account_names));
        $this->assertTrue(in_array($result['entry_list'][0]['name_value_list'][2]['value'], $account_ids));
        $this->assertTrue(in_array($result['entry_list'][1]['name_value_list'][2]['value'], $account_ids));
    }
    
    /**
     * Attempt to login to the soap server
     *
     * @return $set_entry_result - this should contain an id and error.  The id corresponds
     * to the session_id.
     */
    public function _login()
    {
		global $current_user;  	
    	
		$result = $this->_soapClient->call(
		    'login',
            array('user_auth' => 
                array('user_name' => $current_user->user_name,
                    'password' => $current_user->user_hash, 
                    'version' => '.01'), 
                'application_name' => 'SoapTest')
            );
        $this->_sessionId = $result['id'];
		
        return $result;
    }
}