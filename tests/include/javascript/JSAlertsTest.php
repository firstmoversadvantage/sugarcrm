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

 
require_once 'include/javascript/jsAlerts.php';

class JSAlertsTest extends Sugar_PHPUnit_Framework_TestCase
{
    var $beans;

    public function setUp()
    {
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;

        global $current_user;
        $this->beans = array();
        $this->old_user = $current_user;
        $current_user = $this->_user = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
    }
    
    public function tearDown()
    {
        foreach($this->beans as $bean) {
            $bean->mark_deleted($bean->id);
        }
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();

		unset($GLOBALS['app_list_strings']);
		unset($GLOBALS['current_user']);
		unset($GLOBALS['app_strings']);
        unset($GLOBALS['beanList']);
        unset($GLOBALS['beanFiles']);
    }

    protected function createNewMeeting()
    {
        $m = new Meeting();
        $m->name = "40541TestMeeting";
        $m->date_start = gmdate($GLOBALS['timedate']->get_db_date_time_format(), time() + 3000);
        $m->duration_hours = 0;
        $m->duration_minutes = 15;
        $m->reminder_time = 60;
        $m->reminder_checked = true;
        $m->save();
        $m->load_relationship("users");
        $m->users->add($this->_user->id);
        $this->beans[] = $m;
        return $m;
    }

    public function testGetAlertsForUser()
    {

        global $app_list_strings;
            $app_list_strings['reminder_max_time'] = 5000;
        $m = $this->createNewMeeting();
        $alerts = new jsAlerts();
        $script = $alerts->getScript();
        $this->assertRegExp("/addAlert.*\"{$m->name}\"/", $script);
    }

    public function testGetDeclinedAlertsForUser()
    {

        global $app_list_strings;
            $app_list_strings['reminder_max_time'] = 5000;
        $m = $this->createNewMeeting();
        //Decline the meeting
        $query = "UPDATE meetings_users SET deleted = 0, accept_status = 'decline' " .
    			 "WHERE meeting_id = '$m->id' AND user_id = '{$this->_user->id}'";
    	$m->db->query($query);
        $alerts = new jsAlerts();
        $script = $alerts->getScript();
        $this->assertNotRegExp("/addAlert.*\"{$m->name}\"/", $script);
    }
}
