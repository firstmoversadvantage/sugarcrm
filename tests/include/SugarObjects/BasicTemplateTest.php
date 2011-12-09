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

 
require_once 'include/SugarObjects/templates/basic/Basic.php';

class BasicTemplateTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_bean;
    
    public function setUp()
    {
        $this->_bean = new Basic;
    }
    
    public function tearDown()
    {
        unset($this->_bean);
    }
    
    public function testNameIsReturnedAsSummaryText()
    {
        $this->_bean->name = 'teststring';
        $this->assertEquals($this->_bean->get_summary_text(),$this->_bean->name);
    }
    
    /**
     * @ticket 27361
     */
    public function testSettingImportableFieldDefAttributeTrueAsAString()
    {
        $this->_bean->field_defs['date_entered']['importable'] = 'true';
        $this->assertTrue(array_key_exists('date_entered',$this->_bean->get_importable_fields()),
            'Field date_entered should be importable');
    }
    
    /**
     * @ticket 27361
     */
    public function testSettingImportableFieldDefAttributeTrueAsABoolean()
    {
        $this->_bean->field_defs['date_entered']['importable'] = true;
        $this->assertTrue(array_key_exists('date_entered',$this->_bean->get_importable_fields()),
            'Field date_entered should be importable');
    }
    
    /**
     * @ticket 27361
     */
    public function testSettingImportableFieldDefAttributeFalseAsAString()
    {
        $this->_bean->field_defs['date_entered']['importable'] = 'false';
        $this->assertFalse(array_key_exists('date_entered',$this->_bean->get_importable_fields()),
            'Field date_entered should not be importable');
    }
    
    /**
     * @ticket 27361
     */
    public function testSettingImportableFieldDefAttributeFalseAsABoolean()
    {
        $this->_bean->field_defs['date_entered']['importable'] = false;
        $this->assertFalse(array_key_exists('date_entered',$this->_bean->get_importable_fields()),
            'Field date_entered should not be importable');
    }
    
    public function testGetBeanFieldsAsAnArray()
    {
        $this->_bean->date_entered = '2009-01-01 12:00:00';
        $array = $this->_bean->toArray();
        $this->assertEquals($array['date_entered'],$this->_bean->date_entered);
    }
}
