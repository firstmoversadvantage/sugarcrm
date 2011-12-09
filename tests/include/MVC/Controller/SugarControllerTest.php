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

 
require_once 'include/MVC/Controller/SugarController.php';

class SugarControllerTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
    }
    
    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }

    public function testSetup()
    {
        $controller = new SugarControllerMock;
        $controller->setup();
        
        $this->assertEquals('Home',$controller->module);
        $this->assertNull($controller->target_module);
    }
    
    public function testSetupSpecifyModule()
    {
        $controller = new SugarControllerMock;
        $controller->setup('foo');
        
        $this->assertEquals('foo',$controller->module);
        $this->assertNull($controller->target_module);
    }
    
    public function testSetupUseRequestVars()
    {
        $_REQUEST = array(
            'module' => 'dog33434',
            'target_module' => 'dog121255',
            'action' => 'dog3232',
            'record' => 'dog5656',
            'view' => 'dog4343',
            'return_module' => 'dog1312',
            'return_action' => 'dog1212',
            'return_id' => '11212',
            );
        $controller = new SugarControllerMock;
        $controller->setup();
        
        $this->assertEquals($_REQUEST['module'],$controller->module);
        $this->assertEquals($_REQUEST['target_module'],$controller->target_module);
        $this->assertEquals($_REQUEST['action'],$controller->action);
        $this->assertEquals($_REQUEST['record'],$controller->record);
        $this->assertEquals($_REQUEST['view'],$controller->view);
        $this->assertEquals($_REQUEST['return_module'],$controller->return_module);
        $this->assertEquals($_REQUEST['return_action'],$controller->return_action);
        $this->assertEquals($_REQUEST['return_id'],$controller->return_id);
    }
    
    public function testSetModule()
    {
        $controller = new SugarControllerMock;
        $controller->setModule('cat');
        
        $this->assertEquals('cat',$controller->module);
    }
    
    public function testLoadBean()
    {
        
    }
    
    public function testCallLegacyCodeIfLegacyDetailViewFound()
    {
        $module_name = 'TestModule'.mt_rand();
        sugar_mkdir("modules/$module_name/",null,true);
        sugar_touch("modules/$module_name/DetailView.php");
        
        $controller = new SugarControllerMock;
        $controller->setup($module_name);
        $controller->do_action = 'DetailView';
        $controller->view = 'list';
        $controller->callLegacyCode();
        
        $this->assertEquals('classic',$controller->view);
        
        rmdir_recursive("modules/$module_name");
    }

    public function testCallLegacyCodeIfNewDetailViewFound()
    {
        $module_name = 'TestModule'.mt_rand();
        sugar_mkdir("modules/$module_name/views",null,true);
        sugar_touch("modules/$module_name/views/view.detail.php");
        
        $controller = new SugarControllerMock;
        $controller->setup($module_name);
        $controller->do_action = 'DetailView';

        $controller->view = 'list';
        $controller->callLegacyCode();
        
        $this->assertEquals('list',$controller->view);
        
        rmdir_recursive("modules/$module_name");
    }


    public function testCallLegacyCodeIfLegacyDetailViewAndNewDetailViewFound()
    {
        $module_name = 'TestModule'.mt_rand();
        sugar_mkdir("modules/$module_name/views",null,true);
        sugar_touch("modules/$module_name/views/view.detail.php");
        sugar_touch("modules/$module_name/DetailView.php");
        
        $controller = new SugarControllerMock;
        $controller->setup($module_name);
        $controller->do_action = 'DetailView';

        $controller->view = 'list';
        $controller->callLegacyCode();
        
        $this->assertEquals('list',$controller->view);
        
        rmdir_recursive("modules/$module_name");
    }

    public function testCallLegacyCodeIfCustomLegacyDetailViewAndNewDetailViewFound()
    {
        $module_name = 'TestModule'.mt_rand();
        sugar_mkdir("modules/$module_name/views",null,true);
        sugar_touch("modules/$module_name/views/view.detail.php");
        sugar_mkdir("custom/modules/$module_name",null,true);
        sugar_touch("custom/modules/$module_name/DetailView.php");
        
        $controller = new SugarControllerMock;
        $controller->setup($module_name);
        $controller->do_action = 'DetailView';

        $controller->view = 'list';
        $controller->callLegacyCode();
        
        $this->assertEquals('classic',$controller->view);
        
        rmdir_recursive("modules/$module_name");
    }

    public function testCallLegacyCodeIfLegacyDetailViewAndCustomNewDetailViewFound()
    {
        $module_name = 'TestModule'.mt_rand();
        sugar_mkdir("custom/modules/$module_name/views",null,true);
        sugar_touch("custom/modules/$module_name/views/view.detail.php");
        sugar_mkdir("modules/$module_name",null,true);
        sugar_touch("modules/$module_name/DetailView.php");
        
        $controller = new SugarControllerMock;
        $controller->setup($module_name);
        $controller->do_action = 'DetailView';
        $controller->view = 'list';
        $controller->callLegacyCode();
        
        $this->assertEquals('classic',$controller->view);
        
        rmdir_recursive("modules/$module_name");
    }

    public function testCallLegacyCodeIfLegacyDetailViewAndNewDetailViewFoundAndCustomLegacyDetailViewFound()
    {
        $module_name = 'TestModule'.mt_rand();
        sugar_mkdir("modules/$module_name/views",null,true);
        sugar_touch("modules/$module_name/views/view.detail.php");
        sugar_touch("modules/$module_name/DetailView.php");
        sugar_mkdir("custom/modules/$module_name",null,true);
        sugar_touch("custom/modules/$module_name/DetailView.php");
        
        $controller = new SugarControllerMock;
        $controller->setup($module_name);
        $controller->do_action = 'DetailView';

        $controller->view = 'list';
        $controller->callLegacyCode();
        
        $this->assertEquals('classic',$controller->view);
        
        rmdir_recursive("modules/$module_name");
    }

    public function testCallLegacyCodeIfLegacyDetailViewAndNewDetailViewFoundAndCustomNewDetailViewFound()
    {
        $module_name = 'TestModule'.mt_rand();
        sugar_mkdir("custom/modules/$module_name/views",null,true);
        sugar_touch("custom/modules/$module_name/views/view.detail.php");
        sugar_mkdir("modules/$module_name/views",null,true);
        sugar_touch("modules/$module_name/views/view.detail.php");
        sugar_touch("modules/$module_name/DetailView.php");
        
        $controller = new SugarControllerMock;
        $controller->setup($module_name);
        $controller->do_action = 'DetailView';
        $controller->view = 'list';
        $controller->callLegacyCode();
        
        $this->assertEquals('list',$controller->view);
        
        rmdir_recursive("modules/$module_name");
    }

    public function testCallLegacyCodeIfLegacyDetailViewAndNewDetailViewFoundAndCustomLegacyDetailViewFoundAndCustomNewDetailViewFound()
    {
        $module_name = 'TestModule'.mt_rand();
        sugar_mkdir("custom/modules/$module_name/views",null,true);
        sugar_touch("custom/modules/$module_name/views/view.detail.php");
        sugar_touch("custom/modules/$module_name/DetailView.php");
        sugar_mkdir("modules/$module_name/views",null,true);
        sugar_touch("modules/$module_name/views/view.detail.php");
        sugar_touch("modules/$module_name/DetailView.php");
        
        $controller = new SugarControllerMock;
        $controller->setup($module_name);
        $controller->do_action = 'DetailView';

        $controller->view = 'list';
        $controller->callLegacyCode();
        
        $this->assertEquals('list',$controller->view);
        
        rmdir_recursive("modules/$module_name");
    }
    
    public function testPostDelete()
    {
        $_REQUEST['return_module'] = 'foo';
        $_REQUEST['return_action'] = 'bar';
        $_REQUEST['return_id'] = '123';
        
        $controller = new SugarControllerMock;
        $controller->post_delete();
        
        unset($_REQUEST['return_module']);
        unset($_REQUEST['return_action']);
        unset($_REQUEST['return_id']);
        
        $this->assertEquals("index.php?module=foo&action=bar&record=123",$controller->redirect_url);
    }
    
    /**
     * @ticket 23816
     */
    public function testPostDeleteWithOffset()
    {
        $_REQUEST['return_module'] = 'foo';
        $_REQUEST['return_action'] = 'bar';
        $_REQUEST['return_id'] = '123';
        $_REQUEST['offset'] = '2';
        
        $controller = new SugarControllerMock;
        $controller->post_delete();
        
        unset($_REQUEST['return_module']);
        unset($_REQUEST['return_action']);
        unset($_REQUEST['return_id']);
        unset($_REQUEST['offset']);
        
        $this->assertEquals("index.php?module=foo&action=bar&record=123&offset=2",$controller->redirect_url);
    }
    
    /**
     * @ticket 23816
     */
    public function testPostDeleteWithOffsetAndDuplicateSave()
    {
        $_REQUEST['return_module'] = 'foo';
        $_REQUEST['return_action'] = 'bar';
        $_REQUEST['return_id'] = '123';
        $_REQUEST['offset'] = '2';
        $_REQUEST['duplicateSave'] = true;
        
        $controller = new SugarControllerMock;
        $controller->post_delete();
        
        unset($_REQUEST['return_module']);
        unset($_REQUEST['return_action']);
        unset($_REQUEST['return_id']);
        unset($_REQUEST['offset']);
        unset($_REQUEST['duplicateSave']);
        
        $this->assertEquals("index.php?module=foo&action=bar&record=123",$controller->redirect_url);
    }
    
    public function testPostDeleteWithDefaultValues()
    {
        $backupDefaultModule = $GLOBALS['sugar_config']['default_module'];
        $backupDefaultAction = $GLOBALS['sugar_config']['default_action'];
        
        $GLOBALS['sugar_config']['default_module'] = 'yuck';
        $GLOBALS['sugar_config']['default_action'] = 'yuckyuck';
        
        $controller = new SugarControllerMock;
        $controller->post_delete();
        
        $GLOBALS['sugar_config']['default_module'] = $backupDefaultModule;
        $GLOBALS['sugar_config']['default_action'] = $backupDefaultAction;
        
        $this->assertEquals("index.php?module=yuck&action=yuckyuck&record=",$controller->redirect_url);
    }
}

class SugarControllerMock extends SugarController
{
    public $do_action;
    
    public function callLegacyCode()
    {
        return parent::callLegacyCode();
    }
    
    public function post_delete()
    {
        parent::post_delete();
    }
}
