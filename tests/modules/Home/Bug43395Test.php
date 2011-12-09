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


class Bug43395Test extends Sugar_PHPUnit_Framework_OutputTestCase
{
	private static $quickSearch;
	private static $contact;
	
	static function setUpBeforeClass() 
    {
    	global $app_strings, $app_list_strings;
        $app_strings = return_application_language($GLOBALS['current_language']);
        $app_list_strings = return_app_list_strings_language($GLOBALS['current_language']);    	
    	$user = new User();
    	$user->retrieve('1');
        $GLOBALS['current_user'] = $user;
        self::$contact = SugarTestContactUtilities::createContact();
        self::$contact->first_name = 'Bug43395';
        self::$contact->last_name = 'Test';
        self::$contact->salutation = 'Mr.'; 
        self::$contact->save();    	
    }
    
    public static function tearDownAfterClass() 
    {
        unset($_REQUEST['data']);
        unset($_REQUEST['query']);
        SugarTestContactUtilities::removeAllCreatedContacts();
    }
    
    public function testFormatResults()
    {	
    	$_REQUEST = array();
    	$_REQUEST['data'] = '{"form":"search_form","method":"query","modules":["Contacts"],"group":"or","field_list":["name","id"],"populate_list":["contact_c_basic","contact_id_c_basic"],"required_list":["parent_id"],"conditions":[{"name":"name","op":"like_custom","end":"%","value":""}],"order":"name","limit":"30","no_match_text":"No Match"}';
        $_REQUEST['query'] = self::$contact->first_name;
        require_once 'modules/Home/quicksearchQuery.php';
        
        $json = getJSONobj();
		$data = $json->decode(html_entity_decode($_REQUEST['data']));
		if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])){
    		foreach($data['conditions'] as $k=>$v){
    			if(empty($data['conditions'][$k]['value'])){
       				$data['conditions'][$k]['value']=$_REQUEST['query'];
    			}
    		}
		}
 		self::$quickSearch = new quicksearchQuery();
		$result = self::$quickSearch->query($data);
		$resultBean = $json->decodeReal($result);
	    $this->assertEquals($resultBean['fields'][0]['name'], self::$contact->first_name . ' ' . self::$contact->last_name, 'Assert that the quicksearch returns a contact name without salutation');
    }
    
    public function testPersonLocaleNameFormattting()
    {
        $GLOBALS['current_user']->setPreference('default_locale_name_format', 's f l');

    	self::$contact->createLocaleFormattedName = true;
    	self::$contact->_create_proper_name_field();
    	$this->assertContains('Mr.',self::$contact->name, 'Assert that _create_proper_name_field with createLocaleFormattedName set to true returns salutation');

    	self::$contact->createLocaleFormattedName = false;
    	self::$contact->_create_proper_name_field();
    	$this->assertNotContains('Mr.',self::$contact->name, 'Assert that _create_proper_name_field with createLocaleFormattedName set to false does not return salutation');
    }
    
}
?>