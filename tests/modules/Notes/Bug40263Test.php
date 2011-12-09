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
require_once "modules/Notes/Note.php";

/**
 * @group bug40263
 */
class Bug40263Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $user;
	var $note;

	public function setUp()
    {
		global $current_user;

		$this->user = SugarTestUserUtilities::createAnonymousUser();//new User();
		$this->user->first_name = "test";
		$this->user->last_name = "user";
		$this->user->user_name = "test_test";
		$this->user->save();
		$current_user=$this->user;

		$this->note = new Note();
		$this->note->name = "Bug40263 test Note";
		$this->note->save();
	}

	public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        $this->note->mark_deleted($this->note->id);
        $this->note->db->query("DELETE FROM notes WHERE id='{$this->note->id}'");
	}

	public function testGetListViewQueryCreatedBy()
    {
		require_once("include/ListView/ListViewDisplay.php");
        include("modules/Notes/metadata/listviewdefs.php");
        $displayColumns = array(
            'NAME' => array (
			    'width' => '40%',
			    'label' => 'LBL_LIST_SUBJECT',
			    'link' => true,
			    'default' => true,
			 ),
			 'CREATED_BY_NAME' => array (
			     'type' => 'relate',
			     'label' => 'LBL_CREATED_BY',
			     'width' => '10%',
			     'default' => true,
			 ),
		);
		$lvd = new ListViewDisplay();
		$lvd->displayColumns = $displayColumns;
		$fields = $lvd->setupFilterFields();
    	$query = $this->note->create_new_list_query('', 'id="' . $this->note->id . '"', $fields);
    	$regex = '/select.* created_by_name.*LEFT JOIN\s*users jt\d ON\s*notes.created_by\s*=\s*jt\d\.id.*/si';
    	return $this->assertRegExp($regex, $query, "Unable to find the created user in the notes list view query: $query");
    }

}

