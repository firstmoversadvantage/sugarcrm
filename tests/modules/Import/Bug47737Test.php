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



 
require_once('modules/Import/Importer.php');

class Bug47737Test extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // if beanList got unset, set it back
        if (!isset($GLOBALS['beanList'])) {
            require('include/modules.php');
            $GLOBALS['beanList'] = $beanList;
        }
    }

    public function tearDown()
    {
        restore_error_handler();
    }

    public function providerIdData()
    {
        return array(
            //Valid ids
            array('12345','12345'),
            array('12345-6789-1258','12345-6789-1258'),
            array('aaaBBB12AA122cccD','aaaBBB12AA122cccD'),
            array('aaa-BBB-12AA122-cccD','aaa-BBB-12AA122-cccD'),
            array('aaa_BBB_12AA122_cccD','aaa_BBB_12AA122_cccD'),
            //Invalid
            array('1242','12*'),
            array('abdcd36','abdcd$'),
            array('1234-asdf3535353523','1234-asdf####23'),
            );
    }

    /**
     * @ticket 47737
     * @dataProvider providerIdData
     */
    public function testConvertID($expected, $dirty)
    {
        $c = new Contact();
        $importer = new ImporterStub('UNIT TEST',$c);
        $actual = $importer->convertID($dirty);

        $this->assertEquals($expected, $actual, "Error converting id during import process $actual , expected: $expected, before conversion: $dirty");
    }

}


class ImporterStub extends Importer
{

    public function convertID($id)
    {
        return $this->_convertId($id);
    }
}