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

 
class SugarTestTrackerUtility
{
    private static $_trackerSettings = array();
    private static $_monitorId = '';
    
    private function __construct() {}
    
    public static function setup()
    {
        require('modules/Trackers/config.php');
        foreach($tracker_config as $entry) {
            if(isset($entry['bean'])) {
                $GLOBALS['tracker_' . $entry['name']] = false;
            } //if
        } //foreach
        
        $result = $GLOBALS['db']->query("SELECT category, name, value from config WHERE category = 'tracker' and name != 'prune_interval'");
        while($row = $GLOBALS['db']->fetchByAssoc($result)){
            self::$_trackerSettings[$row['name']] = $row['value'];
            $GLOBALS['db']->query("DELETE FROM config WHERE category = 'tracker' AND name = '{$row['name']}'");
        }
    }
    
    public static function restore()
    {
        foreach(self::$_trackerSettings as $name=>$value) {
            $GLOBALS['db']->query("INSERT INTO config (category, name, value) VALUES ('tracker', '{$name}', '{$value}')");
        }
    }
    
    public static function insertTrackerEntry($bean, $action)
    {
        require_once('modules/Trackers/TrackerManager.php');
        $trackerManager = TrackerManager::getInstance();
        $timeStamp = gmdate($GLOBALS['timedate']->get_db_date_time_format());
        $_REQUEST['action'] = $action;
        if($monitor = $trackerManager->getMonitor('tracker'))
        {
            $monitor->setValue('action', $action);
            $monitor->setValue('user_id', $GLOBALS['current_user']->id);
            $monitor->setValue('module_name', $bean->module_dir);
            $monitor->setValue('date_modified', $timeStamp);
            $monitor->setValue('visible', (($action == 'detailview') || ($action == 'editview')
                                            ) ? 1 : 0);

            if (!empty($bean->id))
            {
                $monitor->setValue('item_id', $bean->id);
                $monitor->setValue('item_summary', $bean->get_summary_text());
            }

            //If visible is true, but there is no bean, do not track (invalid/unauthorized reference)
            //Also, do not track save actions where there is no bean id
            if($monitor->visible && empty($bean->id))
            {
               $trackerManager->unsetMonitor($monitor);
               return false;
            }
            $trackerManager->saveMonitor($monitor, true, true);
            if(empty(self::$_monitorId))
            {
                self::$_monitorId = $monitor->monitor_id;
            }
        }
    }
    
    public static function removeAllTrackerEntries()
    {
        if(!empty(self::$_monitorId))
        {
            $GLOBALS['db']->query("DELETE FROM tracker WHERE monitor_id = '".self::$_monitorId."'");
        }
    }
}
?>
