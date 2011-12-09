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


require_once 'modules/DynamicFields/templates/Fields/TemplateInt.php';
require_once 'modules/DynamicFields/templates/Fields/TemplateDate.php';

class TemplateDateTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $hasExistingCustomSearchFields = false;
    
    public function setUp()
    {	
		if(file_exists('custom/modules/Opportunities/metadata/SearchFields.php'))
		{
		   $this->hasExistingCustomSearchFields = true;
		   copy('custom/modules/Opportunities/metadata/SearchFields.php', 'custom/modules/Opportunities/metadata/SearchFields.php.bak');
		   unlink('custom/modules/Opportunities/metadata/SearchFields.php');
		} else if(!file_exists('custom/modules/Opportunities/metadata')) {
		   mkdir_recursive('custom/modules/Opportunities/metadata');
		}
    }
    
    public function tearDown()
    {		

    	if(!$this->hasExistingCustomSearchFields)
		{
		   unlink('custom/modules/Opportunities/metadata/SearchFields.php');
		}    	
    	
		if(file_exists('custom/modules/Opportunities/metadata/SearchFields.php.bak')) {
		   copy('custom/modules/Opportunities/metadata/SearchFields.php.bak', 'custom/modules/Opportunities/metadata/SearchFields.php');
		   unlink('custom/modules/Opportunities/metadata/SearchFields.php.bak');
		}

    }
    
    public function testEnableRangeSearchInt()
    {
		$_REQUEST['view_module'] = 'Opportunities';
		$_REQUEST['name'] = 'probability';
		$templateDate = new TemplateInt();
		$templateDate->enable_range_search = true;
		$templateDate->populateFromPost();
		$this->assertTrue(file_exists('custom/modules/Opportunities/metadata/SearchFields.php'));
		include('custom/modules/Opportunities/metadata/SearchFields.php');
		$this->assertTrue(isset($searchFields['Opportunities']['range_probability']));
		$this->assertTrue(isset($searchFields['Opportunities']['start_range_probability']));
		$this->assertTrue(isset($searchFields['Opportunities']['end_range_probability']));
		$this->assertTrue(!isset($searchFields['Opportunities']['range_probability']['is_date_field']));
		$this->assertTrue(!isset($searchFields['Opportunities']['start_range_probability']['is_date_field']));
		$this->assertTrue(!isset($searchFields['Opportunities']['end_range_probability']['is_date_field']));			
    }
    
    public function testEnableRangeSearchDate()
    {
		$_REQUEST['view_module'] = 'Opportunities';
		$_REQUEST['name'] = 'date_closed';
		$templateDate = new TemplateDate();
		$templateDate->enable_range_search = true;
		$templateDate->populateFromPost();
		$this->assertTrue(file_exists('custom/modules/Opportunities/metadata/SearchFields.php'));
		include('custom/modules/Opportunities/metadata/SearchFields.php');
		$this->assertTrue(isset($searchFields['Opportunities']['range_date_closed']));
		$this->assertTrue(isset($searchFields['Opportunities']['start_range_date_closed']));
		$this->assertTrue(isset($searchFields['Opportunities']['end_range_date_closed']));
		$this->assertTrue(isset($searchFields['Opportunities']['range_date_closed']['is_date_field']));
		$this->assertTrue(isset($searchFields['Opportunities']['start_range_date_closed']['is_date_field']));
		$this->assertTrue(isset($searchFields['Opportunities']['end_range_date_closed']['is_date_field']));		
    }    
    
}
?>