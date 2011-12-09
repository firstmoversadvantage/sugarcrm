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

require_once 'include/Dashlets/Dashlet.php';

/**
 * @ticket 33948
 */
class DashletAutoRefreshTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setup()
    {
        if ( isset($GLOBALS['sugar_config']['dashlet_auto_refresh_min']) ) {
            $this->backup_dashlet_auto_refresh_min = $GLOBALS['sugar_config']['dashlet_auto_refresh_min'];
        }
        unset($GLOBALS['sugar_config']['dashlet_auto_refresh_min']);
    }
    
    public function tearDown()
    {
        if ( isset($this->backup_dashlet_auto_refresh_min) ) {
            $GLOBALS['sugar_config']['dashlet_auto_refresh_min'] = $this->backup_dashlet_auto_refresh_min;
        }
    }
    
    public function testIsAutoRefreshableIfRefreshable() 
    {
        $dashlet = new DashletAutoRefreshTestMock('unit_test_run');
        $dashlet->isRefreshable = true;
        
        $this->assertTrue($dashlet->isAutoRefreshable());
    }
    
    public function testIsNotAutoRefreshableIfNotRefreshable() 
    {
        $dashlet = new DashletAutoRefreshTestMock('unit_test_run');
        $dashlet->isRefreshable = false;
        
        $this->assertFalse($dashlet->isAutoRefreshable());
    }
  
    public function testReturnCorrectAutoRefreshOptionsWhenMinIsSet() 
    {
        $langpack = new SugarTestLangPackCreator();
        $langpack->setAppListString('dashlet_auto_refresh_options',
            array(
                '-1' 	=> 'Never',
                '30' 	=> 'Every 30 seconds',
                '60' 	=> 'Every 1 minute',
                '180' 	=> 'Every 3 minutes',
                '300' 	=> 'Every 5 minutes',
                '600' 	=> 'Every 10 minutes',
                )
            );
        $langpack->save();
    
        $GLOBALS['sugar_config']['dashlet_auto_refresh_min'] = 60;
        
        $dashlet = new DashletAutoRefreshTestMock('unit_test_run');
        $options = $dashlet->getAutoRefreshOptions();
        $this->assertEquals(
            array(
                '-1' 	=> 'Never',
                '60' 	=> 'Every 1 minute',
                '180' 	=> 'Every 3 minutes',
                '300' 	=> 'Every 5 minutes',
                '600' 	=> 'Every 10 minutes',
                ),
            $options
            );
        
        unset($langpack);
    }
    
    public function testReturnCorrectAutoRefreshOptionsWhenMinIsNotSet() 
    {
        $langpack = new SugarTestLangPackCreator();
        $langpack->setAppListString('dashlet_auto_refresh_options',
            array(
                '-1' 	=> 'Never',
                '30' 	=> 'Every 30 seconds',
                '60' 	=> 'Every 1 minute',
                '180' 	=> 'Every 3 minutes',
                '300' 	=> 'Every 5 minutes',
                '600' 	=> 'Every 10 minutes',
                )
            );
        $langpack->save();
    
        $dashlet = new DashletAutoRefreshTestMock('unit_test_run');
        $options = $dashlet->getAutoRefreshOptions();
        $this->assertEquals(
            array(
                '-1' 	=> 'Never',
                '30' 	=> 'Every 30 seconds',
                '60' 	=> 'Every 1 minute',
                '180' 	=> 'Every 3 minutes',
                '300' 	=> 'Every 5 minutes',
                '600' 	=> 'Every 10 minutes',
                ),
            $options
            );
        
        unset($langpack);
    }
    
    public function testProcessAutoRefreshReturnsAutoRefreshTemplateNormally()
    {
        $dashlet = new DashletAutoRefreshTestMock('unit_test_run');
        $dashlet->isRefreshable = true;
        $_REQUEST['module'] = 'unit_test';
        $_REQUEST['action'] = 'unit_test';
        $dashlet->seedBean = new stdClass;
        $dashlet->seedBean->object_name = 'unit_test';
        
        $this->assertNotEmpty($dashlet->processAutoRefresh());
    }
    
    public function testProcessAutoRefreshReturnsNothingIfDashletIsNotRefreshable()
    {
        $dashlet = new DashletAutoRefreshTestMock('unit_test_run');
        $dashlet->isRefreshable = false;
        $_REQUEST['module'] = 'unit_test';
        $_REQUEST['action'] = 'unit_test';
        $dashlet->seedBean = new stdClass;
        $dashlet->seedBean->object_name = 'unit_test';
        
        $this->assertEmpty($dashlet->processAutoRefresh());
    }
    
    public function testProcessAutoRefreshReturnsNothingIfAutoRefreshingIsDisabled()
    {
        $dashlet = new DashletAutoRefreshTestMock('unit_test_run');
        $GLOBALS['sugar_config']['dashlet_auto_refresh_min'] = -1;
        $_REQUEST['module'] = 'unit_test';
        $_REQUEST['action'] = 'unit_test';
        $dashlet->seedBean = new stdClass;
        $dashlet->seedBean->object_name = 'unit_test';
        
        $this->assertEmpty($dashlet->processAutoRefresh());
    }
}

class DashletAutoRefreshTestMock extends Dashlet
{
    public function isAutoRefreshable() 
    {
        return parent::isAutoRefreshable();
    }
    
    public function getAutoRefreshOptions() 
    {
        return parent::getAutoRefreshOptions();
    }
    
    public function processAutoRefresh() 
    {
        return parent::processAutoRefresh();
    }
}
