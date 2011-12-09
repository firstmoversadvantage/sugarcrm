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

 
require_once('include/MVC/View/SugarView.php');

class LoadMenuTest extends Sugar_PHPUnit_Framework_TestCase
{
    protected $_moduleName;

    public function setUp()
	{
		global $mod_strings, $app_strings;
		$mod_strings = return_module_language($GLOBALS['current_language'], 'Accounts');
		$app_strings = return_application_language($GLOBALS['current_language']);

		$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();

		// create a dummy module directory
		$this->_moduleName = 'TestModule'.mt_rand();

        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();

        sugar_mkdir("modules/{$this->_moduleName}",null,true);
	}

	public function tearDown()
	{
		unset($GLOBALS['mod_strings']);
		unset($GLOBALS['app_strings']);

		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
		unset($GLOBALS['current_user']);
        if(!empty($this->_moduleName)) {
    		if ( is_dir("modules/{$this->_moduleName}") )
    		    rmdir_recursive("modules/{$this->_moduleName}");
    		if ( is_dir("custom/modules/{$this->_moduleName}") )
    		    rmdir_recursive("custom/modules/{$this->_moduleName}");
        }
		unset($GLOBALS['current_user']);
	}

	public function testMenuDoesNotExists()
	{
        $view = new SugarView;
        $module_menu = $view->getMenu($this->_moduleName);
        $this->assertTrue(empty($module_menu),'Assert the module menu array is empty');
	}
	
	/**
	 * @ticket 43497
	 */
	public function testMenuExistsCanFindModuleMenu()
	{
	    // Create module menu
        if( $fh = @fopen("modules/{$this->_moduleName}/Menu.php", 'w+') ) {
	        $string = <<<EOQ
<?php
\$module_menu[]=Array("index.php?module=Import&action=bar&import_module=Accounts&return_module=Accounts&return_action=index","Foo","Foo", 'Accounts');
?>
EOQ;
            fputs( $fh, $string);
            fclose( $fh );
        }

        $view = new SugarView;
        $module_menu = $view->getMenu($this->_moduleName);
        $found_menu = false;
        $found_menu_twice = false;
        foreach ($module_menu as $menu_entry) {
        	foreach ($menu_entry as $menu_item) {
        		if (preg_match('/action=bar/', $menu_item)) {
        		    if ( $found_menu ) {
        		        $found_menu_twice = true;
        		    }
        		    $found_menu = true;
        		}
        	}
        }
        
        $this->assertTrue($found_menu, "Assert that menu was detected");
        $this->assertFalse($found_menu_twice, "Assert that menu item wasn't listed twice");
	}

    /**
     * @ticket 29114
     * @ticket 43497
     */
    public function testMenuExistsCanFindModuleExtMenu()
    {
        // Create module ext menu
        sugar_mkdir("custom/modules/{$this->_moduleName}/Ext/Menus/",null,true);
        if( $fh = @fopen("custom/modules/{$this->_moduleName}/Ext/Menus/menu.ext.php", 'w+') ) {
	        $string = <<<EOQ
<?php
\$module_menu[]=Array("index.php?module=Import&action=foo&import_module=Accounts&return_module=Accounts&return_action=index","Foo","Foo", 'Accounts');
?>
EOQ;
            fputs( $fh, $string);
            fclose( $fh );
        }

        $view = new SugarView;
        $module_menu = $view->getMenu($this->_moduleName);
        $found_custom_menu = false;
        $found_custom_menu_twice = false;
        foreach ($module_menu as $key => $menu_entry) {
        	foreach ($menu_entry as $id => $menu_item) {
        		if (preg_match('/action=foo/', $menu_item)) {
        		    if ( $found_custom_menu ) {
        		        $found_custom_menu_twice = true;
        		    }
        		    $found_custom_menu = true;
        		}
        	}
        }
        $this->assertTrue($found_custom_menu, "Assert that custom menu was detected");
        $this->assertFalse($found_custom_menu_twice, "Assert that custom menu item wasn't listed twice");
    }

    /**
     * @ticket 38935
     * @ticket 43497
     */
    public function testMenuExistsCanFindModuleExtMenuWhenModuleMenuDefinedGlobal()
    {
        // Create module ext menu
        sugar_mkdir("custom/modules/{$this->_moduleName}/Ext/Menus/",null,true);
        if( $fh = @fopen("custom/modules/{$this->_moduleName}/Ext/Menus/menu.ext.php", 'w+') ) {
	        $string = <<<EOQ
<?php
global \$module_menu;
\$module_menu[]=Array("index.php?module=Import&action=foo&import_module=Accounts&return_module=Accounts&return_action=index","Foo","Foo", 'Accounts');
?>
EOQ;
            fputs( $fh, $string);
            fclose( $fh );
        }

        $view = new SugarView;
        $module_menu = $view->getMenu($this->_moduleName);
        $found_custom_menu = false;
        $found_custom_menu_twice = false;
        foreach ($module_menu as $key => $menu_entry) {
        	foreach ($menu_entry as $id => $menu_item) {
        		if (preg_match('/action=foo/', $menu_item)) {
        		    if ( $found_custom_menu ) {
        		        $found_custom_menu_twice = true;
        		    }
        		    $found_custom_menu = true;
        		}
        	}
        }
        
        $this->assertTrue($found_custom_menu, "Assert that custom menu was detected");
        $this->assertFalse($found_custom_menu_twice, "Assert that custom menu item wasn't listed twice");
    }    
    
    /**
     * @ticket 43497
     */
    public function testMenuExistsCanFindApplicationExtMenu()
	{
	    // Create module ext menu
	    $backupCustomMenu = false;
	    if ( !is_dir("custom/application/Ext/Menus/") )
	        sugar_mkdir("custom/application/Ext/Menus/",null,true);
        if (file_exists('custom/application/Ext/Menus/menu.ext.php')) {
	        copy('custom/application/Ext/Menus/menu.ext.php', 'custom/application/Ext/Menus/menu.ext.php.backup');
	        $backupCustomMenu = true;
	    }

        if ( $fh = @fopen("custom/application/Ext/Menus/menu.ext.php", 'w+') ) {
	        $string = <<<EOQ
<?php
\$module_menu[]=Array("index.php?module=Import&action=foobar&import_module=Accounts&return_module=Accounts&return_action=index","Foo","Foo", 'Accounts');
?>
EOQ;
            fputs( $fh, $string);
            fclose( $fh );
        }

        $view = new SugarView;
        $module_menu = $view->getMenu($this->_moduleName);
        $found_application_custom_menu = false;
        $found_application_custom_menu_twice = false;
        foreach ($module_menu as $key => $menu_entry) {
        	foreach ($menu_entry as $id => $menu_item) {
        		if (preg_match('/action=foobar/', $menu_item)) {
        		    if ( $found_application_custom_menu ) {
        		        $found_application_custom_menu_twice = true;
        		    }
        		    $found_application_custom_menu = true;
        		}
        	}
        }
        
        $this->assertTrue($found_application_custom_menu, "Assert that application custom menu was detected");
        $this->assertFalse($found_application_custom_menu_twice, "Assert that application custom menu item wasn't duplicated");
        
        if($backupCustomMenu) {
            copy('custom/application/Ext/Menus/menu.ext.php.backup', 'custom/application/Ext/Menus/menu.ext.php');
            unlink('custom/application/Ext/Menus/menu.ext.php.backup');
        }
        else
            unlink('custom/application/Ext/Menus/menu.ext.php');
	}

	/**
	 * @ticket 43497
	 */
	public function testMenuExistsCanFindModuleMenuAndModuleExtMenu()
	{
	    // Create module menu
        if( $fh = @fopen("modules/{$this->_moduleName}/Menu.php", 'w+') ) {
	        $string = <<<EOQ
<?php
\$module_menu[]=Array("index.php?module=Import&action=foo&import_module=Accounts&return_module=Accounts&return_action=index","Foo","Foo", 'Accounts');
?>
EOQ;
            fputs( $fh, $string);
            fclose( $fh );
        }

        // Create module ext menu
        sugar_mkdir("custom/modules/{$this->_moduleName}/Ext/Menus/",null,true);
        if( $fh = @fopen("custom/modules/{$this->_moduleName}/Ext/Menus/menu.ext.php", 'w+') ) {
	        $string = <<<EOQ
<?php
\$module_menu[]=Array("index.php?module=Import&action=bar&import_module=Accounts&return_module=Accounts&return_action=index","Foo","Foo", 'Accounts');
?>
EOQ;
            fputs( $fh, $string);
            fclose( $fh );
        }

        $view = new SugarView;
        $module_menu = $view->getMenu($this->_moduleName);
        $found_custom_menu = false;
        $found_custom_menu_twice = false;
        $found_menu = false;
        $found_menu_twice = false;
        foreach ($module_menu as $key => $menu_entry) {
        	foreach ($menu_entry as $id => $menu_item) {
        		if (preg_match('/action=foo/', $menu_item)) {
        		    if ( $found_menu ) {
        		        $found_menu_twice = true;
        		    }
        		    $found_menu = true;
        		}
        		if (preg_match('/action=bar/', $menu_item)) {
        		    if ( $found_custom_menu ) {
        		        $found_custom_menu_twice = true;
        		    }
        		    $found_custom_menu = true;
        		}
        	}
        }
        $this->assertTrue($found_menu, "Assert that menu was detected");
        $this->assertFalse($found_menu_twice, "Assert that menu item wasn't duplicated");
        $this->assertTrue($found_custom_menu, "Assert that custom menu was detected");
        $this->assertFalse($found_custom_menu_twice, "Assert that custom menu item wasn't duplicated");
	}
}

class ViewLoadMenuTest extends SugarView
{
    public function menuExists(
        $module
        )
    {
        return $this->_menuExists($module);
    }
}