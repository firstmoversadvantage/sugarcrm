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

 
require_once "modules/Tasks/Task.php";

class TasksTest extends Sugar_PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
	}

	public static function tearDownAfterClass()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }

    public function setUp()
    {
        $_REQUEST['module'] = 'Tasks';
    }

    public function tearDown()
    {
        unset($_REQUEST['module']);
        if(!empty($this->taskid)) {
            $GLOBALS['db']->query("DELETE FROM tasks WHERE id='{$this->taskid}'");
        }
    }

    /**
     * @ticket 39259
     */
    public function testListviewTimeDueFieldProperlyHandlesDst()
    {
        $task = new Task();
        $task->name = "New Task";
        $task->date_due = $GLOBALS['timedate']->to_display_date_time("2010-08-30 15:00:00");
        $listViewFields = $task->get_list_view_data();

        $this->assertEquals($listViewFields['TIME_DUE'], $GLOBALS['timedate']->to_display_time("15:00:00"));
    }

    /**
     * @group bug40999
     */
    public function testTaskStatus()
    {
         $task = new Task();
         $this->taskid = $task->id = create_guid();
         $task->new_with_id = 1;
         $task->status = 'Done';
         $task->save();
         // then retrieve
         $task = new Task();
         $task->retrieve($this->taskid);
         $this->assertEquals('Done', $task->status);
    }

    /**
     * @group bug40999
     */
    public function testTaskEmptyStatus()
    {
         $task = new Task();
         $this->taskid = $task->id = create_guid();
         $task->new_with_id = 1;
         $task->save();
         // then retrieve
         $task = new Task();
         $task->retrieve($this->taskid);
         $this->assertEquals('Not Started', $task->status);
    }

}
