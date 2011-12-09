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


require_once('ModuleInstall/ModuleInstaller.php');

class ExtTest extends Sugar_PHPUnit_Framework_TestCase
{
    protected $module_installer;

    public static function setUpBeforeClass()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['current_user']->is_admin = "1";
        $GLOBALS['current_language'] = "en_us";
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
        $GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], 'Administration');
        mkdir_recursive("cache/ExtTest");
    }

	public function setUp()
	{
        $this->module_installer = new ModuleInstaller();
        $this->module_installer->silent = true;
        $this->module_installer->base_dir = "cache/ExtTest";
        $this->module_installer->id_name = 'ExtFrameworkTest';
        $this->testvalue = uniqid("ext", true);
        file_put_contents($this->module_installer->base_dir."/test.ext.php", "<?php \$testvalue = '$this->testvalue';");
	}

	public function tearDown()
	{
	    if($this->module_installer) {
	        $this->module_installer->uninstall_extensions();
	    }
	    if(file_exists($this->module_installer->base_dir."/test.ext.php")) {
	        @unlink($this->module_installer->base_dir."/test.ext.php");
	    }
	}

	public static function tearDownAfterClass()
	{
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        unset($GLOBALS['current_language']);
        unset($GLOBALS['app_strings']);
        unset($GLOBALS['mod_strings']);
	    if(file_exists("cache/ExtTest/test.ext.php")) {
	        @unlink("cache/ExtTest/test.ext.php");
	    }
        rmdir_recursive("cache/ExtTest");
	}

    public function getExt()
    {
        include 'ModuleInstall/extensions.php';
        $params = array();
        foreach($extensions as $name => $ext) {
            if($name == 'modules') continue;
            $params[] = array($name, $ext['section'], $ext['extdir'], $ext['file'], isset($ext['module'])?$ext['module']:'');
        }
        return $params;
    }

    /**
     * @dataProvider getExt
     * @param string $extname
     * @param string $section
     * @param string $dir
     * @param string $file
     * @param string $module
     */
    public function testExtFramework($extname, $section, $extdir, $file, $module = '')
    {
        if(empty($module)) {
            $module = 'application';
        }
        $this->module_installer->installdefs[$section] = array(
            array("from" => '<basepath>/test.ext.php', 'to_module' => $module)
        );
        $prefix = '';
        $srcFileName = "test.ext.php";
        if($extname == 'languages') {
            $this->module_installer->installdefs[$section][0]['language'] = 'en_us';
            $prefix = 'en_us.';
            $file = 'lang.ext.php';
            $srcFileName = "ExtFrameworkTest.php";
        }
	    if($module == 'application') {
            $srcfile = "custom/Extension/application/Ext/$extdir/{$prefix}{$srcFileName}";
            $dstfile = "custom/application/Ext/$extdir/{$prefix}$file";
        } else {
            $srcfile = "custom/Extension/modules/$module/Ext/$extdir/{$prefix}{$srcFileName}";
            $dstfile = "custom/modules/$module/Ext/$extdir/{$prefix}$file";
        }
        $this->module_installer->install_extensions();
        // check file is there
        $this->assertFileExists($srcfile);
        $testvalue = null;
        // check it works
        include($dstfile);
        $this->assertEquals($this->testvalue, $testvalue);
        $testvalue = null;
        // check disable
        $this->module_installer->disable_extensions();
        if(file_exists($dstfile)) include($dstfile);
        $this->assertNull($testvalue);
        // check enable
        $this->module_installer->enable_extensions();
        $this->assertFileExists($srcfile);
        include($dstfile);
        $this->assertEquals($this->testvalue, $testvalue);
        $testvalue = null;
        // check uninstall
        $this->module_installer->uninstall_extensions();
        if(file_exists($dstfile)) include($dstfile);
        $this->assertNull($testvalue);
    }

    public function testExtModules()
    {
        $this->module_installer->installdefs['beans'] = array(
            array(
                'module' => 'ExtFrameworkTest',
                'class' =>  'ExtFrameworkTest',
                'path' =>  'ExtFrameworkTest',
                'tab' => true
            )
        );
        $srcfile = "custom/Extension/application/Ext/Include/ExtFrameworkTest.php";
        $dstfile = "custom/application/Ext/Include/modules.ext.php";
        $this->module_installer->install_extensions();
        // check file is there
        $this->assertFileExists($srcfile);
        $beanList = null;
        // check it works
        include($dstfile);
        $this->assertEquals('ExtFrameworkTest', $beanList['ExtFrameworkTest']);
        // check disable
        $this->module_installer->disable_extensions();
        $beanList = array();
        if(file_exists($dstfile)) include($dstfile);
        $this->assertArrayNotHasKey('ExtFrameworkTest', $beanList);
        // check enable
        $beanList = array();
        $this->module_installer->enable_extensions();
        $this->assertFileExists($srcfile);
        include($dstfile);
        $this->assertEquals('ExtFrameworkTest', $beanList['ExtFrameworkTest']);
        $beanList = array();
        // check uninstall
        $this->module_installer->uninstall_extensions();
        if(file_exists($dstfile)) include($dstfile);
        $this->assertArrayNotHasKey('ExtFrameworkTest', $beanList);
    }
}
