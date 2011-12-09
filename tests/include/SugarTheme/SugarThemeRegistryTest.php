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

 
require_once 'include/SugarTheme/SugarTheme.php';
require_once 'include/dir_inc.php';

class SugarThemeRegistryTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_themeName;
    private $_oldDefaultTheme;
    
    public function setup()
    {
        $this->_themeName = SugarTestThemeUtilities::createAnonymousTheme();
        if ( isset($GLOBALS['sugar_config']['default_theme']) ) {
            $this->_oldDefaultTheme = $GLOBALS['sugar_config']['default_theme'];
        }
        $GLOBALS['sugar_config']['default_theme'] = $this->_themeName;
        SugarThemeRegistry::buildRegistry();
    }
    
    public function tearDown()
    {
        SugarTestThemeUtilities::removeAllCreatedAnonymousThemes();
        if ( isset($this->_oldDefaultTheme) ) {
            $GLOBALS['sugar_config']['default_theme'] = $this->_oldDefaultTheme;
        }
    }
    
    public function testThemesRegistered()
    {
        $this->assertTrue(SugarThemeRegistry::exists($this->_themeName));
    }
    
    public function testGetThemeObject()
    {
        $object = SugarThemeRegistry::get($this->_themeName);
        
        $this->assertInstanceOf('SugarTheme',$object);
        $this->assertEquals($object->__toString(),$this->_themeName);
    }
    
    /**
     * @ticket 41635
     */
    public function testGetDefaultThemeObject()
    {
        $GLOBALS['sugar_config']['default_theme'] = $this->_themeName;
        
        $object = SugarThemeRegistry::getDefault();
        
        $this->assertInstanceOf('SugarTheme',$object);
        $this->assertEquals($object->__toString(),$this->_themeName);
    }
    
    /**
     * @ticket 41635
     */
    public function testGetDefaultThemeObjectWhenDefaultThemeIsNotSet()
    {
        unset($GLOBALS['sugar_config']['default_theme']);
        
        $themename = array_pop(array_keys(SugarThemeRegistry::availableThemes()));
        
        $object = SugarThemeRegistry::getDefault();
        
        $this->assertInstanceOf('SugarTheme',$object);
        $this->assertEquals($object->__toString(),$themename);
    }
    
    public function testSetCurrentTheme()
    {
        SugarThemeRegistry::set($this->_themeName);
        
        $this->assertInstanceOf('SugarTheme',SugarThemeRegistry::current());
        $this->assertEquals(SugarThemeRegistry::current()->__toString(),$this->_themeName);
    }
    
    public function testInListOfAvailableThemes()
    {
        if ( isset($GLOBALS['sugar_config']['disabled_themes']) ) {
            $disabled_themes = $GLOBALS['sugar_config']['disabled_themes'];
            unset($GLOBALS['sugar_config']['disabled_themes']);
        }
        
        $themes = SugarThemeRegistry::availableThemes();
        $this->assertTrue(isset($themes[$this->_themeName]));
        $themes = SugarThemeRegistry::unAvailableThemes();
        $this->assertTrue(!isset($themes[$this->_themeName]));
        $themes = SugarThemeRegistry::allThemes();
        $this->assertTrue(isset($themes[$this->_themeName]));
        
        if ( isset($disabled_themes) )
            $GLOBALS['sugar_config']['disabled_themes'] = $disabled_themes;
    }
    
    public function testDisabledThemeNotInListOfAvailableThemes()
    {
        if ( isset($GLOBALS['sugar_config']['disabled_themes']) ) {
            $disabled_themes = $GLOBALS['sugar_config']['disabled_themes'];
            unset($GLOBALS['sugar_config']['disabled_themes']);
        }
        
        $GLOBALS['sugar_config']['disabled_themes'] = $this->_themeName;
        
        $themes = SugarThemeRegistry::availableThemes();
        $this->assertTrue(!isset($themes[$this->_themeName]));
        $themes = SugarThemeRegistry::unAvailableThemes();
        $this->assertTrue(isset($themes[$this->_themeName]));
        $themes = SugarThemeRegistry::allThemes();
        $this->assertTrue(isset($themes[$this->_themeName]));
        
        if ( isset($disabled_themes) )
            $GLOBALS['sugar_config']['disabled_themes'] = $disabled_themes;
    }
    
    public function testCustomThemeLoaded()
    {
        $customTheme = SugarTestThemeUtilities::createAnonymousCustomTheme($this->_themeName);
        
        SugarThemeRegistry::buildRegistry();
        
        $this->assertEquals(
            SugarThemeRegistry::get($customTheme)->name,
            'custom ' . $customTheme
            );
    }
    
    public function testDefaultThemedefFileHandled()
    {
        create_custom_directory('themes/default/');
        sugar_file_put_contents('custom/themes/default/themedef.php','<?php $themedef = array("group_tabs" => false);');
        
        SugarThemeRegistry::buildRegistry();
        
        $this->assertEquals(
            SugarThemeRegistry::get($this->_themeName)->group_tabs,
            false
            );
        
        unlink('custom/themes/default/themedef.php');
    }
    
    public function testClearCacheAllThemes()
    {
        SugarThemeRegistry::get($this->_themeName)->getCSSURL('style.css');
        $this->assertTrue(isset(SugarThemeRegistry::get($this->_themeName)->_cssCache['style.css']),
                            'File style.css should exist in cache');
        
        SugarThemeRegistry::clearAllCaches();
        SugarThemeRegistry::buildRegistry();
        
        $this->assertFalse(isset(SugarThemeRegistry::get($this->_themeName)->_cssCache['style.css']),
                            'File style.css shouldn\'t exist in cache');
    }
    
    /**
     * @ticket 35307
     */
    public function testOldThemeIsNotRecognized()
    {
        $themename = SugarTestThemeUtilities::createAnonymousOldTheme();
        
        $this->assertNull(SugarThemeRegistry::get($themename));
    }
}
