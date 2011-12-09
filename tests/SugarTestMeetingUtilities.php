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

require_once 'modules/Meetings/Meeting.php';

class SugarTestMeetingUtilities
{
    private static $_createdMeetings = array();

    private function __construct() {}

    public static function createMeeting($id = '')
    {
        $time = mt_rand();
        $name = 'Meeting';
        $meeting = new Meeting();
        $meeting->name = $name . $time;
        if(!empty($id))
        {
            $meeting->new_with_id = true;
            $meeting->id = $id;
        }
        $meeting->save();
        self::$_createdMeetings[] = $meeting;
        return $meeting;
    }

    public static function removeAllCreatedMeetings() 
    {
        $meeting_ids = self::getCreatedMeetingIds();
        $GLOBALS['db']->query(sprintf("DELETE FROM meetings WHERE id IN ('%s')", implode("', '", $meeting_ids)));
    }
    
    public static function removeMeetingContacts()
    {
        $meeting_ids = self::getCreatedMeetingIds();
        $GLOBALS['db']->query(sprintf("DELETE FROM meetings_contacts WHERE meeting_id IN ('%s')", implode("', '", $meeting_ids)));
    }
    
    public static function addMeetingLeadRelation($meeting_id, $lead_id) {
        $id = create_guid();
        $GLOBALS['db']->query("INSERT INTO meetings_leads (id, meeting_id, lead_id) values ('{$id}', '{$meeting_id}', '{$lead_id}')");
        return $id;
    }

    public static function deleteMeetingLeadRelation($id) {
        $GLOBALS['db']->query("delete from meetings_leads where id='{$id}'");
    }


    public static function addMeetingParent($meeting_id, $lead_id) {
        $sql = "update meetings set parent_type='Leads', parent_id='{$lead_id}' where id='{$meeting_id}'";
        $GLOBALS['db']->query($sql);
    }

    public static function removeMeetingUsers()
    {
        $meeting_ids = self::getCreatedMeetingIds();
        $GLOBALS['db']->query(sprintf("DELETE FROM meetings_users WHERE meeting_id IN ('%s')", implode("', '", $meeting_ids)));
    }

    public static function getCreatedMeetingIds()
    {
        $meeting_ids = array();
        foreach (self::$_createdMeetings as $meeting)
        {
            $meeting_ids[] = $meeting->id;
        }
        return $meeting_ids;
    }
}
?>