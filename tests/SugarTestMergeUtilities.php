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

 
class SugarTestMergeUtilities
{
    private static $modules = array();
    private static $files = array();
    private static $has_dir = array();
    
    private function __construct() {}

    public static function setupFiles($modules, $files, $custom_directory) 
    {

		   self::$modules = $modules;
		   self::$files = $files;
		   self::$has_dir = array();
		   
		   foreach(self::$modules as $module) {
			   if(!file_exists("custom/modules/{$module}/metadata")){
				  mkdir_recursive("custom/modules/{$module}/metadata", true);
			   }
			   
			   if(file_exists("custom/modules/{$module}")) {
			   	  self::$has_dir[$module] = true;
			   }
			   
			   foreach(self::$files as $file) {
			   	   if(file_exists("custom/modules/{$module}/metadata/{$file}")) {
				   	  copy("custom/modules/{$module}/metadata/{$file}.php", "custom/modules/{$module}/metadata/{$file}.php.bak");
				   }
				   
				   if(file_exists("custom/modules/{$module}/metadata/{$file}.php.suback.php")) {
				      copy("custom/modules/{$module}/metadata/{$file}.php.suback.php", "custom/modules/{$module}/metadata/{$file}.php.suback.bak");
				   }
				   
				   if(file_exists("{$custom_directory}/custom/modules/{$module}/metadata/{$file}.php")) {
				   	  copy("{$custom_directory}/custom/modules/{$module}/metadata/{$file}.php", "custom/modules/{$module}/metadata/{$file}.php");
				   }
			   } //foreach
		   } //foreach    	
    	
    }
    
    public static function teardownFiles() 
    {
		   foreach(self::$modules as $module) {
			   if(!self::$has_dir[$module]) {
			   	  rmdir_recursive("custom/modules/{$module}");
			   }  else {
				   foreach(self::$files as $file) {
				      if(file_exists("custom/modules/{$module}/metadata/{$file}.php.bak")) {
				      	 copy("custom/modules/{$module}/metadata/{$file}.php.bak", "custom/modules/{$module}/metadata/{$file}.php");
			             unlink("custom/modules/{$module}/metadata/{$file}.php.bak");
				      } else if(file_exists("custom/modules/{$module}/metadata/{$file}.php")) {
				      	 unlink("custom/modules/{$module}/metadata/{$file}.php");
				      }
				      
				   	  if(file_exists("custom/modules/{$module}/metadata/{$module}.php.suback.bak")) {
				      	 copy("custom/modules/{$module}/metadata/{$file}.php.suback.bak", "custom/modules/{$module}/metadata/{$file}.php.suback.php");
			             unlink("custom/modules/{$module}/metadata/{$file}.php.suback.bak");
				      } else if(file_exists("custom/modules/{$module}/metadata/{$file}.php.suback.php")) {
				      	 unlink("custom/modules/{$module}/metadata/{$file}.php.suback.php");
				      }  
				   }
			   }
		   } //foreach
    }
    
}