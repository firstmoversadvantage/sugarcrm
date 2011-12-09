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

 
class Bug44522Test extends Sugar_PHPUnit_Framework_TestCase
{
    var $user;
    var $account;
    var $lead;
    var $contact;
    var $campaign;

    public function setUp()
    {
        //create user
        $this->user = $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();

        //create account
        $this->account = new Account();
        $this->account->name = 'bug44522 account '.date('Y-m-d-H-i-s');
        $this->account->save();
        
        //create campaign
        $this->campaign = SugarTestCampaignUtilities::createCampaign();

        //create contact
        $this->contact = new Contact();
        $this->lead = SugarTestLeadUtilities::createLead();
        
        $this->lead->campaign_id = $this->campaign->id;
        $this->lead->save();

    }
    
    public function tearDown()
    {
        //delete records created from db
        $GLOBALS['db']->query("DELETE FROM accounts WHERE id= '{$this->account->id}'");
        $GLOBALS['db']->query("DELETE FROM leads WHERE id= '{$this->lead->id}'");
        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->contact->id}'");
        $GLOBALS['db']->query("DELETE FROM campaigns WHERE id= '{$this->campaign->id}'");
        $GLOBALS['db']->query("DELETE FROM campaign_log WHERE campaign_id= '{$this->campaign->id}'");
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();

        //unset values
        unset($GLOBALS['current_user']);
        unset($this->user);
        unset($this->account);
        unset($this->contact);
        unset($this->lead);
        unset($this->campaign);
    }
    


    //run test to make sure there is an entry in campaign_log table for newly created contact during lead conversion (bug 44522)
    public function testConvertContactInCampaignLog()
    {
        //there will be output from display function, so call ob_start to trap it
        ob_start();

        $_POST = array();
        
        //set the request parameters and convert the lead
        $_REQUEST['module'] = 'Leads';
        $_REQUEST['action'] = 'ConvertLead';
        $_REQUEST['record'] = $this->lead->id;
        $_REQUEST['handle'] = 'save';
        $_REQUEST['selectedAccount'] = $this->account->id;

        //require view and call display class so that convert functionality is called
        require_once('modules/Leads/views/view.convertlead.php');
        $vc = new ViewConvertLead();
        $vc->display();

        //retrieve the lead again to make sure we have the latest converted lead in memory
        $this->lead->retrieve($this->lead->id);

        //retrieve the new contact id from the conversion
        $contact_id = $this->lead->contact_id;

        //throw error if contact id was not retrieved and exit test
        $this->assertTrue(!empty($contact_id), "contact id was not created during conversion process.  An error has ocurred, aborting rest of test.");
        if (empty($contact_id)){
            return;
        }
        //make sure the new contact has the account related and that it matches the lead account
        $query = "SELECT target_id FROM campaign_log WHERE campaign_id= '{$this->campaign->id}'";
        $result = $GLOBALS['db']->query($query);
        while($row = $GLOBALS['db']->fetchByAssoc($result)){
        $test_contact_id = $row['target_id'];
        }
        
        $this->assertEquals($contact_id, $test_contact_id);
        $output = ob_get_clean();
    }
}