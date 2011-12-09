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

 
require_once('modules/UpgradeWizard/uw_utils.php');		

class UWUtilsTest extends Sugar_PHPUnit_Framework_TestCase  {

var $meeting;	
var $call;
var $original_current_user;

function setUp() 
{
	global $db, $timedate, $current_user;
	
	
	$this->original_current_user = $current_user;
	$user = new User();
	$user->retrieve('1');
	$current_user = $user;
	
	if($db->dbType != 'mysql')
	{
		$this->markTestSkipped('Skipping for non-mysql dbs');
	}	
	
	$this->meeting = SugarTestMeetingUtilities::createMeeting();
	$date_start = $timedate->nowDb();
	$this->meeting->date_start = $date_start;
	$this->meeting->duration_hours = 2;
	$this->meeting->duration_minutes = 30; 
	$this->meeting->save();
	
	$sql = "UPDATE meetings SET date_end = '{$date_start}' WHERE id = '{$this->meeting->id}'";
	$db->query($sql);
	
	$this->call = SugarTestCallUtilities::createCall();
	$date_start = $timedate->nowDb();
	$this->call->date_start = $date_start;
	$this->call->duration_hours = 2;
	$this->call->duration_minutes = 30; 
	$this->call->save();	
	
	$sql = "UPDATE calls SET date_end = '{$date_start}' WHERE id = '{$this->call->id}'";
	$db->query($sql);	
}

function tearDown() {
	global $db, $current_user;

    if($db->dbType != 'mysql') return; // No need to clean up if we skipped the test to begin with



    SugarTestMeetingUtilities::removeAllCreatedMeetings();	
	SugarTestCallUtilities::removeAllCreatedCalls();	
    
	$this->meeting = null;
	$this->call = null;
	
	$meetingsSql = "UPDATE meetings SET date_end = date_add(date_start, INTERVAL - CONCAT(duration_hours, ':', duration_minutes) HOUR_MINUTE)";
	$callsSql = "UPDATE calls SET date_end = date_add(date_start, INTERVAL - CONCAT(duration_hours, ':', duration_minutes) HOUR_MINUTE)";	
	
	$db->query($meetingsSql);
	$db->query($callsSql);
	
	$current_user = $this->original_current_user;
}

function testUpgradeDateTimeFields() {		

	upgradeDateTimeFields();

	global $db;
	$query = "SELECT date_start, date_end FROM meetings WHERE id = '{$this->meeting->id}'";
	$result = $db->query($query);
	$row = $db->fetchByAssoc($result);
	$start_time = strtotime($row['date_start']);
	$end_time = strtotime($row['date_end']);
	$this->assertEquals(($end_time - $start_time), (2.5 * 60 * 60), 'Assert that date_end in meetings table has been properly converted');	
	
	$query = "SELECT date_start, date_end FROM calls WHERE id = '{$this->call->id}'";
	$result = $db->query($query);
	$row = $db->fetchByAssoc($result);
	$start_time = strtotime($row['date_start']);
	$end_time = strtotime($row['date_end']);
	$this->assertEquals(($end_time - $start_time), (2.5 * 60 * 60), 'Assert that date_end in calls table has been properly converted');	
}


}

?>