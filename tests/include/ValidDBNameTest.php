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

 
require_once("include/utils.php");

class ValidDBNameTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function testShortNameUneffected()
    {
        $this->assertEquals(
            'idx_test_123_id',
            getValidDBName('idx_test_123_id')
        );
    }

    public function testmaxLengthParam()
    {
        $this->assertEquals(
            'idx_test_123_456_789_foo_bar_id',
            getValidDBName('idx_test_123_456_789_foo_bar_id', false, 40)
        );
    }

    public function testEnsureUnique()
    {
        $this->assertEquals(
            getValidDBName('idx_test_123_456_789_foo_bar_id', true),
            getValidDBName('idx_test_123_456_789_foo_bar_id', true)
        );

        $this->assertNotEquals(
            getValidDBName('idx_test_123_456_789_foo_bar_id', true),
            getValidDBName('idx_test_123_446_789_foo_bar_id', true)
        );
    }

    public function testValidMySQLNameReturnsTrue()
    {
        $this->assertTrue(isValidDBName('sugarCRM', 'mysql'));
        $this->assertTrue(isValidDBName('sugar-crm', 'mysql'));
        $this->assertTrue(isValidDBName('sugar_crm', 'mysql'));
        $this->assertTrue(isValidDBName('sugar-crm', 'mysql'));
        $this->assertTrue(isValidDBName('sugar-CRM_ver6', 'mysql'));
    }

    public function testInvalidMySQLNameReturnsFalse()
    {
        $this->assertFalse(isValidDBName('sugar/crm', 'mysql'));
        $this->assertFalse(isValidDBName('sugar\crm', 'mysql'));
        $this->assertFalse(isValidDBName('sugar.crm', 'mysql'));
    }

    public function testValidOracleNameReturnsTrue()
    {
        $this->assertTrue(isValidDBName('sugarCRM', 'oci8'));
        $this->assertTrue(isValidDBName('sugar_crm', 'oci8'));
        $this->assertTrue(isValidDBName('sugarCRM_ver6', 'oci8'));
    }

    public function testInvalidOracleNameReturnsFalse()
    {
        $this->assertFalse(isValidDBName('sugar\CRM', 'oci8'));
        $this->assertFalse(isValidDBName('sugar crm', 'oci8'));
        $this->assertFalse(isValidDBName('sugarCRM_ver#63', 'oci8'));
    }

    public function testValidMSSQLNameReturnsTrue()
    {
        $this->assertTrue(isValidDBName('sugarCRM', 'mssql'));
        $this->assertTrue(isValidDBName('sugar_crm', 'mssql'));
        $this->assertTrue(isValidDBName('sugarCRM_ver6', 'mssql'));
    }

    public function testInvalidMSSQLNameReturnsFalse()
    {
        $this->assertFalse(isValidDBName('622sugarCRM', 'mssql'));
        $this->assertFalse(isValidDBName('sugar crm', 'mssql'));
        $this->assertFalse(isValidDBName('#sugarCRM_ver6', 'mssql'));
    }
    
    public function testLongNameEffected()
    {
        $this->assertNotEquals(
            getValidDBName('eeeee_eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee_opportunities', true),
            getValidDBName('eeeee_eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee1_opportunities', true)
        );
    }
}
