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

 
require_once 'include/utils.php';

class SugarArrayMergeTest extends Sugar_PHPUnit_Framework_TestCase
{
    /**
     * @ticket 17142
     */
    public function testSubArrayOrderIsPreserved() 
    {
        $array1 = array(
            'dog' => array(
                'dog1' => 'dog1',
                'dog2' => 'dog2',
                'dog3' => 'dog3',
                'dog4' => 'dog4',
                )
            );
        
        $array2 = array(
            'dog' => array(
                'dog2' => 'dog2',
                'dog1' => 'dog1',
                'dog3' => 'dog3',
                'dog4' => 'dog4',
                )
            );
        
        $results = sugarArrayMerge($array1,$array2);
        
        $keys1 = array_keys($results['dog']);
        $keys2 = array_keys($array2['dog']);
        
        for ( $i = 0; $i < 4; $i++ ) {
            $this->assertEquals($keys1[$i],$keys2[$i]);
        }
    }
    
    public function testSugarArrayMergeMergesTwoArraysWithLikeKeysOverwritingExistingKeys()
    {
        $foo = array(
            'one' => 123,
            'two' => 123,
            'foo' => array(
                'int' => 123,
                'foo' => 'bar',
            ),
        );
        $bar = array(
            'one' => 123,
            'two' => 321,
            'foo' => array(
                'int' => 123,
                'bar' => 'foo',
            ),
        );
        
        $expected = array(
            'one' => 123, 
            'two' => 321,
            'foo' => array(
                'int' => 123,
                'foo' => 'bar',
                'bar' => 'foo',
            ),
        );
        $this->assertEquals(sugarArrayMerge($foo, $bar), $expected);
        // insure that internal functions can't duplicate behavior
        $this->assertNotEquals(array_merge($foo, $bar), $expected);
        $this->assertNotEquals(array_merge_recursive($foo, $bar), $expected);
    }
}
