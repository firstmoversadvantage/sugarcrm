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

 
require_once 'include/utils/file_utils.php';

class SugarFileUtilsTest extends Sugar_PHPUnit_Framework_TestCase 
{
    private $_filename;
    private $_old_default_permissions;
    
    public function setUp() 
    {	
        if (is_windows())
            $this->markTestSkipped('Skipping on Windows');
        
        $this->_filename = realpath(dirname(__FILE__).'/../../../cache/').'file_utils_override'.mt_rand().'.txt';
        touch($this->_filename);
        $this->_old_default_permissions = $GLOBALS['sugar_config']['default_permissions'];
        $GLOBALS['sugar_config']['default_permissions'] =
            array (
                'dir_mode' => 0777,
                'file_mode' => 0660,
                'user' => $this->_getCurrentUser(),
                'group' => $this->_getCurrentGroup(),
              );
    }
    
    public function tearDown() 
    {
        if(file_exists($this->_filename)) {
            unlink($this->_filename);
        }
        $GLOBALS['sugar_config']['default_permissions'] = $this->_old_default_permissions;
        SugarConfig::getInstance()->clearCache();
    }
    
    private function _getCurrentUser()
    {
        if ( function_exists('posix_getuid') ) {
            return posix_getuid();
        }
        return '';
    }
    
    private function _getCurrentGroup()
    {
        if ( function_exists('posix_getgid') ) {
            return posix_getgid();
        }
        return '';
    }
    
    private function _getTestFilePermissions()
    {
        return substr(sprintf('%o', fileperms($this->_filename)), -4);
    }
    
    public function testSugarTouch()
    {
        $this->assertTrue(sugar_touch($this->_filename));
    }
    
    public function testSugarTouchWithTime()
    {
        $time = filemtime($this->_filename);
        
        $this->assertTrue(sugar_touch($this->_filename, $time));
        
        $this->assertEquals($time,filemtime($this->_filename));
    }
    
    public function testSugarTouchWithAccessTime()
    {
        $time  = filemtime($this->_filename);
        $atime = gmmktime();
        
        $this->assertTrue(sugar_touch($this->_filename, $time, $atime));
        
        $this->assertEquals($time,filemtime($this->_filename));
        $this->assertEquals($atime,fileatime($this->_filename));
    }
    
    public function testSugarChmod()
    {
    	return true;
        $this->assertTrue(sugar_chmod($this->_filename));
        $this->assertEquals($this->_getTestFilePermissions(),decoct(get_mode()));
    }
    
    public function testSugarChmodWithMode()
    {
        $mode = 0411;
        $this->assertTrue(sugar_chmod($this->_filename, $mode));
        
        $this->assertEquals($this->_getTestFilePermissions(),decoct($mode));
    }
    
    public function testSugarChmodNoDefaultMode()
    {
        $GLOBALS['sugar_config']['default_permissions']['file_mode'] = null;
        $this->assertFalse(sugar_chmod($this->_filename));
    }
    
    public function testSugarChmodDefaultModeNotAnInteger()
    {
        $GLOBALS['sugar_config']['default_permissions']['file_mode'] = '';
        $this->assertFalse(sugar_chmod($this->_filename));
    }
    
    public function testSugarChmodDefaultModeIsZero()
    {
        $GLOBALS['sugar_config']['default_permissions']['file_mode'] = 0;
        $this->assertFalse(sugar_chmod($this->_filename));
    }
    
    public function testSugarChmodWithModeNoDefaultMode()
    {
        $GLOBALS['sugar_config']['default_permissions']['file_mode'] = null;
        $mode = 0411;
        $this->assertTrue(sugar_chmod($this->_filename, $mode));
        
        $this->assertEquals($this->_getTestFilePermissions(),decoct($mode));
    }
    
    public function testSugarChmodWithModeDefaultModeNotAnInteger()
    {
        $GLOBALS['sugar_config']['default_permissions']['file_mode'] = '';
        $mode = 0411;
        $this->assertTrue(sugar_chmod($this->_filename, $mode));
        
        $this->assertEquals($this->_getTestFilePermissions(),decoct($mode));
    }
    
    public function testSugarChown()
    {
        $this->assertTrue(sugar_chown($this->_filename));
        $this->assertEquals(fileowner($this->_filename),$this->_getCurrentUser());
    }
    
    public function testSugarChownWithUser()
    {
        $this->assertTrue(sugar_chown($this->_filename,$this->_getCurrentUser()));
        $this->assertEquals(fileowner($this->_filename),$this->_getCurrentUser());
    }
    
    public function testSugarChownNoDefaultUser()
    {
        $GLOBALS['sugar_config']['default_permissions']['user'] = '';
        
        $this->assertFalse(sugar_chown($this->_filename));
    }
    
    public function testSugarChownWithUserNoDefaultUser()
    {
        $GLOBALS['sugar_config']['default_permissions']['user'] = '';
        
        $this->assertTrue(sugar_chown($this->_filename,$this->_getCurrentUser()));
        
        $this->assertEquals(fileowner($this->_filename),$this->_getCurrentUser());
    }
}
?>
