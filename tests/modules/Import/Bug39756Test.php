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


require_once('modules/Accounts/Account.php');

class Bug39756Test extends Sugar_PHPUnit_Framework_TestCase
{
    var $_account = null;

    public function setUp() 
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->_account = new Account();
        $this->_account->name = 'Account_'.create_guid();
        $this->_account->save();

    }
    
    public function tearDown() 
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        $sql = "DELETE FROM accounts where id = '{$this->_account->id}'";
        $GLOBALS['db']->query($sql);
    }
    
    public function testUpdateDateEnteredWithValue()
    {
        global $disable_date_format;
        $disable_date_format = true;

       $newDateEntered = '2011-01-28 11:05:10';
       $oldDateEntered = $this->_account->date_entered;

       $this->_account->update_date_entered = true;
       $this->_account->date_entered = $newDateEntered;
       $this->_account->save();

       $acct = new Account();
       $acct->retrieve($this->_account->id);
       
       $this->assertNotEquals($acct->date_entered, $oldDateEntered, "Account date_entered should not be equal to old date_entered");
       $this->assertEquals($acct->date_entered, $newDateEntered, "Account date_entered should be equal to old date_entered");
    }

    public function testNoUpdateDateEnteredWithValue()
    {
        global $disable_date_format;
        $disable_date_format = true;

       $newDateEntered = '2011-01-28 11:05:10';
       $oldDateEntered = $this->_account->date_entered;

       $this->_account->date_entered = $newDateEntered;
       $this->_account->save();

       $acct = new Account();
       $acct->retrieve($this->_account->id);
       
       $this->assertEquals($acct->date_entered, $oldDateEntered, "Account date_entered should be equal to old date_entered");
       $this->assertNotEquals($acct->date_entered, $newDateEntered, "Account date_entered should not be equal to old date_entered");
    }
}
