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

require_once 'ModuleInstall/PackageManager/PackageManager.php';

class Bug39980Test extends Sugar_PHPUnit_Framework_TestCase
{
	
    public function tearDown()
    {
        if (is_file(Bug39980PackageManger::$manifest_location))
            unlink(Bug39980PackageManger::$manifest_location);
    }

    public function testGetinstalledPackagesUninstallable()
    {  
    	$pm = new Bug39980PackageManger();
    	$pm->extractManifest(0, 0);
    	$packs = $pm->getinstalledPackages();
    	//Its confusing, but "UNINSTALLABLE" in file_install means the package is NOT uninstallable
    	$this->assertEquals("UNINSTALLABLE", $packs[0]['file_install']);
    }

}

class Bug39980PackageManger extends PackageManager {
	static $manifest_location = "cache/Bug39980manifest.php";
	
	public function __construct() {
	   parent::__construct();
	   $this->manifest_content = <<<EOQ
<?php
\$manifest = array (
         'acceptable_sugar_versions' => 
          array (
            '6.1.0'
          ),
          'acceptable_sugar_flavors' =>
          array(
            'ENT'
          ),
          'readme'=>'',
          'key'=>'tf1',
          'author' => '',
          'description' => '',
          'icon' => '',
          'is_uninstallable' => false,
          'name' => 'test_file_1',
          'published_date' => '2010-10-20 22:10:01',
          'type' => 'module',
          'version' => '1287612601',
          'remove_tables' => 'prompt',
          );
\$installdefs = array (
  'id' => 'asdfqq',
  'copy' => 
  array (
     0 => array (
      'from' => '<basepath>/Extension/modules/Cases/Ext/Vardefs/dummy_extension2.php',
      'to' => 'custom/Extension/modules/Cases/Ext/Vardefs/dummy_extension2.php',
    ),
  ),
);    
        
EOQ;
	}

	public function getInstalled($types)
	{
		include($this->extractManifest(0,0));
		$sm = array(
		    'manifest'         => (isset($manifest) ? $manifest : ''),
            'installdefs'      => (isset($installdefs) ? $installdefs : ''),
            'upgrade_manifest' => (isset($upgrade_manifest) ? $upgrade_manifest : '')
		);
		return array (
			(object) array(
			    'filename' => Bug39980PackageManger::$manifest_location,
			    'manifest' => base64_encode(serialize($sm)),
			    'date_entered' => '1/1/2010',
			    'new_schema' => '1',
			    'module_dir' => 'Administration' ,
			    'id' => 'b4d22740-4e96-65b3-b712-4ca230d95987' ,
			    'md5sum' => 'fe221d731d8c624f15712878300aa907' ,
			    'type' => 'module' ,
			    'version' => '1285697780' ,
			    'status' => 'installed' ,
			    'name' => 'test_file_1' ,
			    'description' => '' ,
			    'id_name' => 'tf1' ,
			    'enabled' => true ,
			)
		);
	}
	
	public function extractManifest($filename, $base_tmp_upgrade_dir)
	{
	   if (!is_file(Bug39980PackageManger::$manifest_location))
	       file_put_contents(Bug39980PackageManger::$manifest_location, $this->manifest_content);
	   
	   return Bug39980PackageManger::$manifest_location;
	}
}