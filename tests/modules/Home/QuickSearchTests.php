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

 
class QuickSearchTest extends Sugar_PHPUnit_Framework_OutputTestCase
{
	private $quickSearch;
	
	public function setUp() 
    {
    	
    }
    
    public function tearDown() 
    {
        unset($_REQUEST['data']);
        unset($_REQUEST['query']);
        $q = "delete from product_templates where name = 'MasonUnitTest';";
        $GLOBALS['db']->query($q);
    }
	
    public function testFormatResults()
    {
    	$tempPT = new ProductTemplate();
    	$tempPT->name = 'MasonUnitTest';
    	$tempPT->description = "Unit'test";
    	$tempPT->cost_price = 1000;
    	$tempPT->discount_price = 800;
    	$tempPT->list_price = 1100;
    	$tempPT->save();
    	
    	$_REQUEST['data'] = '{"conditions":[{"end":"%","name":"name","op":"like_custom","value":""}],"field_list":["name","id","type_id","mft_part_num","cost_price","list_price","discount_price","pricing_factor","description","cost_usdollar","list_usdollar","discount_usdollar","tax_class_name"],"form":"EditView","group":"or","id":"EditView_product_name[1]","limit":"30","method":"query","modules":["ProductTemplates"],"no_match_text":"No Match","order":"name","populate_list":["name_1","product_template_id_1"],"post_onblur_function":"set_after_sqs"}';
        $_REQUEST['query'] = 'MasonUnitTest';
        require_once 'modules/home/quicksearchQuery.php';
        
        $json = getJSONobj();
		$data = $json->decode(html_entity_decode($_REQUEST['data']));
		if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])){
    		foreach($data['conditions'] as $k=>$v){
    			if(empty($data['conditions'][$k]['value'])){
       				$data['conditions'][$k]['value']=$_REQUEST['query'];
    			}
    		}
		}
 		$this->quickSearch = new quicksearchQuery();
		$result = $this->quickSearch->query($data);
		$resultBean = $json->decodeReal($result);
		$this->assertEquals($resultBean['fields'][0]['description'], $tempPT->description);
    }
}
?>