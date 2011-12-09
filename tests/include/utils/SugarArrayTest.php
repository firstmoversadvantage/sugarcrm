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

 
require_once 'include/utils/array_utils.php';

class SugarArrayTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function testCanFindValueUsingDotNotation() 
    {
        $random = rand(100, 200);
        $array = array(
            'foo' => array(
                $random => array(
                    'bar' => $random,
                ),
            ),
        );

        $array = new SugarArray($array);
        $this->assertEquals($array->get("foo.{$random}.bar"), $random);
    }

    public function testReturnsDefaultValueWhenDoesNotContainRequestedValue() 
    {
        $random = rand(100, 200);
        $array = new SugarArray(array());
        $this->assertEquals($array->get('unknown', $random), $random);
    }
    
    public function testImplementsArrayAccess() 
    {
        $reflection = new ReflectionClass('SugarArray');
        $this->assertTrue($reflection->implementsInterface('ArrayAccess'));
    }

    public function testImplementsCountable() 
    {
        $reflection = new ReflectionClass('SugarArray');
        $this->assertTrue($reflection->implementsInterface('Countable'));
    }

    public function testStaticMethodCanTraverseProvidedArray() 
    {
        $random = rand(100, 200);
        $array = array(
            'foo' => array(
                $random => array(
                    'bar' => $random,
                ),
            ),
        );

        $this->assertEquals(SugarArray::staticGet($array, "foo.{$random}.bar"), $random);
    }

    public function testStaticMethodCanReturnDefaultOnUnknownValue() 
    {
        $random = rand(100, 200);
        $this->assertEquals(SugarArray::staticGet(array(123, 321), 'unknown', $random), $random);
    }
    
    public function testAdd_blank_option()
    {
    	$options = 'noneArray';
    	$expectedArray = array(''=>'');
    	$result = add_blank_option($options);
    	$this->assertEquals($result[''], $expectedArray['']);
    	$options2 = array('mason'=>'unittest');
    	$expectedArray2 = array(''=>'','mason'=>'unittest');
    	$result2 = add_blank_option($options2);
    	$this->assertEquals($result2, $expectedArray2);
    }
}

