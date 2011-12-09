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
 * @ticket 32487
 */
class Bug32487Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $ib = null;
	var $outbound_id = null;
	
	public function setUp()
    {
        global $current_user, $currentModule ;
		$mod_strings = return_module_language($GLOBALS['current_language'], "Contacts");
		$current_user = SugarTestUserUtilities::createAnonymousUser();
		$this->outbound_id = uniqid();
		$time = date('Y-m-d H:i:s');

		$ib = new InboundEmail();
		$ib->is_personal = 1;
		$ib->name = "Test";
		$ib->port = 3309;
		$ib->mailbox = 'empty';
		$ib->created_by = $current_user->id;
		$ib->email_password = "pass";
		$ib->protocol = 'IMAP';
		$stored_options['outbound_email'] = $this->outbound_id;
	    $ib->stored_options = base64_encode(serialize($stored_options));
	    $ib->save();
	    $this->ib = $ib;
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        
        $GLOBALS['db']->query("DELETE FROM inbound_email WHERE id= '{$this->ib->id}'");
        unset($GLOBALS['mod_strings']);
        unset($this->ib);
    }
    
	function testGetAssoicatedInboundAccountForOutboundAccounts(){
	    global $current_user;
	    $ob = new OutboundEmail();
	    $ob->id = $this->outbound_id;
		
	    $results = $ob->getAssociatedInboundAccounts($current_user);
    	$this->assertEquals($this->ib->id, $results[0], "Could not retrieve the inbound mail accounts for an outbound account");
    	
    	$obEmpty = new OutboundEmail();
    	$obEmpty->id = uniqid();
		
	    $empty_results = $obEmpty->getAssociatedInboundAccounts($current_user);
    	$this->assertEquals(0, count($empty_results), "Outbound email account returned for unspecified/empty inbound mail account.");
    }
}
?>