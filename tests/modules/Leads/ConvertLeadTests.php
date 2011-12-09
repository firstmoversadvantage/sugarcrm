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


require_once 'modules/Leads/views/view.convertlead.php';


class ConvertLeadTests extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
    }
    
    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['app_list_strings']);
        unset($GLOBALS['current_user']);
    }
    
    /**
    * @group bug39787
    */
    public function testOpportunityNameValueFilled()
    {
        $lead = SugarTestLeadUtilities::createLead();
        $lead->opportunity_name = 'SBizzle Dollar Store';
        $lead->save();
        
        $_REQUEST['module'] = 'Leads';
        $_REQUEST['action'] = 'ConvertLead';
        $_REQUEST['record'] = $lead->id;
        
        // Check that the opportunity name doesn't get populated when it's not in the Leads editview layout
        require_once('include/MVC/Controller/ControllerFactory.php');
        require_once('include/MVC/View/ViewFactory.php');
        $GLOBALS['app']->controller = ControllerFactory::getController($_REQUEST['module']);
        ob_start();
        $GLOBALS['app']->controller->execute();
        $output = ob_get_clean();
        
        $matches_one = array();
        $pattern = '/SBizzle Dollar Store/';
        preg_match($pattern, $output, $matches_one);
        $this->assertTrue(count($matches_one) == 0, "Opportunity name got carried over to the Convert Leads page when it shouldn't have.");

        // Add the opportunity_name to the Leads EditView
        SugarTestStudioUtilities::addFieldToLayout('Leads', 'editview', 'opportunity_name');
        
        // Check that the opportunity name now DOES get populated now that it's in the Leads editview layout
        ob_start();
        $GLOBALS['app']->controller = ControllerFactory::getController($_REQUEST['module']);
        $GLOBALS['app']->controller->execute();
        $output = ob_get_clean();
        $matches_two = array();
        $pattern = '/SBizzle Dollar Store/';
        preg_match($pattern, $output, $matches_two);
        $this->assertTrue(count($matches_two) > 0, "Opportunity name did not carry over to the Convert Leads page when it should have.");
        
        SugarTestStudioUtilities::removeAllCreatedFields();
        unset($GLOBALS['app']->controller);
        unset($_REQUEST['module']);
        unset($_REQUEST['action']);
        unset($_REQUEST['record']);
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }

    /**
     * @group bug44033
     */
    public function testActivityMove() {
        // init
        $lead = SugarTestLeadUtilities::createLead();
        $contact = SugarTestContactUtilities::createContact();
        $meeting = SugarTestMeetingUtilities::createMeeting();
        SugarTestMeetingUtilities::addMeetingParent($meeting->id, $lead->id);
        $relation_id = SugarTestMeetingUtilities::addMeetingLeadRelation($meeting->id, $lead->id);
        $_REQUEST['record'] = $lead->id;

        // refresh the meeting to include parent_id and parent_type
        $meeting_id = $meeting->id;
        $meeting = new Meeting();
        $meeting->retrieve($meeting_id);

        // action: move meeting from lead to contact
        $convertObj = new TestViewConvertLead();
        $convertObj->moveActivityWrapper($meeting, $contact);

        // verification 1, parent id should be contact id
        $this->assertTrue($meeting->parent_id == $contact->id, 'Meeting parent id is not converted to contact id.');

        // verification 2, parent type should be "Contacts"
        $this->assertTrue($meeting->parent_type == 'Contacts', 'Meeting parent type is not converted to Contacts.');

        // verification 3, record should be deleted from meetings_leads table
        $sql = "select id from meetings_leads where meeting_id='{$meeting->id}' and lead_id='{$lead->id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse($row, "Meeting-Lead relationship is not removed.");

        // verification 4, record should be added to meetings_contacts table
        $sql = "select id from meetings_contacts where meeting_id='{$meeting->id}' and contact_id='{$contact->id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse(empty($row), "Meeting-Contact relationship is not added.");

        // clean up
        unset($_REQUEST['record']);
        $GLOBALS['db']->query("delete from meetings_contacts where meeting_id='{$meeting->id}' and contact_id= '{$contact->id}'");
        SugarTestMeetingUtilities::deleteMeetingLeadRelation($relation_id);
        SugarTestMeetingUtilities::removeMeetingContacts();
        SugarTestMeetingUtilities::removeAllCreatedMeetings();
        SugarTestContactUtilities::removeAllCreatedContacts();
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }

    public function testActivityCopy() {
        // init
        $lead = SugarTestLeadUtilities::createLead();
        $contact = SugarTestContactUtilities::createContact();
        $meeting = SugarTestMeetingUtilities::createMeeting();
        SugarTestMeetingUtilities::addMeetingParent($meeting->id, $lead->id);
        $relation_id = SugarTestMeetingUtilities::addMeetingLeadRelation($meeting->id, $lead->id);
        $_REQUEST['record'] = $lead->id;

        // refresh the meeting to include parent_id and parent_type
        $meeting_id = $meeting->id;
        $meeting = new Meeting();
        $meeting->retrieve($meeting_id);

        // action: copy meeting from lead to contact
        $convertObj = new TestViewConvertLead();
        $convertObj->copyActivityWrapper($meeting, $contact);

        // 1. the original meeting should still have the same parent_type and parent_id
        $meeting->retrieve($meeting_id);
        $this->assertEquals('Leads', $meeting->parent_type, 'parent_type of the original meeting was changed from Leads to '.$meeting->parent_type);
        $this->assertEquals($lead->id, $meeting->parent_id, 'parent_id of the original meeting was changed from '.$lead->id.' to '.$meeting->parent_id);

        // 2. a newly created meeting with parent type=Contatcs and parent_id=$contact->id
        $sql = "select id from meetings where parent_id='{$contact->id}' and parent_type= 'Contacts' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertNotNull($row, 'Could not find the newly created meeting with parent_type=Contacts and parent_id='.$contact->id);
        $new_meeting_id = '';
        if ($row) {
            $new_meeting_id = $row['id'];
        }

        // 3. record should not be deleted from meetings_leads table
        $sql = "select id from meetings_leads where meeting_id='{$meeting->id}' and lead_id='{$lead->id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertNotNull($row, "Meeting-Lead relationship was removed.");

        // 4. new meeting record should be added to meetings_contacts table
        $sql = "select id from meetings_contacts where meeting_id='{$new_meeting_id}' and contact_id='{$contact->id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse(empty($row), "Meeting-Contact relationship has not been added.");

        // clean up
        unset($_REQUEST['record']);
        $GLOBALS['db']->query("delete from meetings where parent_id='{$contact->id}' and parent_type= 'Contacts'");
        $GLOBALS['db']->query("delete from meetings where parent_id='{$lead->id}' and parent_type= 'Leads'");
        $GLOBALS['db']->query("delete from meetings_contacts where meeting_id='{$new_meeting_id}' and contact_id= '{$contact->id}'");
        SugarTestMeetingUtilities::deleteMeetingLeadRelation($relation_id);
        SugarTestMeetingUtilities::removeMeetingContacts();
        SugarTestMeetingUtilities::removeAllCreatedMeetings();
        SugarTestContactUtilities::removeAllCreatedContacts();
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }

    public function testConversionAndMoveActivities() {
        global $sugar_config;

        // init
        $lead = SugarTestLeadUtilities::createLead();
        $account = SugarTestAccountUtilities::createAccount();
        $meeting = SugarTestMeetingUtilities::createMeeting();
        SugarTestMeetingUtilities::addMeetingParent($meeting->id, $lead->id);
        $relation_id = SugarTestMeetingUtilities::addMeetingLeadRelation($meeting->id, $lead->id);
        $_REQUEST['record'] = $lead->id;

        // set the request/post parameters before converting the lead
        $_REQUEST['module'] = 'Leads';
        $_REQUEST['action'] = 'ConvertLead';
        $_REQUEST['record'] = $lead->id;
        $_REQUEST['handle'] = 'save';
        $_REQUEST['selectedAccount'] = $account->id;
        $sugar_config['lead_conv_activity_opt'] = 'move';
        $_POST['lead_conv_ac_op_sel'] = 'Contacts';

        // call display to trigger conversion
        $vc = new ViewConvertLead();
        $vc->display();

        // refresh meeting
        $meeting_id = $meeting->id;
        $meeting = new Meeting();
        $meeting->retrieve($meeting_id);

        // refresh lead
        $lead_id = $lead->id;
        $lead = new Lead();
        $lead->retrieve($lead_id);

        // retrieve the new contact id from the conversion
        $contact_id = $lead->contact_id;

        // 1. Lead's contact_id should not be null
        $this->assertNotNull($contact_id, 'Lead has null contact id after conversion.');

        // 2. Lead status should be 'Converted'
        $this->assertEquals('Converted', $lead->status, "Lead atatus should be 'Converted'.");

        // 3. new parent_type should be Contacts
        $this->assertEquals('Contacts', $meeting->parent_type, 'Meeting parent type has not been set to Contacts');

        // 4. new parent_id should be contact id
        $this->assertEquals($contact_id, $meeting->parent_id, 'Meeting parent id has not been set to contact id.');

        // 5. record should be deleted from meetings_leads table
        $sql = "select id from meetings_leads where meeting_id='{$meeting->id}' and lead_id='{$lead->id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse($row, "Meeting-Lead relationship is not removed.");

        // 6. record should be added to meetings_contacts table
        $sql = "select id from meetings_contacts where meeting_id='{$meeting->id}' and contact_id='{$contact_id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse(empty($row), "Meeting-Contact relationship is not added.");

        // clean up
        unset($_REQUEST['record']);
        $GLOBALS['db']->query("delete from meetings where parent_id='{$lead->id}' and parent_type= 'Leads'");
        $GLOBALS['db']->query("delete from meetings where parent_id='{$contact_id}' and parent_type= 'Contacts'");
        $GLOBALS['db']->query("delete from contacts where id='{$contact_id}'");
        $GLOBALS['db']->query("delete from meetings_contacts where meeting_id='{$meeting->id}' and contact_id= '{$contact_id}'");
        SugarTestMeetingUtilities::deleteMeetingLeadRelation($relation_id);
        SugarTestMeetingUtilities::removeMeetingContacts();
        SugarTestMeetingUtilities::removeAllCreatedMeetings();
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }

    public function testConversionAndCopyActivities() {
        global $sugar_config;

        // init
        $lead = SugarTestLeadUtilities::createLead();
        $account = SugarTestAccountUtilities::createAccount();
        $meeting = SugarTestMeetingUtilities::createMeeting();
        SugarTestMeetingUtilities::addMeetingParent($meeting->id, $lead->id);
        $relation_id = SugarTestMeetingUtilities::addMeetingLeadRelation($meeting->id, $lead->id);
        $_REQUEST['record'] = $lead->id;

        // set the request/post parameters before converting the lead
        $_REQUEST['module'] = 'Leads';
        $_REQUEST['action'] = 'ConvertLead';
        $_REQUEST['record'] = $lead->id;
        $_REQUEST['handle'] = 'save';
        $_REQUEST['selectedAccount'] = $account->id;
        $sugar_config['lead_conv_activity_opt'] = 'copy';
        $_POST['lead_conv_ac_op_sel'] = array('Contacts');

        // call display to trigger conversion
        $vc = new ViewConvertLead();
        $vc->display();

        // refresh meeting
        $meeting_id = $meeting->id;
        $meeting = new Meeting();
        $meeting->retrieve($meeting_id);

        // refresh lead
        $lead_id = $lead->id;
        $lead = new Lead();
        $lead->retrieve($lead_id);

        // retrieve the new contact id from the conversion
        $contact_id = $lead->contact_id;

        // 1. Lead's contact_id should not be null
        $this->assertNotNull($contact_id, 'Lead has null contact id after conversion.');

        // 2. Lead status should be 'Converted'
        $this->assertEquals('Converted', $lead->status, "Lead atatus should be 'Converted'.");

        // 3. parent_type of the original meeting should be Leads
        $this->assertEquals('Leads', $meeting->parent_type, 'Meeting parent should be Leads');

        // 4. parent_id of the original meeting should be contact id
        $this->assertEquals($lead_id, $meeting->parent_id, 'Meeting parent id should be lead id.');

        // 5. record should NOT be deleted from meetings_leads table
        $sql = "select id from meetings_leads where meeting_id='{$meeting->id}' and lead_id='{$lead->id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse(empty($row), "Meeting-Lead relationship is removed.");

        // 6. record should be added to meetings_contacts table
        $sql = "select meeting_id from meetings_contacts where contact_id='{$contact_id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse(empty($row), "Meeting-Contact relationship is not added.");

        // 7. the parent_type of the new meeting should be Contacts
        $new_meeting_id = $row['meeting_id'];
        $sql = "select id, parent_type, parent_id from meetings where id='{$new_meeting_id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse(empty($row), "New meeting is not added for contact.");
        $this->assertEquals('Contacts', $row['parent_type'], 'Parent type of the new meeting should be Contacts');

        // 8. the parent_id of the new meeting should be contact id
        $this->assertEquals($contact_id, $row['parent_id'], 'Parent id of the new meeting should be contact id.');

        // clean up
        unset($_REQUEST['record']);
        $GLOBALS['db']->query("delete from meetings where parent_id='{$lead->id}' and parent_type= 'Leads'");
        $GLOBALS['db']->query("delete from meetings where parent_id='{$contact_id}' and parent_type= 'Contacts'");
        $GLOBALS['db']->query("delete from contacts where id='{$contact_id}'");
        $GLOBALS['db']->query("delete from meetings_leads where meeting_id='{$meeting->id}' and lead_id= '{$lead_id}'");
        $GLOBALS['db']->query("delete from meetings_contacts where contact_id= '{$contact_id}'");
        SugarTestMeetingUtilities::deleteMeetingLeadRelation($relation_id);
        SugarTestMeetingUtilities::removeMeetingContacts();
        SugarTestMeetingUtilities::removeAllCreatedMeetings();
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }

    public function testConversionAndDoNothing() {
        global $sugar_config;

        // init
        $lead = SugarTestLeadUtilities::createLead();
        $account = SugarTestAccountUtilities::createAccount();
        $meeting = SugarTestMeetingUtilities::createMeeting();
        SugarTestMeetingUtilities::addMeetingParent($meeting->id, $lead->id);
        $relation_id = SugarTestMeetingUtilities::addMeetingLeadRelation($meeting->id, $lead->id);
        $_REQUEST['record'] = $lead->id;

        // set the request/post parameters before converting the lead
        $_REQUEST['module'] = 'Leads';
        $_REQUEST['action'] = 'ConvertLead';
        $_REQUEST['record'] = $lead->id;
        $_REQUEST['handle'] = 'save';
        $_REQUEST['selectedAccount'] = $account->id;
        $sugar_config['lead_conv_activity_opt'] = 'none';

        // call display to trigger conversion
        $vc = new ViewConvertLead();
        $vc->display();

        // refresh meeting
        $meeting_id = $meeting->id;
        $meeting = new Meeting();
        $meeting->retrieve($meeting_id);

        // refresh lead
        $lead_id = $lead->id;
        $lead = new Lead();
        $lead->retrieve($lead_id);

        // retrieve the new contact id from the conversion
        $contact_id = $lead->contact_id;

        // 1. Lead's contact_id should not be null
        $this->assertNotNull($contact_id, 'Lead has null contact id after conversion.');

        // 2. Lead status should be 'Converted'
        $this->assertEquals('Converted', $lead->status, "Lead atatus should be 'Converted'.");

        // 3. parent_type of the original meeting should be Leads
        $this->assertEquals('Leads', $meeting->parent_type, 'Meeting parent should be Leads');

        // 4. parent_id of the original meeting should be contact id
        $this->assertEquals($lead_id, $meeting->parent_id, 'Meeting parent id should be lead id.');

        // 5. record should NOT be deleted from meetings_leads table
        $sql = "select id from meetings_leads where meeting_id='{$meeting->id}' and lead_id='{$lead->id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse(empty($row), "Meeting-Lead relationship is removed.");

        // 6. record should NOT be added to meetings_contacts table
        $sql = "select meeting_id from meetings_contacts where contact_id='{$contact_id}' and deleted=0";
        $result = $GLOBALS['db']->query($sql);
        $row = $GLOBALS['db']->fetchByAssoc($result);
        $this->assertFalse($row, "Meeting-Contact relationship should not be added.");

        // clean up
        unset($_REQUEST['record']);
        $GLOBALS['db']->query("delete from meetings where parent_id='{$lead->id}' and parent_type= 'Leads'");
        $GLOBALS['db']->query("delete from meetings where parent_id='{$contact_id}' and parent_type= 'Contacts'");
        $GLOBALS['db']->query("delete from contacts where id='{$contact_id}'");
        $GLOBALS['db']->query("delete from meetings_leads where meeting_id='{$meeting->id}' and lead_id= '{$lead_id}'");
        $GLOBALS['db']->query("delete from meetings_contacts where contact_id= '{$contact_id}'");
        SugarTestMeetingUtilities::deleteMeetingLeadRelation($relation_id);
        SugarTestMeetingUtilities::removeMeetingContacts();
        SugarTestMeetingUtilities::removeAllCreatedMeetings();
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }

    public function testMeetingsUsersRelationships()
    {
        global $current_user;

        $bean = SugarTestMeetingUtilities::createMeeting();
        $convert_lead = SugarTestViewConvertLeadUtilities::createViewConvertLead();

        if ($bean->object_name == "Meeting")
        {
            $convert_lead->setMeetingsUsersRelationship($bean);
        }

        $this->assertTrue(is_object($bean->users), "Relationship wasn't set.");

        SugarTestMeetingUtilities::removeMeetingUsers();
        SugarTestMeetingUtilities::removeAllCreatedMeetings();
    }
}

class TestViewConvertLead extends ViewConvertLead
{
    public function moveActivityWrapper($activity, $bean) {
        parent::moveActivity($activity, $bean);
    }

    public function copyActivityWrapper($activity, $bean) {
        parent::copyActivityAndRelateToBean($activity, $bean);
    }
}
