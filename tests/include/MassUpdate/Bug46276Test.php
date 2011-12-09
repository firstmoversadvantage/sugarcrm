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

 
require_once 'include/MassUpdate.php';
require_once 'modules/Opportunities/Opportunity.php';


class Bug46276Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $opportunities;

	public function setUp()
	{

		global $current_user, $timedate;
		// Create Anon User setted on GMT+1 TimeZone
		$current_user = SugarTestUserUtilities::createAnonymousUser();
		$current_user->setPreference('datef', "Y-m-d");
		$current_user->setPreference('timef', "H:i:s");
		$current_user->setPreference('timezone', "Europe/London");

		// new object to avoid TZ caching
		$timedate = new TimeDate();

		$this->opportunities = new Opportunity();
		$this->opportunities->name = 'Bug46276 Opportunity';
		$this->opportunities->amount = 1234;
		$this->opportunities->sales_stage = "Prospecting";
		$this->opportunities->account_name = "A.G. Parr PLC";
		$this->opportunities->date_closed = '2011-08-12';
		$this->opportunities->save();
	}

	public function tearDown()
	{
		 
		$GLOBALS['db']->query('DELETE FROM opportunities WHERE id = \'' . $this->opportunities->id . '\' ');
		unset($this->opportunities);
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
	}

	//testing handleMassUpdate() for date fields when time zone of the current user is GMT+

	public function testhandleMassUpdateForDateFieldsInGMTPlusTimeZone()
	{
		global $current_user, $timedate;
		$_REQUEST = $_POST = array("module" => "Opportunities",
                                   "action" => "MassUpdate",
                                   "return_action" => "index",
                                   "delete" => "false",
    							   "massupdate" => "true",
    							   "lvso" => "asc",
    							   "uid" => $this->opportunities->id,
    							   "date_closed" => "2011-08-09",		
		);



		$mass = new MassUpdate();
		$mass->setSugarBean($this->opportunities);
		$mass->handleMassUpdate();
		$expected_date = $_REQUEST['date_closed'];
		$actual_date = $this->opportunities->date_closed;
		$this->assertEquals($expected_date, $actual_date);
	}
	 

}
