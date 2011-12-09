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
require_once 'include/SearchForm/SearchForm2.php';
require_once 'modules/Opportunities/Opportunity.php';

class RangeSearchTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $hasExistingCustomSearchFields = false;
    var $searchForm;
    var $originalDbType;
    var $smartyTestFile;
    
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
		
    	//Setup Opportunities module and date_closed field
		$_REQUEST['view_module'] = 'Opportunities';
		$_REQUEST['name'] = 'date_closed';
		$templateDate = new TemplateDate();
		$templateDate->enable_range_search = true;
		$templateDate->populateFromPost();
		include('custom/modules/Opportunities/metadata/SearchFields.php');

		//Prepare SearchForm
    	$seed = new Opportunity();
    	$module = 'Opportunities';
		$this->searchForm = new SearchForm($seed, $module);
		$this->searchForm->searchFields = array(
			'range_date_closed' => array
	        (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'is_date_field' => 1,
	            'value' => '[this_year]',
	            'operator' => 'this_year',
	        ),
	        'start_range_date_closed' => array
	        (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'is_date_field' => 1,
	        ),
	        'end_range_date_closed' => array
	        (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'is_date_field' => 1,
	        ),
       		'range_amount' => array 
	        (
	        	'query_type' => 'default', 
	        	'enable_range_search' => true
	        ),
	   		'start_range_amount' => array 
	        (
	        	'query_type' => 'default',
	        	'enable_range_search' => true
	        ),
       		'end_range_amount' => array (
       			'query_type' => 'default',
       			'enable_range_search' => true
	        ),
		);		
		
		$this->originalDbType = $GLOBALS['db']->dbType;
    }
    
    public function tearDown()
    {		
		$GLOBALS['db']->dbType = $this->originalDbType;
		
    	if(!$this->hasExistingCustomSearchFields)
		{
		   unlink('custom/modules/Opportunities/metadata/SearchFields.php');
		}    	
    	
		if(file_exists('custom/modules/Opportunities/metadata/SearchFields.php.bak')) {
		   copy('custom/modules/Opportunities/metadata/SearchFields.php.bak', 'custom/modules/Opportunities/metadata/SearchFields.php');
		   unlink('custom/modules/Opportunities/metadata/SearchFields.php.bak');
		}

		if(file_exists($this->smartyTestFile))
		{
			unlink($this->smartyTestFile);
		}
		
    }
    
    public function testRangeNumberSearches()
    {
    	$GLOBALS['db']->dbType = 'mysql';
    	unset($this->searchForm->searchFields['range_date_closed']);
		$this->searchForm->searchFields['range_amount'] = array (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'value' => '10000',
	            'operator' => 'greater_than',
	    );    

		$where_clauses = $this->searchForm->generateSearchWhere();		
		$this->assertEquals($where_clauses[0], "opportunities.amount > '10000'");

		$this->searchForm->searchFields['range_amount'] = array (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'value' => '10000',
	            'operator' => 'less_than',
	    );    

		$where_clauses = $this->searchForm->generateSearchWhere();		
		$this->assertEquals($where_clauses[0], "opportunities.amount < '10000'"); 	

		$this->searchForm->searchFields['range_amount'] = array (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'value' => '10000',
	            'operator' => 'greater_than_equals',
	    );    

		$where_clauses = $this->searchForm->generateSearchWhere();		
		$this->assertEquals($where_clauses[0], "opportunities.amount >= '10000'");

		$this->searchForm->searchFields['range_amount'] = array (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'value' => '10000',
	            'operator' => 'less_than_equals',
	    );    

		$where_clauses = $this->searchForm->generateSearchWhere();		
		$this->assertEquals($where_clauses[0], "opportunities.amount <= '10000'"); 		
		
		$this->searchForm->searchFields['range_amount'] = array (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'value' => '10000',
	            'operator' => 'not_equal',
	    );    

		$where_clauses = $this->searchForm->generateSearchWhere();		
		$this->assertEquals($where_clauses[0], "opportunities.amount != '10000'"); 

		$this->searchForm->searchFields['range_amount'] = array (
	            'query_type' => 'default',
	            'enable_range_search' => 1,
	            'value' => '10000',
	            'operator' => '=',
	    );    

		$where_clauses = $this->searchForm->generateSearchWhere();		
		$this->assertEquals($where_clauses[0], "opportunities.amount >= '9999.99' AND opportunities.amount <= '10000.01'"); 		
		
    }
    
    /**
     * testRangeSearchWithSavedReportValues
     * This test attempts to simulate testing what would happen should a saved report be invoked against
     * a range search field
     * 
     */
    public function testRangeSearchWithSavedReportValues()
    {
    	require_once('include/SugarFields/Fields/Datetime/SugarFieldDatetime.php');
    	$parentFieldArray = 'fields';
    	
    	$vardef = array();
    	$vardef['name'] = 'date_closed_advanced';
    	$vardef['vname'] = 'LBL_DATE_CLOSED';
    	
		$opportunity = new Opportunity();
		$vardef = $opportunity->field_defs['date_closed'];
		$vardef['name'] = 'date_closed_advanced';
		$vardef['options'] = array
        (
            '=' => 'Equals',
            'not_equal' => 'Not On',
            'greater_than' => ' After',
            'less_than' => ' Before',
            'last_7_days' => ' Last 7 Days',
            'next_7_days' => ' Next 7 Days',
            'last_30_days' => ' Last 30 Days',
            'next_30_days' => ' Next 30 Days',
            'last_month' => ' Last Month',
            'this_month' => ' This Month',
            'next_month' => ' Next Month',
            'last_year' => ' Last Year',
            'this_year' => ' This Year',
            'next_year' => ' Next Year',
            'between' => ' Is Between',
        );
		
		
		$displayParams = array('labelSpan'=>'', 'fieldSpan'=>'');
		$tabindex = '';

		$sugarFieldDatetime = new SugarFieldDatetime('Datetime');
		
		$_REQUEST['action'] = 'SearchForm';
		$html = $sugarFieldDatetime->getSearchViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex);
		
		//Write this widget's contents to a file
		$this->smartyTestFile = 'tests/include/SearchForm/RangeSearchTest.tpl';
        $handle = sugar_fopen($this->smartyTestFile, 'wb');
        fwrite($handle, $html);
        
        //Check that the template exists before we proceed with further tests
        $this->assertTrue(file_exists($this->smartyTestFile));
        
        //Stuff Smarty variables
        $vardef['value'] = '';
        $fields = array();
        $fields['date_closed_advanced'] = $vardef;
        
        //Create Smarty instance
    	require_once('include/Sugar_Smarty.php');
    	$ss = new Sugar_Smarty();
    	
    	//Assign Smarty variables
    	$ss->assign('fields', $fields);
    	$ss->assign('APP', $GLOBALS['app_strings']);
    	$ss->assign('CALENDAR_FORMAT', 'm-d-Y');
    	
    	//Simulate the request with saved report value
    	$_REQUEST['date_closed_advanced'] = '07-03-2009';
    	
		$output = $ss->fetch($this->smartyTestFile);
        $this->assertRegExp("/range_date_closed_advanced\"\s+?value\s*?\=s*?\'07\-03\-2009\'/", $output);
        
    	//Simulate the request with range search value
    	$_REQUEST['range_date_closed_advanced'] = '07-04-2009';
    	
		$output = $ss->fetch($this->smartyTestFile);
        $this->assertRegExp("/range_date_closed_advanced\"\s+?value\s*?\=s*?\'07\-04\-2009\'/", $output);        
    } 
   
}
?>