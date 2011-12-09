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
 * @ticket 32487
 */
class GetNamePlusEmailAddressesForComposeTest extends Sugar_PHPUnit_Framework_TestCase
{
	public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    public function testGetNamePlusEmailAddressesForCompose()
	{    
	    $account = SugarTestAccountUtilities::createAccount();
        
	    $email = new Email;
	    $this->assertEquals(
	        "{$account->name} <{$account->email1}>",
	        $email->getNamePlusEmailAddressesForCompose('Accounts',array($account->id))
	        );
	    
	    SugarTestAccountUtilities::removeAllCreatedAccounts();
    }
    
    public function testGetNamePlusEmailAddressesForComposeMultipleIds()
	{    
	    $account1 = SugarTestAccountUtilities::createAccount();
	    $account2 = SugarTestAccountUtilities::createAccount();
	    $account3 = SugarTestAccountUtilities::createAccount();
        
	    $email = new Email;
	    $addressString = $email->getNamePlusEmailAddressesForCompose('Accounts',array($account1->id,$account2->id,$account3->id));
	    $this->assertContains("{$account1->name} <{$account1->email1}>",$addressString);
	    $this->assertContains("{$account2->name} <{$account2->email1}>",$addressString);
	    $this->assertContains("{$account3->name} <{$account3->email1}>",$addressString);
	    
	    SugarTestAccountUtilities::removeAllCreatedAccounts();
    }
    

	public function testGetNamePlusEmailAddressesForComposePersonModule()
	{    
	    $contact = SugarTestContactUtilities::createContact();
        
	    $email = new Email;
	    $this->assertEquals(
	        $GLOBALS['locale']->getLocaleFormattedName($contact->first_name, $contact->last_name, $contact->salutation, $contact->title) . " <{$contact->email1}>",
	        $email->getNamePlusEmailAddressesForCompose('Contacts',array($contact->id))
	        );
	    
	    SugarTestContactUtilities::removeAllCreatedContacts();
    }
    
    public function testGetNamePlusEmailAddressesForComposeUser()
	{    
	    $user = SugarTestUserUtilities::createAnonymousUser();
	    $user->email1 = 'foo@bar.com';
	    $user->save();
	    
	    $email = new Email;
	    $this->assertEquals(
	        $GLOBALS['locale']->getLocaleFormattedName($user->first_name, $user->last_name, '', $user->title) . " <{$user->email1}>",
	        $email->getNamePlusEmailAddressesForCompose('Users',array($user->id))
	        );
    }
}