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

 
require_once 'include/MVC/View/SugarView.php';

class SugarViewTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_view = new SugarViewTestMock();
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
        $GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], 'Users');
    }
    
    public function tearDown()
    {
    	unset($GLOBALS['mod_strings']);
    	unset($GLOBALS['app_strings']);
    }
    
    public function testGetModuleTab()
    {
        $_REQUEST['module_tab'] = 'ADMIN';
        $moduleTab = $this->_view->getModuleTab();
        $this->assertEquals('ADMIN', $moduleTab, 'Module Tab names are not equal from request');
    }

    public function testGetMetaDataFile()
    {
        $this->_view->module = 'Contacts';
        $this->_view->type = 'list';
        $metaDataFile = $this->_view->getMetaDataFile();
        $this->assertEquals('modules/Contacts/metadata/listviewdefs.php', $metaDataFile, 'Did not load the correct metadata file');

        //test custom file
        sugar_mkdir('custom/modules/Contacts/metadata/', null, true);
        $customFile = 'custom/modules/Contacts/metadata/listviewdefs.php';
        if(!file_exists($customFile))
        {
            sugar_file_put_contents($customFile, array());
            $customMetaDataFile = $this->_view->getMetaDataFile();
            $this->assertEquals($customFile, $customMetaDataFile, 'Did not load the correct custom metadata file');
            unlink($customFile);
        }
    }
    
    public function testInit()
    {
        $bean = new SugarBean;
        $view_object_map = array('foo'=>'bar');
        $GLOBALS['action'] = 'barbar';
        $GLOBALS['module'] = 'foofoo';
        
        $this->_view->init($bean,$view_object_map);
        
        $this->assertInstanceOf('SugarBean',$this->_view->bean);
        $this->assertEquals($view_object_map,$this->_view->view_object_map);
        $this->assertEquals($GLOBALS['action'],$this->_view->action);
        $this->assertEquals($GLOBALS['module'],$this->_view->module);
        $this->assertInstanceOf('Sugar_Smarty',$this->_view->ss);
    }
    
    public function testInitNoParameters()
    {
        $GLOBALS['action'] = 'barbar';
        $GLOBALS['module'] = 'foofoo';
        
        $this->_view->init();
        
        $this->assertNull($this->_view->bean);
        $this->assertEquals(array(),$this->_view->view_object_map);
        $this->assertEquals($GLOBALS['action'],$this->_view->action);
        $this->assertEquals($GLOBALS['module'],$this->_view->module);
        $this->assertInstanceOf('Sugar_Smarty',$this->_view->ss);
    }
    
    public function testInitSmarty()
    {
        $this->_view->initSmarty();
        
        $this->assertInstanceOf('Sugar_Smarty',$this->_view->ss);
        $this->assertEquals($this->_view->ss->get_template_vars('MOD'),$GLOBALS['mod_strings']);
        $this->assertEquals($this->_view->ss->get_template_vars('APP'),$GLOBALS['app_strings']);
    }
    
    /**
     * @outputBuffering enabled
     */
    public function testDisplayErrors()
    {
        $this->_view->errors = array('error1','error2');
        $this->_view->suppressDisplayErrors = true;
        
        $this->assertEquals(
            '<span class="error">error1</span><br><span class="error">error2</span><br>',
            $this->_view->displayErrors()
            );
    }
    
    /**
     * @outputBuffering enabled
     */
    public function testDisplayErrorsDoNotSupressOutput()
    {
        $this->_view->errors = array('error1','error2');
        $this->_view->suppressDisplayErrors = false;
        
        $this->assertEmpty($this->_view->displayErrors());
    }
    
    public function testGetBrowserTitle()
    {
        $viewMock = $this->getMock('SugarViewTestMock',array('_getModuleTitleParams'));
        $viewMock->expects($this->any())
                 ->method('_getModuleTitleParams')
                 ->will($this->returnValue(array('foo','bar')));
        
        $this->assertEquals(
            "bar &raquo; foo &raquo; {$GLOBALS['app_strings']['LBL_BROWSER_TITLE']}",
            $viewMock->getBrowserTitle()
            );
    }
    
    public function testGetBrowserTitleUserLogin()
    {
        $this->_view->module = 'Users';
        $this->_view->action = 'Login';
        
        $this->assertEquals(
            "{$GLOBALS['app_strings']['LBL_BROWSER_TITLE']}",
            $this->_view->getBrowserTitle()
            );
    }
    
    public function testGetBreadCrumbSymbolForLTRTheme()
    {
        $theme = SugarTestThemeUtilities::createAnonymousTheme();
        SugarThemeRegistry::set($theme);
        
        $this->assertEquals(
            "<span class='pointer'>&raquo;</span>",
            $this->_view->getBreadCrumbSymbol()
            );
    }
    
    public function testGetBreadCrumbSymbolForRTLTheme()
    {
        $theme = SugarTestThemeUtilities::createAnonymousRTLTheme();
        SugarThemeRegistry::set($theme);
        
        $this->assertEquals(
            "<span class='pointer'>&laquo;</span>",
            $this->_view->getBreadCrumbSymbol()
            );
    }
}

class SugarViewTestMock extends SugarView
{
    public function getModuleTab()
    {
        return parent::_getModuleTab();
    }
    
    public function initSmarty()
    {
        return parent::_initSmarty();
    }
}
