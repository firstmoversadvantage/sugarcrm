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


require_once('include/Localization/Localization.php');

/**
 * Bug #35413
 * Other character sets not displayed properly
 */
class Bug35413Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $_localization = null;

    function setUp()
    {
        $this->_localization = new Localization();
    }

    function stringsProvider()
    {
        return array(
            array(
                '7cvU3iDI5d7L7O3TIOUg1cfU7N0g3c4g5ezR1NPd5eHU3csg287a',
                'يثشق بهقثىيس ه صاشىف فخ هىرشسفهلشفث غخع',
                'windows-1256'
            ),
            array(
                '7cjT7cjU0+3IwcbExNE=',
                'يبسيبشسيبءئؤؤر',
                'windows-1256'
            )
        );
    }

    /**
     * Test convert base64 $source to string and convert string from $encoding to utf8. It has to return $utf8string.
     *
     * @dataProvider stringsProvider
     * @ticket 35413
     * @param string $source base64 encoded string in native charset
     * @param string $utf8string previous string in utf8
     * @param string $encoding encoding of native string
     */
    public function testEncodings($source, $utf8string, $encoding)
    {
        $source = base64_decode($source);
        $translateCharsetResult = $this->_localization->translateCharset($source, $encoding, 'UTF-8');
        $this->assertEquals($utf8string, $translateCharsetResult, 'Strings have to be the same');
    }
}