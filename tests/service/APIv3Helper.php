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

//Helper functions used by both SOAP and REST Unit Test Calls.

class APIv3Helper
{
    
    function populateSeedDataForSearchTest($user_id)
    {
        $results = array();
        $a1_id = create_guid();
        $a1 = new Account();
        $a1->id = $a1_id;
        $a1->new_with_id = TRUE;
        $a1->name = "UNIT TEST $a1_id";
        $a1->assigned_user_id = $user_id;
        $a1->save();
        $results[] = array('id' => $a1_id, 'fieldName' => 'name', 'fieldValue' => "UNIT TEST $a1_id");
        
        $a2_id = create_guid();
        $a2 = new Account();
        $a2->new_with_id = TRUE;
        $a2->id = $a2_id;
        $a2->name = "UNIT TEST $a2_id";
        $a2->assigned_user_id = 'unittest';
        $a2->save();
        $results[] = array('id' => $a2_id, 'fieldName' => 'name', 'fieldValue' => "UNIT TEST $a2_id");
        
        $c1_id = create_guid();
        $c1 = new Contact();
        $c1->id = $c1_id;
        $c1->new_with_id = TRUE;
        $c1->first_name = "UNIT TEST";
        $c1->last_name = "UNIT_TEST";
        $c1->assigned_user_id = $user_id;
        $c1->save();
        $results[] = array('id' => $c1_id, 'fieldName' => 'name', 'fieldValue' => $c1->first_name .' ' . $c1->last_name);
        
        $op1_id = create_guid();
        $op1 = new Opportunity();
        $op1->new_with_id = TRUE;
        $op1->id = $op1_id;
        $op1->name = "UNIT TEST $op1_id";
        $op1->assigned_user_id = $user_id;
        $op1->save();
        $results[] = array('id' => $op1_id, 'fieldName' => 'name', 'fieldValue' => "UNIT TEST $op1_id");
        
        $op2_id = create_guid();
        $op2 = new Opportunity();
        $op2->new_with_id = TRUE;
        $op2->id = $op2_id;
        $op2->name = "UNIT TEST $op2_id";
        $op2->assigned_user_id = 'unittest';
        $op2->save();
        $results[] = array('id' => $op2_id, 'fieldName' => 'name', 'fieldValue' => "UNIT TEST $op2_id");
        
        return $results;
    }    
    
    /**
     * Linear search function used to find a bean id in an entry list array.
     *
     * @param array $list
     * @param string $bean_id
     */
    function findBeanIdFromEntryList($list,$bean_id,$module)
    {
        $found = FALSE;
        foreach ($list as $moduleEntry)
        {
            if($moduleEntry['name'] == $module)
            {
                foreach ($moduleEntry['records'] as $entry)
                {
                    foreach ($entry as $fieldEntry)
                    {
                        if($fieldEntry['name'] == 'id' && $fieldEntry['value'] == $bean_id )
                            return TRUE;
                    }
                }
            }
        }
        
        return $found;
    }
    
    /**
     * Linear search function used to find a particular field in an entry list array.
     *
     * @param array $list
     * @param string $bean_id
     */
    function findFieldByNameFromEntryList($list,$bean_id,$module,$fieldName)
    {
        $found = FALSE;

        foreach ($list as $moduleEntry)
        {
            if($moduleEntry['name'] == $module)
            {
                foreach ($moduleEntry['records'] as $entry)
                {
                    $value = $this->_retrieveFieldValueByFieldName($entry, $fieldName,$bean_id);
                    if($value !== FALSE)
                        return $value;
                }
            }
        }
        
        return $found;
    }
    
    function _retrieveFieldValueByFieldName($entry, $fieldName, $beanId)
    {
        $found = FALSE;
        $fieldValue = FALSE;
        foreach ($entry as $fieldEntry)
        {
            if($fieldEntry['name'] == 'id' && $fieldEntry['value'] == $beanId )
                $found = TRUE;
                
            if($fieldEntry['name'] == $fieldName )
                $fieldValue = $fieldEntry['value'];
        }
        
        if($found)
            return $fieldValue;
        else 
            return FALSE;
    }
}