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

 
class SilentUpgradeSessionVarsTest extends Sugar_PHPUnit_Framework_TestCase 
{
    private $externalTestFileName = 'test_silent_upgrade_vars.php';
    
    public function setUp() 
    {
        $this->writeExternalTestFile();
    }

    public function tearDown() 
    {
        $this->removeExternalTestFile();
    }

    public function testSilentUpgradeSessionVars()
    {
    	
    	require_once('modules/UpgradeWizard/uw_utils.php');
    	
    	$varsCacheFileName = "{$GLOBALS['sugar_config']['cache_dir']}/silentUpgrader/silentUpgradeCache.php";
        
    	$loaded = loadSilentUpgradeVars();
    	$this->assertTrue($loaded, "Could not load the silent upgrade vars");
    	global $silent_upgrade_vars_loaded;
    	$this->assertTrue(!empty($silent_upgrade_vars_loaded), "\$silent_upgrade_vars_loaded array should not be empty");
    	
    	$set = setSilentUpgradeVar('SDizzle', 'BSnizzle');
    	$this->assertTrue($set, "Could not set a silent upgrade var");
    	
    	$get = getSilentUpgradeVar('SDizzle');
    	$this->assertEquals('BSnizzle', $get, "Unexpected value when getting silent upgrade var before resetting");
    	
    	$write = writeSilentUpgradeVars();
    	$this->assertTrue($write, "Could not write the silent upgrade vars to the cache file. Function returned false");
    	$this->assertFileExists($varsCacheFileName, "Cache file doesn't exist after call to writeSilentUpgradeVars()");
    	
    	$output = shell_exec("php {$this->externalTestFileName}");
    	
    	$this->assertEquals('BSnizzle', $output, "Running custom script didn't successfully retrieve the value");
    	
    	$remove = removeSilentUpgradeVarsCache();
    	$this->assertTrue(empty($silent_upgrade_vars_loaded), "Silent upgrade vars variable should have been unset in removeSilentUpgradeVarsCache() call");
    	$this->assertFileNotExists($varsCacheFileName, "Cache file exists after call to removeSilentUpgradeVarsCache()");
    	
    	$get = getSilentUpgradeVar('SDizzle');
    	$this->assertNotEquals('BSnizzle', $get, "Unexpected value when getting silent upgrade var after resetting");
    }
    
    private function writeExternalTestFile()
    {
        $externalTestFileContents = <<<EOQ
<?php
        
        define('sugarEntry', true);
        require_once('include/entryPoint.php');
        require_once('modules/UpgradeWizard/uw_utils.php');
        
        \$get = getSilentUpgradeVar('SDizzle');
        
        echo \$get;
EOQ;
        
        file_put_contents($this->externalTestFileName, $externalTestFileContents);
    }
    
    private function removeExternalTestFile()
    {
        if(file_exists($this->externalTestFileName))
        {
            unlink($this->externalTestFileName);
        }
    }
}
?>
