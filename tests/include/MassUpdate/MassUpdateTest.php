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

 
require_once 'include/MassUpdate.php';
require_once 'include/dir_inc.php';

class MassUpdateTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
		$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
		$GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        unset($GLOBALS['app_strings']);
    }
    
    /**
     * @ticket 12300
     */
    public function testAdddateWorksWithMultiByteCharacters()
    {
        $mass = new MassUpdate();
        $displayname = "开始日期:";
        $varname = "date_start";
        
        $result = $mass->addDate($displayname , $varname);
        $pos_f = strrpos($result, $GLOBALS['app_strings']['LBL_MASSUPDATE_DATE']);
        $this->assertTrue((bool) $pos_f);
    }
    
    /**
     * @ticket 23900
     */
    public function testAddStatus() 
    {
        $mass = new MassUpdate();
        $options = array (
            '10' => 'ten',
            '20' => 'twenty',
            '30' => 'thirty',
            );
        $result = $mass->addStatus('test_dom', 'test_dom', $options);
        preg_match_all('/value=[\'\"].*?[\'\"]/si', $result, $matches);
       /* $this->assertTrue(isset($matches));
        $this->assertTrue($matches[0][0] == "value=''");
        $this->assertTrue($matches[0][2] == "value='10'");
        $this->assertTrue($matches[0][3] == "value='20'"); */
        $this->assertTrue($matches[0][0] == "value=''");
        $this->assertTrue($matches[0][1] == "value='__SugarMassUpdateClearField__'");
        $this->assertTrue($matches[0][2] == "value='10'");
        $this->assertTrue($matches[0][3] == "value='20'");
        $this->assertTrue($matches[0][4] == "value='30'");       	
    }
    
    /**
     * @ticket 23900
     */
    public function testAddStatusMulti() 
    {
        $mass = new MassUpdate();
        $options = array (
            '10' => 'ten',
            '20' => 'twenty',
            '30' => 'thirty',
            );
        
        $result = $mass->addStatusMulti('test_dom', 'test_dom', $options);
        preg_match_all('/value=[\'\"].*?[\'\"]/si', $result, $matches);
        $this->assertTrue(isset($matches));
        /*$this->assertTrue($matches[0][0] == "value=''");
        $this->assertTrue($matches[0][2] == "value='10'");
        $this->assertTrue($matches[0][3] == "value='20'"); */
        $this->assertTrue($matches[0][0] == "value=''");
        $this->assertTrue($matches[0][1] == "value='__SugarMassUpdateClearField__'");
        $this->assertTrue($matches[0][2] == "value='10'");
        $this->assertTrue($matches[0][3] == "value='20'");
        $this->assertTrue($matches[0][4] == "value='30'");       	
    }
}
