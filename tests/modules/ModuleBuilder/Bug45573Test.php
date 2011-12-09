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


class Bug45573Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $hasCustomSearchFields;
	
	public function setUp()
	{
	    require('include/modules.php');
	    $GLOBALS['beanList'] = $beanList;
	    $GLOBALS['beanFiles'] = $beanFiles;
	    $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
	    
	    $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
	    $GLOBALS['current_user']->is_admin = true;
		
		if(file_exists('custom/modules/Cases/metadata/SearchFields.php'))
		{			
			$this->hasCustomSearchFields = true;
            copy('custom/modules/Cases/metadata/SearchFields.php', 'custom/modules/Cases/metadata/SearchFields.php.bak');
            unlink('custom/modules/Cases/metadata/SearchFields.php');			
		}
	}
	
	public function tearDown()
	{
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
		
		if($this->hasCustomSearchFields && file_exists('custom/modules/Cases/metadata/SearchFields.php.bak'))
		{
		   copy('custom/modules/Cases/metadata/SearchFields.php.bak', 'custom/modules/Cases/metadata/SearchFields.php');
		   unlink('custom/modules/Cases/metadata/SearchFields.php.bak');
		} else if(!$this->hasCustomSearchFields && file_exists('custom/modules/Cases/metadata/SearchFields.php')) {
		   unlink('custom/modules/Cases/metadata/SearchFields.php');
		}
		
		//Refresh vardefs for Cases to reset
		VardefManager::loadVardef('Cases', 'aCase', true); 
	}
	
	/**
	 * testActionAdvancedSearchViewSave
	 * This method tests to ensure that custom SearchFields are created or updated when a search layout change is made
	 */
	public function testActionAdvancedSearchViewSave()
	{
		require_once('modules/ModuleBuilder/controller.php');
		$mbController = new ModuleBuilderController();
		$_REQUEST['view_module'] = 'Cases';
		$_REQUEST['view'] = 'advanced_search';
		$mbController->action_searchViewSave();
		$this->assertTrue(file_exists('custom/modules/Cases/metadata/SearchFields.php'));
		
		require('custom/modules/Cases/metadata/SearchFields.php');
		$this->assertTrue(isset($searchFields['Cases']['range_date_entered']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_entered']['enable_range_search']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_modified']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_modified']['enable_range_search']));
	}
	
	/**
	 * testActionBasicSearchViewSave
	 * This method tests to ensure that custom SearchFields are created or updated when a search layout change is made
	 */
	public function testActionBasicSearchViewSave()
	{
		require_once('modules/ModuleBuilder/controller.php');
		$mbController = new ModuleBuilderController();
		$_REQUEST['view_module'] = 'Cases';
		$_REQUEST['view'] = 'basic_search';
		$mbController->action_searchViewSave();
		$this->assertTrue(file_exists('custom/modules/Cases/metadata/SearchFields.php'));
		
		require('custom/modules/Cases/metadata/SearchFields.php');
		$this->assertTrue(isset($searchFields['Cases']['range_date_entered']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_entered']['enable_range_search']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_modified']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_modified']['enable_range_search']));
	}	
	
	
	/**
	 * testActionAdvancedSearchSaveWithoutAnyRangeSearchFields
	 * One last test to check what would happen if we had a module that did not have any range search fields enabled
	 */
	public function testActionAdvancedSearchSaveWithoutAnyRangeSearchFields()
	{
        //Load the vardefs for the module to pass to TemplateRange
        VardefManager::loadVardef('Cases', 'aCase', true); 
        global $dictionary;      
        $vardefs = $dictionary['Case']['fields'];
        foreach($vardefs as $key=>$def)
        {
        	if(!empty($def['enable_range_search']))
        	{
        		unset($vardefs[$key]['enable_range_search']);
        	}
        }
        
        require_once('modules/DynamicFields/templates/Fields/TemplateRange.php');
        TemplateRange::repairCustomSearchFields($vardefs, 'Cases');	
		
        //In this case there would be no custom SearchFields.php file created
		$this->assertTrue(!file_exists('custom/modules/Cases/metadata/SearchFields.php'));
		
		//Yet we have the defaults set still in out of box settings
		require('modules/Cases/metadata/SearchFields.php');
		$this->assertTrue(isset($searchFields['Cases']['range_date_entered']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_entered']['enable_range_search']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_modified']));
		$this->assertTrue(isset($searchFields['Cases']['range_date_modified']['enable_range_search']));
	}
		
}

?>