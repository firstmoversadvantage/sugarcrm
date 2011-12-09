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

 
class Bug44324Test extends Sugar_PHPUnit_Framework_OutputTestCase
{
	var $contact;

	public function setUp()
	{
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['app_list_strings'] = return_app_list_strings_language('en_us');
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
        $this->contact = SugarTestContactUtilities::createContact();	
        $this->contact->salutation = 'Ms.';
        $this->contact->first_name = 'Lady';
        $this->contact->last_name = 'Gaga';	
        //Save contact with salutation
        $this->contact->save();
	}
	
	public function tearDown()
	{
	    SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        SugarTestContactUtilities::removeAllCreatedContacts();		
	}
	
    public function testSearchNamePopulatedCorrectly()
    {
    	require_once('include/Popups/PopupSmarty.php');
    	$popupSmarty = new PopupSmarty($this->contact, $this->contact->module_dir);
    	$this->contact->_create_proper_name_field();
    	$search_data = array();
    	$search_data[] = array('ID'=>$this->contact->id, 'NAME'=>$this->contact->name, 'FIRST_NAME'=>$this->contact->first_name, 'LAST_NAME'=>$this->contact->last_name);
    	
    	$data = array('data'=>$search_data);
    	$data['pageData']['offsets']['lastOffsetOnPage'] = 0;
    	$data['pageData']['offsets']['current'] = 0;
    	$popupSmarty->data = $data;
    	$popupSmarty->fieldDefs = array();
    	$popupSmarty->view= 'popup';
    	$popupSmarty->tpl = 'include/Popups/tpls/PopupGeneric.tpl';
    	$this->expectOutputRegex('/\"NAME\":\"Lady Gaga\"/', 'Assert that NAME value was set to "Lady Gaga"');
    	echo $popupSmarty->display();
    }

}

?>
