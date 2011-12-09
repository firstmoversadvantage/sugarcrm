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


require_once('include/utils.php');


/**
 * @ticket 33806
 */
class Bug33806Test extends Sugar_PHPUnit_Framework_TestCase
{

    function _moduleNameProvider()
    {
        return array(
            array( 'singular' => 'Account', 'module' => 'Accounts'),
            array( 'singular' => 'Contact', 'module' => 'Contacts'),
        );
    }

    /**
     * Test the getMime function for the use case where the mime type is already provided.
     *
     * @dataProvider _moduleNameProvider
     */
    public function testGetModuleFromSingular($singular, $expectedName)
    {
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);

        $module = get_module_from_singular($singular);

        $this->assertEquals($expectedName, $module);
    }

    function _moduleNameProvider2()
    {
        return array(
            array( 'renamed' => 'Acct', 'module' => 'Accounts'),
        );
    }

    /**
     * Test the getMime function for the use case where the mime type is already provided.
     *
     * @dataProvider _moduleNameProvider2
     */
    public function testGetModuleFromRenamed($renamed, $expectedName)
    {
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);

        // manually rename the module name to 'Acct'
        $GLOBALS['app_list_strings']['moduleList']['Accounts'] = 'Acct';
        
        $module = get_module_from_singular($renamed);

        $this->assertEquals($expectedName, $module);
    }
}
