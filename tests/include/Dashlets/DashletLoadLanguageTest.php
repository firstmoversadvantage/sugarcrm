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

require_once 'include/Dashlets/Dashlet.php';

class DashletLoadLanguageTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_moduleName;
    
    public function setup()
    {
        $GLOBALS['dashletStrings'] = array();
        $this->_moduleName = 'TestModuleForDashletLoadLanguageTest'.mt_rand();
    }
    
    public function tearDown()
    {
        if ( is_dir("modules/{$this->_moduleName}") )
            rmdir_recursive("modules/{$this->_moduleName}");
        if ( is_dir("custom/modules/{$this->_moduleName}") )
            rmdir_recursive("custom/modules/{$this->_moduleName}");
        
        unset($GLOBALS['dashletStrings']);
        $GLOBALS['current_language'] = $GLOBALS['sugar_config']['default_language'];
    }
    
    public function testCanLoadCurrentLanguageAppStrings() 
    {
        $GLOBALS['current_language'] = 'en_us';
        sugar_mkdir("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/",null,true);
        sugar_file_put_contents("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.en_us.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "bar"; ?>');
        
        $dashlet = new Dashlet(0);
        $dashlet->loadLanguage('TestModuleDashlet',"modules/{$this->_moduleName}/Dashlets/");
        
        $this->assertEquals("bar",$dashlet->dashletStrings["foo"]);
    }
    
    public function testCanLoadCustomLanguageAppStrings() 
    {
        $GLOBALS['current_language'] = 'en_us';
        sugar_mkdir("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/",null,true);
        sugar_file_put_contents("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.en_us.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "bar"; ?>');
        create_custom_directory("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/");
        sugar_file_put_contents("custom/modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.en_us.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "barbar"; ?>');
        
        $dashlet = new Dashlet(0);
        $dashlet->loadLanguage('TestModuleDashlet',"modules/{$this->_moduleName}/Dashlets/");
        
        $this->assertEquals("barbar",$dashlet->dashletStrings["foo"]);
    }
    
    public function testCanLoadCustomLanguageAppStringsWhenThereIsNoNoncustomLanguageFile() 
    {
        $GLOBALS['current_language'] = 'en_us';
        create_custom_directory("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/");
        sugar_file_put_contents("custom/modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.en_us.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "barbar"; ?>');
        
        $dashlet = new Dashlet(0);
        $dashlet->loadLanguage('TestModuleDashlet',"modules/{$this->_moduleName}/Dashlets/");
        
        $this->assertEquals("barbar",$dashlet->dashletStrings["foo"]);
    }
    
    public function testCanLoadCurrentLanguageAppStringsWhenNotEnglish() 
    {
        $GLOBALS['current_language'] = 'FR_fr';
        sugar_mkdir("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/",null,true);
        sugar_file_put_contents("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.en_us.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "bar"; ?>');
        sugar_mkdir("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/",null,true);
        sugar_file_put_contents("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.FR_fr.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "barrie"; ?>');
        
        $dashlet = new Dashlet(0);
        $dashlet->loadLanguage('TestModuleDashlet',"modules/{$this->_moduleName}/Dashlets/");
        
        $this->assertEquals("barrie",$dashlet->dashletStrings["foo"]);
    }
    
    public function testCanLoadEnglishLanguageAppStringsWhenCurrentLanguageDoesNotExist() 
    {
        $GLOBALS['current_language'] = 'FR_fr';
        sugar_mkdir("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/",null,true);
        sugar_file_put_contents("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.en_us.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "bar"; ?>');
        
        $dashlet = new Dashlet(0);
        $dashlet->loadLanguage('TestModuleDashlet',"modules/{$this->_moduleName}/Dashlets/");
        
        $this->assertEquals("bar",$dashlet->dashletStrings["foo"]);
    }
    
    public function testCanLoadCustomEnglishLanguageAppStringsWhenCurrentLanguageDoesNotExist() 
    {
        $GLOBALS['current_language'] = 'FR_fr';
        sugar_mkdir("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/",null,true);
        sugar_file_put_contents("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.en_us.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "bar"; ?>');
        create_custom_directory("modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/");
        sugar_file_put_contents("custom/modules/{$this->_moduleName}/Dashlets/TestModuleDashlet/TestModuleDashlet.en_us.lang.php",
            '<?php $dashletStrings["TestModuleDashlet"]["foo"] = "barbarbar"; ?>');
        
        $dashlet = new Dashlet(0);
        $dashlet->loadLanguage('TestModuleDashlet',"modules/{$this->_moduleName}/Dashlets/");
        
        $this->assertEquals("barbarbar",$dashlet->dashletStrings["foo"]);
    }
}
