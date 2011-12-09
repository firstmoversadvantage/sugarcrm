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

 
require_once 'modules/Emails/Email.php';

class SugarTestEmailUtilities
{
    private static $_createdEmails = array();

    private function __construct() {}

    public static function createEmail($id = '', $override = array()) 
    {
        global $timedate;
        
        $time = mt_rand();
    	$name = 'SugarEmail';
    	$email = new Email();
        $email->name = $name . $time;
        $email->type = 'out';
        $email->status = 'sent';
        $email->date_sent = $timedate->to_display_date_time(gmdate("Y-m-d H:i:s", (gmmktime() - (3600 * 24 * 2) ))) ; // Two days ago
        if(!empty($id))
        {
            $email->new_with_id = true;
            $email->id = $id;
        }
        foreach($override as $key => $value)
        {
            $email->$key = $value;
        }
        $email->save();
        if(!empty($override['parent_id']) && !empty($override['parent_type']))
        {
            self::createEmailsBeansRelationship($email->id, $override['parent_type'], $override['parent_id']);
        }
        self::$_createdEmails[] = $email;
        return $email;
    }

    public static function removeAllCreatedEmails() 
    {
        $email_ids = self::getCreatedEmailIds();
        $GLOBALS['db']->query('DELETE FROM emails WHERE id IN (\'' . implode("', '", $email_ids) . '\')');
        self::removeCreatedEmailBeansRelationships();
    }
    
    private static function createEmailsBeansRelationship($email_id, $parent_type, $parent_id)
    {
        $unique_id = create_guid();
        $GLOBALS['db']->query("INSERT INTO emails_beans ( id, email_id, bean_id, bean_module, date_modified, deleted ) ".
							  "VALUES ( '{$unique_id}', '{$email_id}', '{$parent_id}', '{$parent_type}', '".gmdate('Y-m-d H:i:s')."', 0)");
    }
    
    private static function removeCreatedEmailBeansRelationships(){
    	$email_ids = self::getCreatedEmailIds();
        $GLOBALS['db']->query('DELETE FROM emails_beans WHERE email_id IN (\'' . implode("', '", $email_ids) . '\')');
    }
    
    public static function getCreatedEmailIds() 
    {
        $email_ids = array();
        foreach (self::$_createdEmails as $email) {
            $email_ids[] = $email->id;
        }
        return $email_ids;
    }
}
?>