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

class Bug41003Test extends Sugar_PHPUnit_Framework_TestCase
{
    public function providerVerifyStrippingOfBrInBr2nlFunction()
    {
        return array(
            array("here is my text with no newline", "here is my text with no newline"),
            array("here is my text with a newline lowercased\n", "here is my text with a newline lowercased<br>"),
            array("here is my text with a newline mixed case\n", "here is my text with a newline mixed case<Br>"),
            array("here is my text with a newline mixed case with /\n", "here is my text with a newline mixed case with /<Br />"),
            array("here is my text with a newline uppercase\n", "here is my text with a newline uppercase<BR />"),
            array("here is my crappy text éèçàô$*%ù§!#with a newline\n in the middle", "here is my crappy text éèçàô$*%ù§!#with a newline<bR> in the middle"),
            );
    }
    
    /**
     * @dataProvider providerVerifyStrippingOfBrInBr2nlFunction
     */
    public function testVerifyStrippingOfBrInBr2nlFunction($expectedResult,$testString)
    {
        $this->assertEquals($expectedResult, br2nl($testString));
    }
}

