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

 
require_once 'include/database/DBManagerFactory.php';

class RepairDatabaseTest extends Sugar_PHPUnit_Framework_TestCase
{

var $db;	
	
public function setUp()
{
	
	$this->markTestSkipped('Skip for now');	
    $this->db = DBManagerFactory::getInstance();	
    if($this->db->dbType == 'mysql')
    {
       $sql =  'ALTER TABLE meetings ALTER COLUMN status SET DEFAULT NULL';
       $sql2 = 'ALTER TABLE calls ALTER COLUMN status SET DEFAULT NULL';
       $sql3 = 'ALTER TABLE tasks ALTER COLUMN status SET DEFAULT NULL';

	   //Run the SQL
	   $this->db->query($sql);  
	   $this->db->query($sql2);  
	   $this->db->query($sql3);       
    }
    
         
}	

public function tearDown()
{
	if($this->db->dbType == 'mysql')
    {	
    	$sql = "ALTER TABLE meetings ALTER COLUMN status SET DEFAULT 'Planned'";
    	$sql2 = "ALTER TABLE calls ALTER COLUMN status SET DEFAULT 'Planned'";
    	$sql3 = "ALTER TABLE tasks ALTER COLUMN status SET DEFAULT 'Not Started'";
	    //Run the SQL
	    $this->db->query($sql);
	    $this->db->query($sql2); 
	    $this->db->query($sql3);      	
    }   	
}

public function testRepairTableParams()
{
	    if($this->db->dbType != 'mysql')
	    {
	       $this->markTestSkipped('Skip if not mysql db');
	       return;	
	    }
	
	    $bean = new Meeting();
	    $result = $this->getRepairTableParamsResult($bean);
	    $this->assertRegExp('/ALTER TABLE meetings\s+?modify column status varchar\(100\)  DEFAULT \'Planned\' NULL/i', $result);
	    
	    /*
	    $bean = new Call();
	    $result = $this->getRepairTableParamsResult($bean);
	    $this->assertTrue(!empty($result));
	    $this->assertRegExp('/ALTER TABLE calls\s+?modify column status varchar\(100\)  DEFAULT \'Planned\' NULL/i', $result);
	    */
	    
	    $bean = new Task();
	    $result = $this->getRepairTableParamsResult($bean);
	    $this->assertTrue(!empty($result));	    
	    $this->assertRegExp('/ALTER TABLE tasks\s+?modify column status varchar\(100\)  DEFAULT \'Not Started\' NULL/i', $result);
 
}

private function getRepairTableParamsResult($bean)
{
        $indices   = $bean->getIndices();
        $fielddefs = $bean->getFieldDefinitions();
        $tablename = $bean->getTableName();

		//Clean the indicies to prevent duplicate definitions
		$new_indices = array();
		foreach($indices as $ind_def)
		{
			$new_indices[$ind_def['name']] = $ind_def;
		}
		
        global $dictionary;
        $engine=null;
        if (isset($dictionary[$bean->getObjectName()]['engine']) && !empty($dictionary[$bean->getObjectName()]['engine']) )
        {
            $engine = $dictionary[$bean->getObjectName()]['engine'];	
        }
        
        
	    $result = $this->db->repairTableParams($bean->table_name, $fielddefs, $new_indices, false, $engine);
	    return $result;	
}
	
}