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


require_once 'include/SugarObjects/SugarConfig.php';
require_once 'include/SugarObjects/VardefManager.php';

/**
 * @group bug32797
 */
class Bug32797Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $_old_sugar_config = null;

    public function setUp()
    {
        $this->_old_sugar_config = $GLOBALS['sugar_config'];
        $GLOBALS['sugar_config'] = array('require_accounts' => false);
    }

    public function tearDown()
    {
        $config = SugarConfig::getInstance();
        $config->clearCache();
        $GLOBALS['sugar_config'] = $this->_old_sugar_config;
    }

    public function vardefProvider()
    {
        return array(
            array(
                array('fields' => array('account_name' => array('required' => true))),
                array('fields' => array('account_name' => array('required' => false)))
            ),
            array(
                array('fields' => array('account_name' => array('required' => false))),
                array('fields' => array('account_name' => array('required' => false)))
            ),
            array(
                array('fields' => array('account_name' => array('required' => null))),
                array('fields' => array('account_name' => array('required' => false)))
            ),
            array(
                array('fields' => array('account_name' => array())),
                array('fields' => array('account_name' => array()))
            ),
            array(
                array('fields' => array()),
                array('fields' => array())
            )
        );
    }

    /**
     * @dataProvider vardefProvider
     */
    public function testApplyGlobalAccountRequirements($vardef, $vardefToCompare)
    {
        $this->assertEquals($vardefToCompare, VardefManager::applyGlobalAccountRequirements($vardef));
    }

    public function vardefProvider1()
    {
        return array(
            array(
                array('fields' => array('account_name' => array('required' => true))),
                array('fields' => array('account_name' => array('required' => true)))
            ),
            array(
                array('fields' => array('account_name' => array('required' => false))),
                array('fields' => array('account_name' => array('required' => true)))
            )
        );
    }

    /**
     * @dataProvider vardefProvider1
     */
    public function testApplyGlobalAccountRequirements1($vardef, $vardefToCompare)
    {
        $GLOBALS['sugar_config']['require_accounts'] = true;
        $this->assertEquals($vardefToCompare, VardefManager::applyGlobalAccountRequirements($vardef));
    }

    public function vardefProvider2()
    {
        return array(
            array(
                array('fields' => array('account_name' => array('required' => true))),
                array('fields' => array('account_name' => array('required' => true)))
            ),
            array(
                array('fields' => array('account_name' => array('required' => false))),
                array('fields' => array('account_name' => array('required' => false)))
            )
        );
    }

    /**
     * @dataProvider vardefProvider2
     */
    public function testApplyGlobalAccountRequirements2($vardef, $vardefToCompare)
    {
        unset($GLOBALS['sugar_config']['require_accounts']);
        $this->assertEquals($vardefToCompare, VardefManager::applyGlobalAccountRequirements($vardef));
    }
}