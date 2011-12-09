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

 
require_once('data/SugarBean.php');
require_once('modules/Contacts/Contact.php');
require_once('include/SubPanel/SubPanelDefinitions.php');

/**
 * test get_union_related_list() with subpanels, functions, distinct clause
 */
class GetUnionRelatedTest extends Sugar_PHPUnit_Framework_TestCase
{
    /**
     * Bean to use for tests
     * @var SugarBean
     */
    protected $bean;

	public function setUp()
	{
	    global $moduleList, $beanList, $beanFiles;
        require('include/modules.php');
	    $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->bean = new Contact();
	}

	public function tearDown()
	{
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
	}

    public function testGetUnionRelatedList()
    {
        $subpanel = array(
			'order' => 20,
			'sort_order' => 'desc',
			'sort_by' => 'date_entered',
			'type' => 'collection',
			'subpanel_name' => 'history',   //this values is not associated with a physical file.
			'top_buttons' => array(),
			'collection_list' => array(
				'meetings' => array(
					'module' => 'Meetings',
					'subpanel_name' => 'ForHistory',
					'get_subpanel_data' => 'meetings',
				),
				'emails' => array(
					'module' => 'Emails',
					'subpanel_name' => 'ForHistory',
					'get_subpanel_data' => 'emails',
				    'get_distinct_data' => true,
				),
				'linkedemails_contacts' => array(
	                'module' => 'Emails',
	                'subpanel_name' => 'ForHistory',
				    'generate_select'=>true,
				    'get_distinct_data' => true,
                    'get_subpanel_data' => 'function:GetUnionRelatedTest_get_select',
        			'function_parameters' => array('import_function_file' => __FILE__),
				),
			)
        );
        $subpanel_def = new aSubPanel("testpanel", $subpanel, $this->bean);
        $query = $this->bean->get_union_related_list($this->bean, "", '', "", 0, 5, -1, 0, $subpanel_def);
        $result = $this->bean->db->query($query["query"]);
        $this->assertTrue($result != false, "Bad query: {$query["query"]}");
    }
}

function GetUnionRelatedTest_get_select()
{
    $return_array['select']='SELECT DISTINCT emails.id';
    $return_array['from']='FROM emails ';
	$return_array['join'] = " JOIN emails_email_addr_rel eear ON eear.email_id = emails.id AND eear.deleted=0
		    	JOIN email_addr_bean_rel eabr ON eabr.email_address_id=eear.email_address_id AND eabr.bean_module = 'Contacts'
		    		AND eabr.deleted=0 AND eabr.bean_id = '1'";
    $return_array['where']="";
    $return_array['join_tables'] = array();
    return $return_array;
}
