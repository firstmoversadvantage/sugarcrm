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

/**
 * @ticket 41296
 */
require_once('include/nusoap/nusoap.php');
require_once 'tests/service/SOAPTestCase.php';


class Bug41296Test extends SOAPTestCase
{
	public $_soapClient = null;
    var $_sessionId;
    var $c = null;
    var $c2 = null;
	
	public function setUp() 
    {
        $this->_soapURL = $GLOBALS['sugar_config']['site_url'].'/soap.php';
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        
		$unid = uniqid();
		$time = date('Y-m-d H:i:s');

        $contact = new Contact();
		$contact->id = 'c_'.$unid;
        $contact->first_name = 'testfirst';
        $contact->last_name = 'testlast';
        $contact->email1 = 'one@example.com';
        $contact->email2 = 'one_other@example.com';
        $contact->new_with_id = true;
        $contact->disable_custom_fields = true;
        $contact->save();
		$this->c = $contact;

        parent::setUp();
    }

    public function tearDown() 
    {
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->c->id}'");
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->_resultId}'");
        unset($this->c);
        parent::tearDown();
    }

    public function testCreateNewContactWithNoEmail()
    {
        $this->_login();

        $contacts_list=array( 'session'=>$this->_sessionId, 'module_name' => 'Contacts',
				   'name_value_lists' => array(
                                        array(array('name'=>'assigned_user_id' , 'value'=>$this->_user->id),array('name'=>'first_name' , 'value'=>'testfirst'),array('name'=>'last_name' , 'value'=>'testlast'))
                                        ));

        $result = $this->_soapClient->call('set_entries',$contacts_list);
        $this->_resultId = $result['ids'][0];
        $this->assertNotEquals($this->c->id, $result['ids'][0], "Contacts should not match");
    }

}