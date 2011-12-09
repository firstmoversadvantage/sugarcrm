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

 
require_once 'modules/Schedulers/Scheduler.php';

class Bug44215Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $testScheduler;
	
	public function setUp()
    {
	    unset($GLOBALS['disable_date_format']);
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
	    $GLOBALS['current_user']->setPreference('datef', "m/d/Y");
		$GLOBALS['current_user']->setPreference('timef', "h:ia");
		$GLOBALS['current_user']->setPreference('timezone', "America/Los_Angeles");    	
        $this->testScheduler = new Bug44215MockTestScheduler();
        $this->testScheduler->save(); 
    }

    public function tearDown()
    {
	    SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);    
        $GLOBALS['db']->query("DELETE FROM schedulers WHERE id = '" . $this->testScheduler->id . "'");
        $GLOBALS['db']->query("DELETE FROM schedulers_times WHERE scheduler_id = '" . $this->testScheduler->id . "'");
    }

    
    public function testFlushDeadJobs()
    {
		$this->testScheduler->fire();
		$this->testScheduler->status = 'In Progress';
		$this->testScheduler->save();
 		$this->assertEquals('In Progress', $this->testScheduler->status, "Assert that the test scheduler instance has status of 'In Progress'");
 		
 		$result = $GLOBALS['db']->query("SELECT id FROM schedulers_times WHERE scheduler_id ='{$this->testScheduler->id}'");
 		$jobCount = 0;
 		
 		while($row = $GLOBALS['db']->fetchByAssoc($result)) 
 		{
 			$job = new SchedulersJob();
 			$job->retrieve($row['id']);
 			
 			$this->assertEquals('completed', $job->status, "Assert that schedulers_times status is set to 'completed'");
 			
 			$job->execute_time = $this->testScheduler->date_time_start; //Set this to the start time of the scheduler which is in year 2005
 			$job->save();
 			$jobCount++;
 		}
 		
 		$this->assertTrue($jobCount > 0, "Assert that we created schedulers_times entries");
		$this->testScheduler->flushDeadJobs();
		
		$this->testScheduler->retrieve($this->testScheduler->id);
		$this->assertEquals('Active', $this->testScheduler->status, "Assert that the status for scheduler is set to 'Active'");
		
        $result = $GLOBALS['db']->query("SELECT id FROM schedulers_times WHERE scheduler_id ='{$this->testScheduler->id}'");

 		while($row = $GLOBALS['db']->fetchByAssoc($result)) 
 		{
 			$job = new SchedulersJob();
 			$job->retrieve($row['id']);	
 			$this->assertEquals('failed', $job->status, "Assert that schedulers_times status is set to 'failed'");
 		}		
		
    }


}

function Bug44215TestFunction()
{
	//Could do something here, but don't need to
	return true;
}

//Mock Scheduler bean for the test scheduler
class Bug44215MockTestScheduler extends Scheduler
{
    public $fired = false;
    public $name = "Bug44215MockTestScheduler";
    public $date_time_start = '2005-01-01 19:00:00';
    public $job_interval = '*::*::*::*::*';
    public $job = 'function::Bug44215TestFunction';
}