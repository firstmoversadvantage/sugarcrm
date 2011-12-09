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

 
require_once 'modules/Leads/Lead.php';

class SugarTestLeadUtilities
{
    private static $_createdLeads = array();

    private function __construct() {}

    public static function createLead($id = '') 
    {
        $time = mt_rand();
    	$first_name = 'SugarLeadFirst';
    	$last_name = 'SugarLeadLast';
    	$email1 = 'lead@sugar.com';
    	$lead = new Lead();
        $lead->first_name = $first_name . $time;
        $lead->last_name = $last_name ;
        $lead->email1 = 'lead@'. $time. 'sugar.com';
        if(!empty($id))
        {
            $lead->new_with_id = true;
            $lead->id = $id;
        }
        $lead->save();
        self::$_createdLeads[] = $lead;
        return $lead;
    }

    public static function setCreatedLead($lead_ids) {
    	foreach($lead_ids as $lead_id) {
    		$lead = new Lead();
    		$lead->id = $lead_id;
        	self::$_createdLeads[] = $lead;
    	} // foreach
    } // fn
    
    public static function removeAllCreatedLeads() 
    {
        $lead_ids = self::getCreatedLeadIds();
        $GLOBALS['db']->query('DELETE FROM leads WHERE id IN (\'' . implode("', '", $lead_ids) . '\')');
    }
    
    public static function removeCreatedLeadsUsersRelationships(){
    	$lead_ids = self::getCreatedLeadIds();
        $GLOBALS['db']->query('DELETE FROM leads_users WHERE lead_id IN (\'' . implode("', '", $lead_ids) . '\')');
    }
    
    public static function getCreatedLeadIds() 
    {
        $lead_ids = array();
        foreach (self::$_createdLeads as $lead) {
            $lead_ids[] = $lead->id;
        }
        return $lead_ids;
    }
}
?>