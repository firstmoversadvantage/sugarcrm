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


/**
 * @ticket 43471
 */
class Bug43471Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $_tablename;
    private $_old_installing;
    
    public function setUp()
    {
        $this->accountMockBean = $this->getMock('TestBean');
        $this->_tablename = 'test' . date("YmdHis");
    }
    
    public function tearDown()
    {
    }
    
    public function testDynamicFieldsRepairCustomFields()
    {
        $bean = $this->accountMockBean;

        /** @var $df DynamicField */
        $df = $this->getMock('DynamicField', array('createCustomTable'));
        $bean->table_name = $this->_tablename;
        $bean->field_defs = array (
              'id' =>
              array (
                'name' => 'id',
                'vname' => 'LBL_ID',
                'type' => 'id',
                'required' => true,
                'reportable' => true,
                'comment' => 'Unique identifier',
              ),
              'name' =>
              array (
                'name' => 'name',
                'type' => 'name',
                'dbType' => 'varchar',
                'vname' => 'LBL_NAME',
                'len' => 150,
                'comment' => 'Name of the Company',
                'unified_search' => true,
                'audited' => true,
                'required' => true,
                'importable' => 'required',
                'merge_filter' => 'selected',
              ),
              'FooBar_c' =>
              array (
                'required' => false,
                'source' => 'custom_fields',
                'name' => 'FooBar_c',
                'vname' => 'LBL_FOOBAR',
                'type' => 'varchar',
                'massupdate' => '0',
                'default' => NULL,
                'comments' => 'LBL_FOOBAR_COMMENT',
                'help' => 'LBL_FOOBAR_HELP',
                'importable' => 'true',
                'duplicate_merge' => 'disabled',
                'duplicate_merge_dom_value' => '0',
                'audited' => false,
                'reportable' => true,
                'calculated' => false,
                'len' => '255',
                'size' => '20',
                'id' => 'AccountsFooBar_c',
                'custom_module' => 'Accounts',
              ),
            );
        $df->setup($bean);
        $df->expects($this->any())
                ->method('createCustomTable')
                ->will($this->returnValue(null));

        $helper = $this->getMock('MysqliHelper');
        $helper->expects($this->any())
                ->method('get_columns')
                ->will($this->returnValue(array(
                'foobar_c' => array (
                    'name' => 'FooBar_c',
                    'type' => 'varchar',
                    'len' => '255',
                    ),
                )));
        // set the new db helper
        $GLOBALS['db']->helper = $helper;

        $repair = $df->repairCustomFields(false);
        $this->assertEquals("", $repair);

        // reset the db helper
        $GLOBALS['db']->helper = null;
    }
}


// test bean class
require_once("include/SugarObjects/templates/company/Company.php");

// Account is used to store account information.
class TestBean extends Company {
}
