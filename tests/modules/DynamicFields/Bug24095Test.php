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

require_once("modules/Accounts/Account.php");

/**
 * @ticket 24095
 */
class Bug24095Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $_tablename;
    private $_old_installing;
    
    public function setUp()
    {
        $this->accountMockBean = $this->getMock('Account' , array('hasCustomFields'));
        $this->_tablename = 'test' . date("YmdHis");
        if ( isset($GLOBALS['installing']) )
            $this->_old_installing = $GLOBALS['installing'];
        $GLOBALS['installing'] = true;
        
        $GLOBALS['db']->createTableParams($this->_tablename . '_cstm',
            array(
                'id_c' => array (
                    'name' => 'id_c',
                    'type' => 'id',
                    ),
                'foo_c' => array (
                    'name' => 'foo_c',
                    'type' => 'varchar',
                    'len' => '255',
                    ),
                ),
            array()
            );
        $GLOBALS['db']->query("INSERT INTO {$this->_tablename}_cstm (id_c,foo_c) VALUES ('12345','67890')");
    }
    
    public function tearDown()
    {
        $GLOBALS['db']->dropTableName($this->_tablename . '_cstm');
        if ( isset($this->_old_installing) ) {
            $GLOBALS['installing'] = $this->_old_installing;
        }
        else {
            unset($GLOBALS['installing']);
        }
    }
    
    public function testDynamicFieldsRetrieveWorks()
    {
        $bean = $this->accountMockBean;
        $bean->custom_fields = new DynamicField($bean->module_dir);
        $bean->custom_fields->setup($bean);
        $bean->expects($this->any())
             ->method('hasCustomFields')
             ->will($this->returnValue(true));
        $bean->table_name = $this->_tablename;
        $bean->id = '12345';
        $bean->custom_fields->retrieve();
        $this->assertEquals($bean->id_c, '12345');
        $this->assertEquals($bean->foo_c, '67890');
    }
}
