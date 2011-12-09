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

 
require_once('modules/DynamicFields/FieldCases.php');

/**
 * @group DynamicFieldsCurrencyTests
 */

class DynamicFieldsCurrencyTests extends Sugar_PHPUnit_Framework_TestCase
{
    private $_modulename = 'Accounts';
    private $_originaldbType = '';
    private $field;
    
    public function setUp()
    {
        // Set Original Global dbType
        $this->_originaldbType = $GLOBALS['db']->dbType;
        
    	$this->field = get_widget('currency');
        $this->field->id = $this->_modulename.'foofighter_c';
        $this->field->name = 'foofighter_c';
        $this->field->vanme = 'LBL_Foo';
        $this->field->comments = NULL;
        $this->field->help = NULL;
        $this->field->custom_module = $this->_modulename;
        $this->field->type = 'currency';
        $this->field->len = 18;
        $this->field->precision = 6;
        $this->field->required = 0;
        $this->field->default_value = NULL;
        $this->field->date_modified = '2010-12-22 01:01:01';
        $this->field->deleted = 0;
        $this->field->audited = 0;
        $this->field->massupdate = 0;
        $this->field->duplicate_merge = 0;
        $this->field->reportable = 1;
        $this->field->importable = 'true';
        $this->field->ext1 = NULL;
        $this->field->ext2 = NULL;
        $this->field->ext3 = NULL;
        $this->field->ext4 = NULL;
    }
    
    public function tearDown()
    {
        // Reset Original Global dbType
        $GLOBALS['db']->dbType = $this->_originaldbType;
    }
    
    public function testCurrencyDbType()
    {
        // oci8 - number
        $GLOBALS['db']->dbType = 'oci8';
        $this->field->len = NULL;
        $dbTypeString = $this->field->get_db_type();
        $this->assertRegExp('/number *\(/', $dbTypeString);
        $dbTypeString = $this->field->get_db_type();
        $this->field->len = 20;
        $this->assertRegExp('/number *\(/', $dbTypeString);
        
        // mssql - decimal
        $GLOBALS['db']->dbType = 'mssql';
        $this->field->len = NULL;
        $dbTypeString = $this->field->get_db_type();
        $this->assertRegExp('/decimal *\(/', $dbTypeString);
        $this->field->len = 20;
        $dbTypeString = $this->field->get_db_type();
        $this->assertRegExp('/decimal *\(/', $dbTypeString);
        
        // default - decimal
        $GLOBALS['db']->dbType = 'mssql';
        $this->field->len = NULL;
        $dbTypeString = $this->field->get_db_type();
        $this->assertRegExp('/decimal *\(/', $dbTypeString);
        $this->field->len = 20;
        $dbTypeString = $this->field->get_db_type();
        $this->assertRegExp('/decimal *\(/', $dbTypeString);
    }
}
