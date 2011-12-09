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

 
require_once('service/v3/SugarWebServiceUtilv3.php');
require_once('tests/service/APIv3Helper.php');


class RESTAPI4Test extends Sugar_PHPUnit_Framework_TestCase
{
    protected $_user;
    protected $_admin_user;
    protected $_lastRawResponse;

    private static $helperObject;
    
    protected $aclRole;
    protected $aclField;
    
    public function setUp()
    {
        $beanList = array();
		$beanFiles = array();
		require('include/modules.php');
		$GLOBALS['beanList'] = $beanList;
		$GLOBALS['beanFiles'] = $beanFiles;
		
        //Reload langauge strings
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
        $GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], 'Accounts');
        //Create an anonymous user for login purposes/
        $this->_user = SugarTestUserUtilities::createAnonymousUser();
        
        $this->_admin_user = SugarTestUserUtilities::createAnonymousUser();
        $this->_admin_user->status = 'Active';
        $this->_admin_user->is_admin = 1;
        $this->_admin_user->save();
        $GLOBALS['current_user'] = $this->_user;

        self::$helperObject = new APIv3Helper();
        
        //Disable access to the website field.
        $this->aclRole = new ACLRole();
        $this->aclRole->name = "Unit Test";
        $this->aclRole->save();
        $this->aclRole->set_relationship('acl_roles_users', array('role_id'=>$this->aclRole->id ,'user_id'=> $this->_user->id), false);
    }

    public function tearDown()
	{
	    $GLOBALS['db']->query("DELETE FROM acl_roles WHERE id IN ( SELECT role_id FROM acl_roles_users WHERE user_id = '{$GLOBALS['current_user']->id}' )");
	    $GLOBALS['db']->query("DELETE FROM acl_roles_users WHERE user_id = '{$GLOBALS['current_user']->id}'");
	    
	    if(isset($GLOBALS['listViewDefs'])) unset($GLOBALS['listViewDefs']);
	    if(isset($GLOBALS['viewdefs'])) unset($GLOBALS['viewdefs']);
	    unset($GLOBALS['beanList']);
		unset($GLOBALS['beanFiles']);
		unset($GLOBALS['app_list_strings']);
	    unset($GLOBALS['app_strings']);
	    unset($GLOBALS['mod_strings']);
	    unset($GLOBALS['current_user']);
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
	}

    protected function _makeRESTCall($method,$parameters)
    {
        // specify the REST web service to interact with
        $url = $GLOBALS['sugar_config']['site_url'].'/service/v4/rest.php';
        // Open a curl session for making the call
        $curl = curl_init($url);
        // set URL and other appropriate options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        // build the request URL
        $json = json_encode($parameters);
        $postArgs = "method=$method&input_type=JSON&response_type=JSON&rest_data=$json";
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
        // Make the REST call, returning the result
        $response = curl_exec($curl);
        // Close the connection
        curl_close($curl);

        $this->_lastRawResponse = $response;

        // Convert the result from JSON format to a PHP array
        return json_decode($response,true);
    }

    protected function _returnLastRawResponse()
    {
        return "Error in web services call. Response was: {$this->_lastRawResponse}";
    }

    protected function _login($user = null)
    {
        if($user == null)
            $user = $this->_user;
        return $this->_makeRESTCall('login',
            array(
                'user_auth' =>
                    array(
                        'user_name' => $user->user_name,
                        'password' => $user->user_hash,
                        'version' => '.01',
                        ),
                'application_name' => 'mobile',
                'name_value_list' => array(),
                )
            );
    }
    /**
     * Ensure the ability to retrieve a module list of recrods that are favorites.
     *
     */
    public function testGetModuleFavoriteList()
    {
        $result = $this->_login($this->_admin_user);
        $session = $result['id'];

        $account = new Account();
        $account->id = uniqid();
        $account->new_with_id = TRUE;
        $account->name = "Test " . $account->id;
        $account->save();

        $this->_markBeanAsFavorite($session, "Accounts", $account->id);
        
        $whereClause = "accounts.name='{$account->name}'";
        $module = 'Accounts';
        $orderBy = 'name';
        $offset = 0;
        $returnFields = array('name');
        $linkNameFields = "";
        $maxResults = 50;
        $deleted = FALSE;
        $favorites = TRUE;
        $result = $this->_makeRESTCall('get_entry_list', array($session, $module, $whereClause, $orderBy,$offset, $returnFields,$linkNameFields, $maxResults, $deleted, $favorites));

        $this->assertEquals($account->id, $result['entry_list'][0]['id'],'Unable to retrieve account favorite list.');

        $GLOBALS['db']->query("DELETE FROM accounts WHERE id = '{$account->id}'");
        $GLOBALS['db']->query("DELETE FROM sugarfavorites WHERE record_id = '{$account->id}'");
    }
    
    /**
     * Test set entries call with name value list format key=>value.
     *
     */
    public function testSetEntriesCall()
    {
        $result = $this->_login();
        $session = $result['id'];
        $module = 'Contacts';
        $c1_uuid = uniqid();
        $c2_uuid = uniqid();
        $contacts = array( 
            array('first_name' => 'Unit Test', 'last_name' => $c1_uuid), 
            array('first_name' => 'Unit Test', 'last_name' => $c2_uuid)
        );
        $results = $this->_makeRESTCall('set_entries',
        array(
            'session' => $session,
            'module' => $module,
            'name_value_lists' => $contacts,
        ));
        $this->assertTrue(isset($results['ids']) && count($results['ids']) == 2);
        
        $actual_results = $this->_makeRESTCall('get_entries',
        array(
            'session' => $session,
            'module' => $module,
            'ids' => $results['ids'],
            'select_fields' => array('first_name','last_name')
        ));
        
        $this->assertTrue(isset($actual_results['entry_list']) && count($actual_results['entry_list']) == 2);
        $this->assertEquals($actual_results['entry_list'][0]['name_value_list']['last_name']['value'], $c1_uuid);
        $this->assertEquals($actual_results['entry_list'][1]['name_value_list']['last_name']['value'], $c2_uuid);
    }
    
    
    /**
     * Test search by module with favorites flag enabled.
     *
     */
    public function testSearchByModuleWithFavorites()
    {
        $result = $this->_login($this->_admin_user);
        $session = $result['id'];

        $account = new Account();
        $account->id = uniqid();
        $account->assigned_user_id = $this->_user->id;
        $account->team_id = 1;
        $account->new_with_id = TRUE;
        $account->name = "Unit Test Fav " . $account->id;
        $account->save();
        $this->_markBeanAsFavorite($session, "Accounts", $account->id);
        
        //Negative test.
        $account2 = new Account();
        $account2->id = uniqid();
        $account2->new_with_id = TRUE;
        $account2->name = "Unit Test Fav " . $account->id;
        $account->assigned_user_id = $this->_user->id;
        $account2->save();
        
        $searchModules = array('Accounts');
        $searchString = "Unit Test Fav ";
        $offSet = 0;
        $maxResults = 10;

        $results = $this->_makeRESTCall('search_by_module',
                        array(
                            'session' => $session,
                            'search_string'  => $searchString,
                            'modules' => $searchModules,
                            'offset'  => $offSet,
                            'max_results'     => $maxResults,
                            'assigned_user_id'    => $this->_user->id,
                            'select_fields' => array(),
                            'unified_search_only' => true,
                            'favorites' => true,                            
                            )
                        );
        
        $GLOBALS['db']->query("DELETE FROM accounts WHERE name like 'Unit Test %' ");
        $GLOBALS['db']->query("DELETE FROM sugarfavorites WHERE record_id = '{$account->id}'");
        $GLOBALS['db']->query("DELETE FROM sugarfavorites WHERE record_id = '{$account2->id}'");
        
        $this->assertTrue( self::$helperObject->findBeanIdFromEntryList($results['entry_list'],$account->id,'Accounts'), "Unable to find {$account->id} id in favorites search.");
        $this->assertFalse( self::$helperObject->findBeanIdFromEntryList($results['entry_list'],$account2->id,'Accounts'), "Account {$account2->id} id in favorites search should not be there.");
    }    
    /**
     * Private helper function to mark a bean as a favorite item.
     *
     * @param string $session
     * @param string $moduleName
     * @param string $recordID
     */
    private function _markBeanAsFavorite($session, $moduleName, $recordID)
    {
        $result = $this->_makeRESTCall('set_entry',
            array(
                'session' => $session,
                'module' => 'SugarFavorites',
                'name_value_list' => array(
                    array('name' => 'record_id', 'value' => $recordID),
                    array('name' => 'module', 'value' => $moduleName),
                    ),
                )
            );
    }


    public function testRelateAccountToTwoContacts()
    {
        $result = $this->_login();
        $this->assertTrue(!empty($result['id']) && $result['id'] != -1,$this->_returnLastRawResponse());
        $session = $result['id'];

        $result = $this->_makeRESTCall('set_entry',
            array(
                'session' => $session,
                'module' => 'Accounts',
                'name_value_list' => array(
                    array('name' => 'name', 'value' => 'New Account'),
                    array('name' => 'description', 'value' => 'This is an account created from a REST web services call'),
                    ),
                )
            );

        $this->assertTrue(!empty($result['id']) && $result['id'] != -1,$this->_returnLastRawResponse());

        $accountId = $result['id'];

        $result = $this->_makeRESTCall('set_entry',
            array(
                'session' => $session,
                'module' => 'Contacts',
                'name_value_list' => array(
                    array('name' => 'last_name', 'value' => 'New Contact 1'),
                    array('name' => 'description', 'value' => 'This is a contact created from a REST web services call'),
                    ),
                )
            );

        $this->assertTrue(!empty($result['id']) && $result['id'] != -1,$this->_returnLastRawResponse());

        $contactId1 = $result['id'];

        $result = $this->_makeRESTCall('set_entry',
            array(
                'session' => $session,
                'module' => 'Contacts',
                'name_value_list' => array(
                    array('name' => 'last_name', 'value' => 'New Contact 2'),
                    array('name' => 'description', 'value' => 'This is a contact created from a REST web services call'),
                    ),
                )
            );

        $this->assertTrue(!empty($result['id']) && $result['id'] != -1,$this->_returnLastRawResponse());

        $contactId2 = $result['id'];

        // now relate them together
        $result = $this->_makeRESTCall('set_relationship',
            array(
                'session' => $session,
                'module' => 'Accounts',
                'module_id' => $accountId,
                'link_field_name' => 'contacts',
                'related_ids' => array($contactId1,$contactId2),
                )
            );

        $this->assertEquals($result['created'],1,$this->_returnLastRawResponse());

        // check the relationship
        $result = $this->_makeRESTCall('get_relationships',
            array(
                'session' => $session,
                'module' => 'Accounts',
                'module_id' => $accountId,
                'link_field_name' => 'contacts',
                'related_module_query' => '',
                'related_fields' => array('last_name','description'),
                'related_module_link_name_to_fields_array' => array(),
                'deleted' => false,
                )
            );

        $returnedValues = array();
        $returnedValues[] = $result['entry_list'][0]['name_value_list']['last_name']['value'];
        $returnedValues[] = $result['entry_list'][1]['name_value_list']['last_name']['value'];

        $GLOBALS['db']->query("DELETE FROM accounts WHERE id= '{$accountId}'");
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$contactId1}'");
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$contactId2}'");
        $GLOBALS['db']->query("DELETE FROM accounts_contacts WHERE account_id= '{$accountId}'");

        $this->assertContains('New Contact 1',$returnedValues,$this->_returnLastRawResponse());
        $this->assertContains('New Contact 2',$returnedValues,$this->_returnLastRawResponse());
    }
}