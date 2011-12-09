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


require_once('include/dir_inc.php');
require_once('modules/UpgradeWizard/UpgradeRemoval.php');

class Bug46027Test extends Sugar_PHPUnit_Framework_TestCase 
{

	public function setUp()
	{		
		if(file_exists('custom/backup/include/utils/external_cache'))
		{
			rmdir_recursive('custom/backup/include/utils/external_cache');
			rmdir_recursive('custom/backup/include/utils');
			rmdir_recursive('custom/backup/include');	
		}
		
		if(file_exists('include/JSON.js'))
		{
			unlink('include/JSON.js');
		}		
		
		//Simulate file and directory that should be removed by UpgradeRemove62x.php
		copy('include/JSON.php', 'include/JSON.js');
		mkdir_recursive('include/utils/external_cache');		
	}
	
	/**
	 * ensure that the test directory and file are removed at the end of the test
	 */
	public function tearDown()
	{
		if(file_exists('include/utils/external_cache'))
		{
		   rmdir_recursive('include/utils/external_cache');
		}
		
		if(file_exists('include/JSON.js'))
		{
		   unlink('include/JSON.js');	
		}
		
		if(file_exists('custom/backup/include/utils/external_cache'))
		{
			rmdir_recursive('custom/backup/include/utils/external_cache');
			rmdir_recursive('custom/backup/include/utils');
			rmdir_recursive('custom/backup/include');
		}		
	}
	
	public function testUpgradeRemoval()
	{
		$instance = new UpgradeRemoval62xMock();
		$instance->processFilesToRemove($instance->getFilesToRemove(622));
		$this->assertTrue(!file_exists('include/utils/external_cache'), 'Assert that include/utils/external_cache was removed');
		$this->assertTrue(file_exists('custom/backup/include/utils/external_cache'), 'Assert that the custom/backup/include/utils/external_cache directory was created');		
		$this->assertTrue(!file_exists('include/JSON.js'), 'Assert that include/JSON.js file is removed');
		$this->assertTrue(file_exists('custom/backup/include/JSON.js'), 'Assert that include/JSON.js was moved to custom/backup/include/JSON.js');
	}
	
}

class UpgradeRemoval62xMock extends UpgradeRemoval
{
	
public function getFilesToRemove($version)
{
	$files = array();
	$files[] = 'include/utils/external_cache';
	$files[] = 'include/JSON.js';
	return $files;
}

}
?>