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

 
require_once('modules/Emails/Email.php');

class Bug40527Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $contact;
    private $account;
    private $email;
    
	public function setUp()
    {
        global $app_strings, $app_list_strings;
        $app_strings = return_application_language($GLOBALS['current_language']);
        $app_list_strings = return_app_list_strings_language($GLOBALS['current_language']);
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->contact = SugarTestContactUtilities::createContact();
        $this->account = SugarTestAccountUtilities::createAccount();
        
        $override_data = array(
            'parent_type' => 'Accounts',
            'parent_id' => $this->account->id,
        );
        $this->email   = SugarTestEmailUtilities::createEmail('', $override_data);
	}

    public function tearDown()
    {
        SugarTestContactUtilities::removeAllCreatedContacts();
        SugarTestAccountUtilities::removeAllCreatedAccounts();

        SugarTestEmailUtilities::removeAllCreatedEmails();
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    public function testContactRelationship()
    {
        $this->assertTrue($this->email->parent_type == 'Accounts', "The email parent_type should be Accounts");
        $this->assertTrue($this->email->parent_id == $this->account->id, "The email parent_id should be SDizzle");
        
        $this->email->fill_in_additional_detail_fields();
        $this->assertTrue(empty($this->email->contact_id), "There should be no contact associated with the Email");
    }
}
