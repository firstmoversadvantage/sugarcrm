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

require_once 'include/TimeDate.php';
require_once 'modules/Calendar/Calendar.php';
require_once 'modules/Meetings/Meeting.php';

/**
 * @ticket 20626
 */
class Bug20626Test extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    	$GLOBALS['reload_vardefs'] = true;
        global $current_user;

        $current_user = SugarTestUserUtilities::createAnonymousUser();
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        $GLOBALS['reload_vardefs'] = false;
    }

    public function testDateAndTimeShownInCalendarActivityAdditionalDetailsPopup()
    {
        global $timedate,$sugar_config,$DO_USER_TIME_OFFSET , $current_user;

        $DO_USER_TIME_OFFSET = true;
        $timedate = TimeDate::getInstance();

        $meeting = new Meeting();
        $format = $current_user->getUserDateTimePreferences();
        $meeting->date_start = $timedate->swap_formats("2006-12-23 11:00pm" , 'Y-m-d h:ia', $format['date'].' '.$format['time']);
        $meeting->time_start = "";
        $meeting->object_name = "Meeting";
        $meeting->duration_hours = 2;
        $ca = new CalendarActivity($meeting);
        $this->assertEquals($meeting->date_start , $ca->sugar_bean->date_start);
    }
}