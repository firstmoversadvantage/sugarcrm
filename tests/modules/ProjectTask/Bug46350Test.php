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




require_once "modules/ProjectTask/ProjectTask.php";
require_once "modules/Project/Project.php";

/**
 * Created: Sep 21, 2011
 */
class Bug46350Test extends Sugar_PHPUnit_Framework_TestCase
{
	public $project;
	public $projectTasks = array ();

	/**
	 * Different values, nevermind, 0-100
	 */
	public $oldPercentValue = 34;
	public $newPercentValue = 33;
	public $defaultStaticSecondPercent = 56;
	/**
	 *
	 */

    private $_user;

	public function setUp()
	{
		$this->_user = SugarTestUserUtilities::createAnonymousUser();
		$GLOBALS['current_user'] = $this->_user;
		$this->project = SugarTestProjectUtilities::createProject();
		$projectId = $this->project->id;
		$projectTasksData = array (
			'parentTask' => array (
				'project_id' => $projectId,
				'parent_task_id' => '',
				'project_task_id' => 1,
				'percent_complete' => $this->countAverage(array ($this->oldPercentValue, $this->defaultStaticSecondPercent)),
				'name' => 'Task 1',
			),
			'firstChildTask' => array (
				'project_id' => $projectId,
				'parent_task_id' => 1,
				'project_task_id' => 2,
				'percent_complete' => $this->oldPercentValue,
				'name' => 'Task 2',
			),
			'secondChildTask' => array (
				'project_id' => $projectId,
				'parent_task_id' => 1,
				'project_task_id' => 3,
				'percent_complete' => $this->defaultStaticSecondPercent,
				'name' => 'Task 3',
			),
		);
		foreach ($projectTasksData as $key => $value)
		{
			$this->projectTasks[$key] = SugarTestProjectTaskUtilities::createProjectTask($value);
		}
	}

	public function tearDown()
	{
		SugarTestProjectUtilities::removeAllCreatedProjects();
		SugarTestProjectTaskUtilities::removeAllCreatedProjectTasks();
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
		unset($this->project);
		unset($this->projectTasks);
        unset($this->_user);
		unset($GLOBALS['current_user']);
	}

	public function countAverage($values)
	{
		$count = 0;
		foreach ($values as $key => $value)
		{
			$count += $value;
		}
		return (round($count / count($values)));
	}

	public function testResourceName()
	{
		$processingTask = $this->projectTasks['firstChildTask'];
		$processingTask->percent_complete = $this->newPercentValue;
		$processingTask->save();

		/**
		 * New method testing
		 */
		$processingTask->updateParentProjectTaskPercentage();

		$testparentTask = new ProjectTask();
		$testparentTask->retrieve($this->projectTasks['parentTask']->id);

		$average = $this->countAverage(array ($this->newPercentValue, $this->projectTasks['secondChildTask']->percent_complete));
		$this->assertEquals($average, $testparentTask->percent_complete);
	}
}