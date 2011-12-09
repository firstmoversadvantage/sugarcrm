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

class SugarArrayUtilsTest extends Sugar_PHPUnit_Framework_TestCase
{
	
	public function test_array_merge_values()
	{	
		$array1 = array("a","b","c");
		$array2 = array("x","y","z");
		$array3 = array(1, 2, 3);
		$array4 = array("a", "b", "c", "d", "e");
		
		$expectedResult12 = array("ax","by","cz");
		$expectedResult13 = array("a1", "b2", "c3");
		$expectedResult14 = false;
		
		
		$this->assertEquals($expectedResult12, array_merge_values($array1, $array2));
		$this->assertEquals($expectedResult13, array_merge_values($array1, $array3));
		$this->assertEquals($expectedResult14, array_merge_values($array1, $array4));
			
	}
	
	
	public function test_array_search_insensitive()
	{
		$arrayLowerCase = array("alpha","bravo","charlie","delta","echo");
		$arrayUpperCase = array("ALPHA", "BRAVO", "CHARLIE", "DELTA", "ECHO");
		$arrayMixed = array("Alpha","Bravo","Charlie", "Delta", "Echo");
		$arrayEmpty = array();
		
		$this->assertTrue(array_search_insensitive("delta", $arrayLowerCase));
		$this->assertTrue(array_search_insensitive("delta", $arrayUpperCase));
		$this->assertTrue(array_search_insensitive("delta", $arrayMixed));
		$this->assertFalse(array_search_insensitive("delta", $arrayEmpty));	
	}
	
	public function test_object_to_array_recursive()
	{
		$simple = new SimpleObejct();
		
		$notSimple = new NotSimpleObject();
		$notSimple->setFoo(new SimpleObejct());
		$notObject = "foo";
		
		$simpleExpected = array('foo'=>'bar', 'b'=>1);
		$notSimpleExpected = array('foo'=>array('foo'=>'bar', 'b'=>1), 'b'=>1);
		$notObjectExpected = 'foo';
		
		$this->assertEquals($simpleExpected, object_to_array_recursive($simple));
		$this->assertEquals($notSimpleExpected, object_to_array_recursive($notSimple));
		$this->assertEquals($notObjectExpected, object_to_array_recursive($notObject));
		
	}
	
	public function test_overide_value_to_string()
	{
		$name = 'name';
		$value_name = 1;
		$value = 4;
		
		$expected = '$name[1] = 4;';
		
		$this->assertEquals($expected, override_value_to_string($name, $value_name, $value));
		
	}
	
	//To do: test eval == true.
	public function test_override_value_to_string_recursive()
	{
		$key_names = array(1, 2, 3, 4, 5);
		global $array_name; 
		$array_name= 'name';
		$value = 'foo';
	
		
		$expectedNoEval = '$name[1][2][3][4][5]='."'".'foo'."';";
		$expectedEval = true;
		
		$this->assertEquals($expectedNoEval, override_value_to_string_recursive($key_names, $array_name, $value));
		global $name;
		
		$array = override_value_to_string_recursive($key_names, $array_name, $value, true);
	} 
	
	
	//array_name is never used in this function...
	public function test_override_recursive_helper()
	{
		$key_names = array(1, 2, 3, 4, 5);
		$array_name = 'name';
		$value = 'foo';
		
		$expected = '$name[1][2][3][4][5]='."'".$value."';";
		
		$this->assertEquals($expected, override_value_to_string_recursive($key_names, $array_name, $value));	
	} 

	//Todo: hit the if statement
	public function test_setDeepArrayValue()
	{
		$arrayActualSimple = array(1=>'a');
		setDeepArrayValue($arrayActualSimple, 1, 'b');
		$arrayExpectedSimple = array(1=>'b');
		
		$this->assertEquals($arrayExpectedSimple, $arrayActualSimple);	
	}	
}

class SimpleObejct
{
	public $foo = 'bar';
	public $b = 1;
}

class NotSimpleObject
{
	public $foo;
	public $b = 1;
	public function setFoo($input)
	{
		$this->foo = $input;
	}
}
