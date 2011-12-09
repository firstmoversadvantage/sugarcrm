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

 
require_once('modules/EmailAddresses/EmailAddress.php');

/**
 * Test cases for php file Emails/emailAddress.php
 */
class EmailAddressTest extends Sugar_PHPUnit_Framework_TestCase
{
	private $emailaddress;
	private $testEmailAddressString  = 'unitTest@sugarcrm.com';

	public function setUp()
	{
		$this->emailaddress = new EmailAddress();
	}

	public function tearDown()
	{
		unset($this->emailaddress);
		$query = "delete from email_addresses where email_address = '".$this->testEmailAddressString."';";
        $GLOBALS['db']->query($query);
	}

	public function testEmailAddress()
	{
		$id = '';
		$module = '';
		$new_addrs=array();
		$primary='';
		$replyTo='';
		$invalid='';
		$optOut='';
		$in_workflow=false;
		$_REQUEST['_email_widget_id'] = 0;
		$_REQUEST['0emailAddress0'] = $this->testEmailAddressString;
		$_REQUEST['emailAddressPrimaryFlag'] = '0emailAddress0';
		$_REQUEST['emailAddressVerifiedFlag0'] = 'true';
		$_REQUEST['emailAddressVerifiedValue0'] = 'unitTest@sugarcrm.com';
		$requestVariablesSet = array('0emailAddress0','emailAddressPrimaryFlag','emailAddressVerifiedFlag0','emailAddressVerifiedValue0');
		$this->emailaddress->save($id, $module, $new_addrs, $primary, $replyTo, $invalid, $optOut, $in_workflow);
		foreach ($requestVariablesSet as $k)
		  unset($_REQUEST[$k]);

		$this->assertEquals($this->emailaddress->addresses[0]['email_address'], $this->testEmailAddressString);
		$this->assertEquals($this->emailaddress->addresses[0]['primary_address'], 1);
	}

	public function testSaveEmailAddressUsingSugarbeanSave()
	{
	    $this->emailaddress->email_address = $this->testEmailAddressString;
	    $this->emailaddress->opt_out = '1';
	    $this->emailaddress->save();

	    $this->assertTrue(!empty($this->emailaddress->id));
	    $this->assertEquals(
	        $this->emailaddress->id,
	        $GLOBALS['db']->getOne("SELECT id FROM email_addresses WHERE id = '{$this->emailaddress->id}' AND email_address = '{$this->testEmailAddressString}' and opt_out = '1'"),
	        'Email Address record not added'
	        );
	}

	public function getEmails()
	{
	    return array(
	        array("test@sugarcrm.com", "", "test@sugarcrm.com"),
	        array("John Doe <test@sugarcrm.com>", "John Doe", "test@sugarcrm.com"),
	        array("\"John Doe\" <test@sugarcrm.com>", "John Doe", "test@sugarcrm.com"),
	        array("\"John Doe\" <test@sugarcrm.com>", "John Doe", "test@sugarcrm.com"),
	        array("\"John Doe (<doe>)\" <test@sugarcrm.com>", "John Doe (doe)", "test@sugarcrm.com"),
	        // bad ones
	        array("\"John Doe (<doe>)\"", "John Doe (doe)", ""),
	        array("John Doe <vlha>", "John Doe vlha", ""),
	        array("<script>alert(1)</script>", "scriptalert(1)/script", ""),
	        array("Test <test@test>", "Test test@test", ""),
	        );
	}

	/**
	 * @dataProvider getEmails
	 * @param string $addr
	 * @param string $name
	 * @param string $email
	 */

	public function testSplitEmail($addr, $name, $email)
	{
	    $parts = $this->emailaddress->splitEmailAddress($addr);
	    $this->assertEquals($name, $parts['name']);
	    $this->assertEquals($email, $parts['email']);
	}
}
