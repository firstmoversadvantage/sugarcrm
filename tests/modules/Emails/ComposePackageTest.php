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

 
require_once('modules/Contacts/Contact.php');


/**
 * @ticket 32487
 */
class ComposePackageTest extends Sugar_PHPUnit_Framework_TestCase
{
	var $c = null;
	var $a = null;
	var $ac_id = null;
	
	public function setUp()
    {
        global $current_user, $currentModule ;
        $mod_strings = return_module_language($GLOBALS['current_language'], "Contacts");
        $beanList = array();
        $beanFiles = array();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        $current_user = SugarTestUserUtilities::createAnonymousUser();
        $unid = uniqid();
        $time = date('Y-m-d H:i:s');

        $contact = new Contact();
        $contact->id = 'c_'.$unid;
        $contact->first_name = 'testfirst';
        $contact->last_name = 'testlast';
        $contact->new_with_id = true;
        $contact->disable_custom_fields = true;
        $contact->save();
		$this->c = $contact;
		
		$beanList = array();
		$beanFiles = array();
		require('include/modules.php');
		$GLOBALS['beanList'] = $beanList;
		$GLOBALS['beanFiles'] = $beanFiles;

	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);

        unset($GLOBALS['mod_strings']);
        unset($GLOBALS['beanList']);
		unset($GLOBALS['beanFiles']);
		

        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->c->id}'");
        
        unset($this->c);
    }

	public function testComposeFromMethodCallNoData()
	{    
	    $_REQUEST['forQuickCreate'] = true;
	    require_once('modules/Emails/Compose.php');
	    $data = array();
	    $compose_data = generateComposeDataPackage($data,FALSE);
	    
		$this->assertEquals('', $compose_data['to_email_addrs']);
    }
    
    public function testComposeFromMethodCallForContact()
    {    
	    $_REQUEST['forQuickCreate'] = true;
	    require_once('modules/Emails/Compose.php');
	    $data = array();
	    $data['parent_type'] = 'Contacts';
	    $data['parent_id'] = $this->c->id;
	    
	    $compose_data = generateComposeDataPackage($data,FALSE);

		$this->assertEquals('Contacts', $compose_data['parent_type']);
		$this->assertEquals($this->c->id, $compose_data['parent_id']);
		$this->assertEquals($this->c->name, $compose_data['parent_name']);
    }
}