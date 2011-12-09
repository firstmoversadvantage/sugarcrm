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

 
require_once 'modules/Users/User.php';

class SugarTestUserUtilities
{
    private static $_createdUsers = array();

    private function __construct() {}
    
    public function __destruct()
    {
        self::removeAllCreatedAnonymousUsers();
    }

    public static function createAnonymousUser($save = true, $is_admin=0)
    {
        if (isset($_REQUEST['action'])) { 
        unset($_REQUEST['action']);
        }
        
        $time = mt_rand();
    	$userId = 'SugarUser';
    	$user = new User();
        $user->user_name = $userId . $time;
        $user->user_hash = md5($userId.$time);
        $user->first_name = $userId;
        $user->last_name = $time;
        $user->status='Active';
        if ($is_admin) {
            $user->is_admin = 1;
        }
        if ( $save ) {
            $user->save();
        }

        $user->fill_in_additional_detail_fields();
        self::$_createdUsers[] = $user;
        return $user;
    }
    
    public function removeAllCreatedAnonymousUsers() 
    {
        $user_ids = self::getCreatedUserIds();
        if ( count($user_ids) > 0 ) {
            $GLOBALS['db']->query('DELETE FROM users WHERE id IN (\'' . implode("', '", $user_ids) . '\')');
            $GLOBALS['db']->query('DELETE FROM user_preferences WHERE assigned_user_id IN (\'' . implode("', '", $user_ids) . '\')');
        }
        self::$_createdUsers = array();
    }
    
    public static function getCreatedUserIds() 
    {
        $user_ids = array();
        foreach (self::$_createdUsers as $user)
            if ( is_object($user) && $user instanceOf User )
                $user_ids[] = $user->id;
        
        return $user_ids;
    }
}