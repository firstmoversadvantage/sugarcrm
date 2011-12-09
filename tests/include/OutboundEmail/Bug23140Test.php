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

 
require_once('include/OutboundEmail/OutboundEmail.php');

/**
 * @ticket 23140
 */
class Bug23140Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $outbound_id = null;
	var $_user = null;
	var $ob = null;
	var $userOverideAccont = null;
	
	public function setUp()
    {
        global $current_user, $currentModule ;
		$this->_user = SugarTestUserUtilities::createAnonymousUser();
		$current_user = $this->_user;
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        //unset($GLOBALS['current_user']);
        
        if ($this->ob != null)
            $GLOBALS['db']->query("DELETE FROM outbound_email WHERE id= '{$this->ob->id}'");
        if ($this->userOverideAccont != null)
            $GLOBALS['db']->query("DELETE FROM outbound_email WHERE id= '{$this->userOverideAccont->id}'");
    }
    
    function testSystemAccountMailSettingsChangedUserAccessToUsername()
    {
        //User not alloweed to access system email username/password
        $GLOBALS['db']->query("INSERT INTO config (category,name,value) VALUES ('notify','allow_default_outbound','2') ");
        
        $newSystemPort = 864;
        $newSystemServer = "system.imap.com";
        $newSystemUsername = "system_user_name";
        $newSystemPassword = "SYSTEM_PASSWORD";
        
        $userID = create_guid();
        $ob = new OutboundEmail();
        $ob->id = $userID;
        $ob->new_with_id = TRUE;
        $ob->name = 'Sugar Test';
        $ob->type = 'system-override';
        $ob->user_id = $this->_user->id;
        $ob->mail_sendtype = "SMTP";
        $ob->mail_smtpuser = "Test User";
        $ob->mail_smtppass = "User Pass";
        $ob->save();
        $this->ob = $ob;
        
        
        $system = $ob->getSystemMailerSettings();
        $system->new_with_id = FALSE;
        $system->mail_smtpport = $newSystemPort;
        $system->mail_smtpserver = $newSystemServer;
        $system->mail_smtpuser = $newSystemUsername;
        $system->mail_smtppass = $newSystemPassword;
        
        $system->saveSystem();
        
        $obRetrieved = new OutboundEmail();
        $obRetrieved->retrieve($userID);
        
        $this->assertEquals($newSystemPort, $obRetrieved->mail_smtpport, "Could not update users system-override accounts after system save.");
        $this->assertEquals($newSystemServer, $obRetrieved->mail_smtpserver, "Could not update users system-override accounts after system save.");
        $this->assertEquals($newSystemUsername, $obRetrieved->mail_smtpuser, "Could not update users system-override accounts after system save.");
        $this->assertEquals($newSystemPassword, $obRetrieved->mail_smtppass, "Could not update users system-override accounts after system save.");
        
    }
    
    
    function testSystemAccountMailSettingsChangedNoUserAccessToUsername()
    {
        //User not alloweed to access system email username/password
        $GLOBALS['db']->query("DELETE FROM config WHERE category='notify' AND name='allow_default_outbound' ");
        
        $newSystemPort = 864;
        $newSystemServer = "system.imap.com";
        
        $userID = create_guid();
        $ob = new OutboundEmail();
        $ob->id = $userID;
        $ob->new_with_id = TRUE;
        $ob->name = 'Sugar Test';
        $ob->type = 'system-override';
        $ob->user_id = $this->_user->id;
        $ob->mail_sendtype = "SMTP";
        $ob->mail_smtpuser = "Test User";
        $ob->mail_smtppass = "User Pass";
        $ob->save();
        $this->ob = $ob;
        
        
        $system = $ob->getSystemMailerSettings();
        $system->new_with_id = FALSE;
        $system->mail_smtpport = $newSystemPort;
        $system->mail_smtpserver = $newSystemServer;
        $system->saveSystem();
        
        $obRetrieved = new OutboundEmail();
        $obRetrieved->retrieve($userID);
        
        $this->assertEquals($newSystemPort, $obRetrieved->mail_smtpport, "Could not update users system-override accounts after system save.");
        $this->assertEquals($newSystemServer, $obRetrieved->mail_smtpserver, "Could not update users system-override accounts after system save.");
        $this->assertEquals("Test User", $obRetrieved->mail_smtpuser, "Could not update users system-override accounts after system save.");
        $this->assertEquals("User Pass", $obRetrieved->mail_smtppass, "Could not update users system-override accounts after system save.");
    }
    
    
    function testUserMailForSystemOverrideRetrieval()
    {
        $ob = new OutboundEmail();
        $ob->name = 'Sugar Test';
        $ob->type = 'system-override';
        $ob->user_id = $this->_user->id;
        $ob->mail_sendtype = "SMTP";
        $ob->mail_smtpuser = "Test User";
        $ob->save();
        $this->ob = $ob;
        
        $retrievedOb = $ob->getUsersMailerForSystemOverride($this->_user->id);
        $this->assertEquals($ob->name, $retrievedOb->name, "Could not retrieve users system override outbound email account");
        $this->assertEquals($ob->type, $retrievedOb->type, "Could not retrieve users system override outbound email account");
        $this->assertEquals($ob->user_id, $retrievedOb->user_id, "Could not retrieve users system override outbound email account");
        $this->assertEquals($ob->mail_sendtype, $retrievedOb->mail_sendtype, "Could not retrieve users system override outbound email account");
        $this->assertEquals("Test User", $retrievedOb->mail_smtpuser, "Could not retrieve users system override outbound email account");
    }
    
    function testDuplicateSystemAccountForUser()
    {
        $oe = new OutboundEmail();
        $userOverideAccont = $oe->createUserSystemOverrideAccount($this->_user->id, "TEST USER NAME", "TEST PASSWORD");
        $this->userOverideAccont = $userOverideAccont;
        $retrievedOb = $oe->getUsersMailerForSystemOverride($this->_user->id);
        
        $this->assertEquals("TEST USER NAME", $retrievedOb->mail_smtpuser, "Could not duplicate systems outbound account for user");
        $this->assertEquals($this->_user->id, $retrievedOb->user_id, "Could not duplicate systems outbound account for user");
        $this->assertEquals("TEST PASSWORD", $retrievedOb->mail_smtppass, "Could not duplicate systems outbound account for user");
        $this->assertEquals('system-override', $userOverideAccont->type, "Could not duplicate systems outbound account for user");
    }
    
    function testIsUserAlloweedAccessToSystemOutboundEmail()
    {
        $oe = new OutboundEmail();
        $GLOBALS['db']->query("DELETE FROM config WHERE category='notify' AND name='allow_default_outbound' ");
        $emptyTest = $oe->isAllowUserAccessToSystemDefaultOutbound();
        $this->assertFalse($emptyTest, "User alloweed access to system outbound email account error");
        
        $GLOBALS['db']->query("INSERT INTO config (category,name,value) VALUES ('notify','allow_default_outbound','2') ");
        $allowTest = $oe->isAllowUserAccessToSystemDefaultOutbound();
        $this->assertTrue($allowTest, "User alloweed access to system outbound email account error");
        
        $GLOBALS['db']->query("DELETE FROM config WHERE category='notify' AND name='allow_default_outbound' ");
        $emptyTest = $oe->isAllowUserAccessToSystemDefaultOutbound();
        $this->assertFalse($emptyTest, "User alloweed access to system outbound email account error");
           
    }
    
    
    function testIsUserAuthRequiredForOverrideAccount()
    {
        $oe = new OutboundEmail();
        
        $GLOBALS['db']->query("DELETE FROM config WHERE category='notify' AND name='allow_default_outbound' ");
        $system = $oe->getSystemMailerSettings();
        
        //System does not require auth, no user overide account.
        $system->mail_smtpauth_req = 0;
        $system->save(FALSE);
        
        $notRequired = $oe->doesUserOverrideAccountRequireCredentials($this->_user->id);
        $this->assertFalse($notRequired, "Test failed for determining if user auth required.");
        
        //System does require auth, no user overide account.
        $system->mail_smtpauth_req = 1;
        $system->save(FALSE);
        $notRequired = $oe->doesUserOverrideAccountRequireCredentials($this->_user->id);
        $this->assertTrue($notRequired, "Test failed for determining if user auth required.");
        
        //System requires auth and users alloweed to use sys defaults.
        $GLOBALS['db']->query("INSERT INTO config (category,name,value) VALUES ('notify','allow_default_outbound','2') ");
        $notRequired = $oe->doesUserOverrideAccountRequireCredentials($this->_user->id);
        $this->assertFalse($notRequired, "Test failed for determining if user auth required.");
        
        
        //System requires auth but user details are empty and users are not alloweed to use system details..
        $GLOBALS['db']->query("DELETE FROM config WHERE category='notify' AND name='allow_default_outbound' ");
        $userOverideAccont = $oe->createUserSystemOverrideAccount($this->_user->id, "", "");
        $this->userOverideAccont = $userOverideAccont;
        $notRequired = $oe->doesUserOverrideAccountRequireCredentials($this->_user->id);
        $this->assertTrue($notRequired, "Test failed for determining if user auth required.");
        
        //User has provided all credentials.
        $this->userOverideAccont->mail_smtpuser = "TEST USER NAME";
        $this->userOverideAccont->mail_smtppass = "TEST PASSWORD";
        $this->userOverideAccont->new_with_id = FALSE;
        $this->userOverideAccont->save();
        $notRequired = $oe->doesUserOverrideAccountRequireCredentials($this->_user->id);
        $this->assertFalse($notRequired, "Test failed for determining if user auth required.");
        
    }
    
}
?>