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

class Bug45960 extends Sugar_PHPUnit_Framework_TestCase
{
    protected $email_id = null;

    public function setUp()
    {
        $this->_user = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['current_user'] = $this->_user;
        $this->_account = SugarTestAccountUtilities::createAccount();
    }
    
    public function tearDown()
    {
        if ($this->email_id) {
            $GLOBALS['db']->query("delete from emails where id='{$this->email_id}'");
        }
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }

    public function testSaveNewEmailWithParent()
    {
        $email = new Email();
        $email->type = 'out';
        $email->status = 'sent';
        $email->from_addr_name = $email->cleanEmails("sender@domain.eu");
        $email->to_addrs_names = $email->cleanEmails("to@domain.eu");
        $email->cc_addrs_names = $email->cleanEmails("cc@domain.eu");

        // set a few parent info to test the scenario
        $email->parent_type = 'Accounts';
        $email->parent_id = $this->_account->id;
        $email->fetched_row['parent_type'] = 'Accounts';
        $email->fetched_row['parent_id'] = $this->_account->id;

        $email->save();

        $this->assertNotNull($email->id, 'Null email id');
        $this->email_id = $email->id;

        // ensure record is inserted into emails_beans table
        $query = "select count(*) as CNT from emails_beans eb WHERE eb.bean_id = '{$this->_account->id}' AND eb.bean_module = 'Accounts' AND eb.email_id = '{$email->id}' AND eb.deleted=0";
        $result = $GLOBALS['db']->query($query);
        $count = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertEquals(1, $count['CNT'], 'Incorrect emails_beans count');
    }
    
}


