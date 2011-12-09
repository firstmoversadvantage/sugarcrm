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
require_once('modules/Meetings/Meeting.php');

/**
 * @ticket 39757
 */
class Bug39757Test extends Sugar_PHPUnit_Framework_TestCase 
{
	private $_meetingId;
	
	public function setUp() 
	{
		if ( $GLOBALS['db']->dbType != 'mysql' ) {
            $this->markTestSkipped('Only applies to MySQL');
		}
		$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
		$id = create_guid();
		$sql = "INSERT INTO meetings (id, date_start, duration_hours, duration_minutes, date_end, deleted) VALUES('{$id}', '2010-10-11 23:45:00', 0, 30, '2010-10-12', 0)";
		$GLOBALS['db']->query($sql);
		$this->_meetingId = $id;
	}
	
	public function tearDown() 
	{
	    $sql = "DELETE FROM MEETINGS WHERE id = '{$this->_meetingId}'";
	    $GLOBALS['db']->query($sql);
	    SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
	}
	
	
	public function testEndDateChange() 
	{	
	    $meetingsSql = "UPDATE meetings AS a INNER JOIN meetings AS b ON a.id = b.id SET a.date_end = date_add(b.date_start, INTERVAL + concat(b.duration_hours, b.duration_minutes) HOUR_MINUTE) WHERE a.id = '{$this->_meetingId}'";
		$GLOBALS['db']->query($meetingsSql);
		
		$meeting = new Meeting();
		$meeting->disable_row_level_security = true;
		$meeting->retrieve($this->_meetingId);
		$meeting->fixUpFormatting();
		$this->assertEquals($meeting->date_end, '2010-10-12 00:15:00', 'Ensuring that the end_date is saved properly as a date time field');
	}
}