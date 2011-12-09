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

 
require_once('include/MVC/View/views/view.classic.php');

class ViewClassicTest extends Sugar_PHPUnit_Framework_TestCase
{   
    public function testConstructor() 
	{
        $view = new ViewClassic();
        
        $this->assertEquals('',$view->type);
	}
	
	public function testDisplayWithClassicView()
	{
	    $view = $this->getMock('ViewClassic',array('includeClassicFile'));
	    
	    $view->module = 'testmodule'.mt_rand();
	    $view->action = 'testaction'.mt_rand();
	    
	    sugar_mkdir("modules/{$view->module}",null,true);
	    sugar_touch("modules/{$view->module}/{$view->action}.php");
	    
	    $return = $view->display();
	    
	    rmdir_recursive("modules/{$view->module}");
	    
	    $this->assertTrue($return);
	}
	
	public function testDisplayWithClassicCustomView()
	{
	    $view = $this->getMock('ViewClassic',array('includeClassicFile'));
	    
	    $view->module = 'testmodule'.mt_rand();
	    $view->action = 'testaction'.mt_rand();
	    
	    sugar_mkdir("custom/modules/{$view->module}",null,true);
	    sugar_touch("custom/modules/{$view->module}/{$view->action}.php");
	    
	    $return = $view->display();
	    
	    rmdir_recursive("custom/modules/{$view->module}");
	    
	    $this->assertTrue($return);
	}
	
	public function testDisplayWithNoClassicView()
	{
	    $view = $this->getMock('ViewClassic',array('includeClassicFile'));
	    
	    $view->module = 'testmodule'.mt_rand();
	    $view->action = 'testaction'.mt_rand();
	    
	    $this->assertFalse($view->display());
	}
}