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
require_once "modules/Contacts/Contact.php";
require_once "include/SearchForm/SearchForm2.php";

class Bug45709Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $task = null;
	var $contact = null;
	var $requestArray = null;
	var $searchForm = null;
   
    public function setUp()
    {
		$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
		$this->contact = SugarTestContactUtilities::createContact();	
    	$this->task =SugarTestTaskUtilities::createTask();
    	$this->task->contact_id = $this->contact->id;
    	$this->task->save();
    }
    
    public function tearDown()
    {
        
        SugarTestContactUtilities::removeAllCreatedContacts();
        SugarTestTaskUtilities::removeAllCreatedTasks();
        unset($GLOBALS['current_user']);
    }
    
    /**
     * @ticket 45709
     */
    public function testGenerateSearchWhereForFieldsWhenFullContactNameGiven()
    {
        //test GenerateSearchWhere for fields that have db_concat_fields set in vardefs
        //Contact in advanced search panel in Tasks module is one of those
        
    	//array to simulate REQUEST object
    	$this->requestArray['module'] = 'Tasks';
    	$this->requestArray['action'] = 'index';
    	$this->requestArray['searchFormTab'] = 'advanced_search';
    	$this->requestArray['contact_name_advanced'] = $this->contact->first_name. " ". $this->contact->last_name; //value of a contact name field set in REQUEST object
    	$this->requestArray['query'] = 'true';
    	
    	
    	$this->searchForm = new SearchForm($this->task,'Tasks');
    	
    	require 'modules/Tasks/vardefs.php';
    	require 'modules/Tasks/metadata/SearchFields.php';
    	require 'modules/Tasks/metadata/searchdefs.php';
        $this->searchForm->searchFields = $searchFields[$this->searchForm->module]; 
        $this->searchForm->searchdefs = $searchdefs[$this->searchForm->module]; 
        $this->searchForm->fieldDefs = $this->task->getFieldDefinitions();                        
    	$this->searchForm->populateFromArray($this->requestArray,'advanced_search',false);
    	$whereArray = $this->searchForm->generateSearchWhere(true, $this->task->module_dir);
    	$test_query = "SELECT id FROM contacts WHERE " . $whereArray[0];
    	$result = $GLOBALS['db']->query($test_query);
    	$row = $GLOBALS['db']->fetchByAssoc($result);

    	$this->assertEquals($this->contact->id, $row['id']);
    	
    	$result2 = $GLOBALS['db']->query("SELECT * FROM tasks WHERE tasks.contact_id='{$this->task->contact_id}'");
        $row2 = $GLOBALS['db']->fetchByAssoc($result2);
        
        $this->assertEquals($this->task->id, $row2['id']);
    }
}
