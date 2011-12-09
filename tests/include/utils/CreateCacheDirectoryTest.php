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

class CreateCacheDirectoryTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_original_cwd = '';
    
    public function setUp() 
    {
        $this->_original_cwd = getcwd();
        chdir(dirname(__FILE__));
        $this->_removeCacheDirectory('./cache');
    }

    public function tearDown() 
    {
        $this->_removeCacheDirectory('./cache');
        chdir($this->_original_cwd);
    }

    private function _removeCacheDirectory($dir)
    {
        $dir_handle = @opendir($dir);
        if ($dir_handle === false) {
            return;
        }
        while (($filename = readdir($dir_handle)) !== false) {
            if ($filename == '.' || $filename == '..') {
                continue;
            }
            if (is_dir("{$dir}/{$filename}")) {
                $this->_removecacheDirectory("{$dir}/{$filename}");
            } else {
                unlink("{$dir}/{$filename}");
            }
        }
        closedir($dir_handle);
        rmdir("{$dir}");
    }

    public function testCreatesCacheDirectoryIfDoesnotExist()
    {
        $this->assertFalse(file_exists('./cache'), 'check that the cache directory does not exist');
        create_cache_directory('foobar');
        $this->assertTrue(file_exists('./cache'), 'creates a cache directory');
    }

    public function testCreatesDirectoryInCacheDirectoryProvidedItIsGivenAFile()
    {
        $this->assertFalse(file_exists('./cache/foobar-test'));
        create_cache_directory('foobar-test/cache-file.php');
        $this->assertTrue(file_exists('./cache/foobar-test'));
    }

    public function testReturnsDirectoryCreated()
    {
        $created = create_cache_directory('foobar/cache-file.php');
        $this->assertEquals(
            'cache/foobar/cache-file.php',
            $created
        );
    }
}

