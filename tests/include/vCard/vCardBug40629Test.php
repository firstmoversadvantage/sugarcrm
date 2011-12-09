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

 
require_once 'include/vCard.php';

class vCardBug40629Test extends Sugar_PHPUnit_Framework_TestCase
{
    protected $account;
    
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->account = SugarTestAccountUtilities::createAccount();
        $this->account->name = "SDizzle Inc";
        $this->account->save();
    }
    
    public function tearDown()
    {
        unset($GLOBALS['current_user']);
        SugarTestAccountUtilities::removeAllCreatedAccounts();
    }
    
    /**
     * @group bug40629
     */
	public function testImportedVcardAccountLink()
    {
        $filename  = dirname(__FILE__)."/SimpleVCard.vcf";
        
        $vcard = new vCard();
        $contact_id = $vcard->importVCard($filename,'Contacts');
        $contact_record = new Contact();
        $contact_record->retrieve($contact_id);
        
        $this->assertFalse(empty($contact_record->account_id), "Contact should have an account record associated");
        $GLOBALS['db']->query("delete from contacts where id = '{$contact_id}'");
        
        $vcard = new vCard();
        $lead_id = $vcard->importVCard($filename,'Leads');
        $lead_record = new Lead();
        $lead_record->retrieve($lead_id);
        
        $this->assertTrue(empty($lead_record->account_id), "Lead should not have an account record associated");
        $GLOBALS['db']->query("delete from leads where id = '{$lead_id}'");
    }
}