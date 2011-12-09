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

 
require_once('modules/UpgradeWizard/uw_utils.php');

class Bug42622Test extends Sugar_PHPUnit_Framework_TestCase 
{
	var $has_notification_studio_file = false;
	var $studio_file = 'modules/Notifications/metadata/studio.php';
	var $backup_file;
	
	public function setUp() 
	{
		
		if(file_exists($this->studio_file))
		{
		   //This really shouldn't be happening, but just in case...
		   $this->has_notification_studio_file = true;
		   $this->backup_file = $this->studio_file . '.' . gmmktime() . '.bak';
		   copy($this->studio_file, $this->backup_file);
		} else {
		   if(!file_exists('modules/Notifications/metadata'))
		   {
		   	  mkdir_recursive('modules/Notifications/metadata');
		   }
		   //Create the test file
		   write_array_to_file("test", array(), $this->studio_file);
		}
	}
	
	public function tearDown() 
	{
		if($this->has_notification_studio_file)
		{
		   copy($this->backup_file, $this->studio_file);
		   unlink($this->backup_file);
		} else {
		   if(file_exists($this->studio_file))
		   {
		   		unlink($this->studio_file);
		   }
		}
	}

	public function testUnlinkUpgradeFilesPre620()
	{
		$this->assertTrue(file_exists($this->studio_file), 'Assert the ' . $this->studio_file . ' exists');
		unlinkUpgradeFiles('613');
		$this->assertFalse(file_exists($this->studio_file), 'Assert the ' . $this->studio_file . ' no longer exists');
	}	
	
	public function testUnlinkUpgradeFilesPost620()
	{
		$this->assertTrue(file_exists($this->studio_file), 'Assert the ' . $this->studio_file . ' exists');
		unlinkUpgradeFiles('620');
		$this->assertTrue(file_exists($this->studio_file), 'Assert the ' . $this->studio_file . ' still exists (post 620)');
	}
}