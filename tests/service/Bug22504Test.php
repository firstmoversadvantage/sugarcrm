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

require_once 'tests/service/SOAPTestCase.php';
require_once 'tests/SugarTestAccountUtilities.php';
require_once 'modules/Emails/Email.php';
/**
 * @ticket 22504
 */
class Bug22504Test extends SOAPTestCase
{
    /**
     * Create test account
     *
     */
	public function setUp()
    {
    	$this->_soapURL = $GLOBALS['sugar_config']['site_url'].'/service/v3_1/soap.php';
    	$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
    	$this->acc = SugarTestAccountUtilities::createAccount();
		parent::setUp();
    }

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        if(!empty($this->email_id)) {
            $GLOBALS['db']->query("DELETE FROM emails WHERE id='{$this->email_id}'");
            $GLOBALS['db']->query("DELETE FROM emails_beans WHERE email_id='{$this->email_id}'");
            $GLOBALS['db']->query("DELETE FROM emails_text WHERE email_id='{$this->email_id}'");
            $GLOBALS['db']->query("DELETE FROM emails_email_addr_rel WHERE email_id='{$this->email_id}'");
        }
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        parent::tearDown();
    }

    public function testEmailImport()
    {
    	$this->_login();
    	$nv = array(
    	    'from_addr' => 'test@test.com',
    	    'parent_type' => 'Accounts',
    	    'parent_id' => $this->acc->id,
    	    'description' => 'test',
    	    'name' => 'Test Subject',
    	);
		$result = $this->_soapClient->call('set_entry',array('session'=>$this->_sessionId,"module_name" => 'Emails', 'name_value_list' => $nv));
		$this->email_id = $result['id'];
        $email = new Email();
        $email->retrieve($this->email_id );
        $email->load_relationship('accounts');
        $acc = $email->accounts->get();
        $this->assertEquals($this->acc->id, $acc[0]);
    }
}
