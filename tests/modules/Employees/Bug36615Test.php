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
require_once 'modules/Employees/EmployeeStatus.php';
require_once 'SugarTestUserUtilities.php';


class Bug36615Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $focus;
	var $current_user;
	var $view;
	//var $EMPLOYEE_STATUS = "<select name='employee_status'>option value='Acitve' selected=''>Active</option><option value='Terminated'>Terminated</option><option value='Leave of Absence'>Leave of Absence</option>";
	var $emplsts;
	var $sugar_config;

	public function setUp()
	{

		$this->current_user = new User();
		$this->focus = SugarTestUserUtilities::createAnonymousUser();
		$GLOBALS['app_list_strings'] = return_application_language($GLOBALS['current_language']);
		global $sugar_config;
    	$sugar_config['default_user_name'] = $this->focus->user_name;
    	global $app_list_strings;
   		$app_list_strings = return_app_list_strings_language($GLOBALS['current_language']);

	}


	public function tearDown()
	{

	}


	public function testEmployeeStatusAdminEditView()
	{

		$this->current_user->retrieve('1');
		$GLOBALS['current_user'] = $this->current_user;

		$this->view = "EditView";

		$this->emplsts = getEmployeeStatusOptions($this->focus, 'employee_status', '', $this->view);

		//On EditView and admin user, employee_status must not be blank.
		$this->assertNotEquals( $this->emplsts, '');


	}

	public function testEmployeeStatusAdminDeatilView()
	{

		$this->current_user->retrieve('1');
		$GLOBALS['current_user'] = $this->current_user;

		$this->view = "DetailView";

		//setting employee_status to Active. On DetailedView for this user, admin should not see a blank string.
		$this->focus->employee_status = "Active";

		$this->emplsts = getEmployeeStatusOptions($this->focus, 'employee_status', '', $this->view);


		$this->assertNotEquals( $this->emplsts, '');


	}


	public function testEmployeeStatusRegularUserDeatilView()
	{

		$GLOBALS['current_user'] = $this->current_user;

		$this->view = "DetailView";

		$this->current_user->employee_status = "Active";

		$this->emplsts = getEmployeeStatusOptions($this->current_user, 'employee_status', '', $this->view);

		$this->assertEquals( $this->emplsts, 'Active');


	}

	public function testEmployeeStatusRegularUserEditView()
	{

		$GLOBALS['current_user'] = $this->current_user;

		$this->view = "EditView";

		$this->current_user->employee_status = "Active";

		$this->emplsts = getEmployeeStatusOptions($this->current_user, 'employee_status', '', $this->view);

		$this->assertEquals( $this->emplsts, 'Active');


	}

	public function testEmployeeStatusAfterUserEdit()
	{

	//Stub

		//Need to simulate the sitation described in the bug:
		//A regular user edits its own employee page. After clicking Save, the employee_status field is blank.


	}
}
