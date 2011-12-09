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

require_once('modules/Calls/Call.php');
require_once('modules/Calls/CallHelper.php');

class CallHelperTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
    }
    
    public function tearDown()
    {
        unset($GLOBALS['app_list_strings']);
    }
    
    public function providerGetDurationMinutesOptions()
    {
        return array(
            array('EditView',<<<EOHTML
<select id="duration_minutes"onchange="SugarWidgetScheduler.update_time();"tabindex="1" name="duration_minutes">
<OPTION value='0'>00</OPTION>
<OPTION selected value='15'>15</OPTION>
<OPTION value='30'>30</OPTION>
<OPTION value='45'>45</OPTION></select>
EOHTML
                ),
            array('MassUpdate',<<<EOHTML
<select id="duration_minutes"tabindex="1" name="duration_minutes">
<OPTION value='0'>00</OPTION>
<OPTION selected value='15'>15</OPTION>
<OPTION value='30'>30</OPTION>
<OPTION value='45'>45</OPTION></select>
EOHTML
                ),
            array('QuickCreate',<<<EOHTML
<select id="duration_minutes"onchange="SugarWidgetScheduler.update_time();"tabindex="1" name="duration_minutes">
<OPTION value='0'>00</OPTION>
<OPTION selected value='15'>15</OPTION>
<OPTION value='30'>30</OPTION>
<OPTION value='45'>45</OPTION></select>
EOHTML
                ),

            array('DetailView','15'),
        );
    }
    
    /**
     * @dataProvider providerGetDurationMinutesOptions
     */
	public function testGetDurationMinutesOptions(
	    $view,
	    $returnValue
	    )
    {
        $focus = new Call();
        
        $this->assertEquals(
            getDurationMinutesOptions($focus,'','',$view),
            $returnValue
            );
    }
    
    public function testGetDurationMinutesOptionsNonDefaultValue()
    {
        $focus = new Call();
        $focus->duration_minutes = '30';
        
        $this->assertEquals(
            getDurationMinutesOptions($focus,'','','DetailView'),
            $focus->duration_minutes
            );
    }
    
    public function testGetDurationMinutesOptionsFromRequest()
    {
        $focus = new Call();
        $_REQUEST['duration_minutes'] = '45';
        
        $this->assertEquals(
            getDurationMinutesOptions($focus,'','','DetailView'),
            $_REQUEST['duration_minutes']
            );
        
        unset($_REQUEST['duration_minutes']);
    }
    
    public function testGetDurationMinutesOptionsOtherValues()
    {
        $focus = new Call();
        $focus->date_start = null;
        $focus->duration_hours = null;
        $focus->minutes_value_default = null;
        
        getDurationMinutesOptions($focus,'','','DetailView');
        
        $this->assertEquals($focus->date_start,$GLOBALS['timedate']->to_display_date(gmdate($GLOBALS['timedate']->get_date_time_format())));
        $this->assertEquals($focus->duration_hours,'0');
        $this->assertEquals($focus->duration_minutes,'1');
    }
    
    public function providerGetReminderTime()
    {
        return array(
            array('EditView',<<<EOHTML
<select id="reminder_time" name="reminder_time">
<OPTION value='60'>1 minute prior</OPTION>
<OPTION value='300'>5 minutes prior</OPTION>
<OPTION value='600'>10 minutes prior</OPTION>
<OPTION value='900'>15 minutes prior</OPTION>
<OPTION value='1800'>30 minutes prior</OPTION>
<OPTION value='3600'>1 hour prior</OPTION></select>
EOHTML
                ),
            array('MassUpdate',<<<EOHTML
<select id="reminder_time" name="reminder_time">
<OPTION value='60'>1 minute prior</OPTION>
<OPTION value='300'>5 minutes prior</OPTION>
<OPTION value='600'>10 minutes prior</OPTION>
<OPTION value='900'>15 minutes prior</OPTION>
<OPTION value='1800'>30 minutes prior</OPTION>
<OPTION value='3600'>1 hour prior</OPTION></select>
EOHTML
                ),
            array('SubpanelCreates',<<<EOHTML
<select id="reminder_time" name="reminder_time">
<OPTION value='60'>1 minute prior</OPTION>
<OPTION value='300'>5 minutes prior</OPTION>
<OPTION value='600'>10 minutes prior</OPTION>
<OPTION value='900'>15 minutes prior</OPTION>
<OPTION value='1800'>30 minutes prior</OPTION>
<OPTION value='3600'>1 hour prior</OPTION></select>
EOHTML
                ),
            array('QuickCreate',<<<EOHTML
<select id="reminder_time" name="reminder_time">
<OPTION value='60'>1 minute prior</OPTION>
<OPTION value='300'>5 minutes prior</OPTION>
<OPTION value='600'>10 minutes prior</OPTION>
<OPTION value='900'>15 minutes prior</OPTION>
<OPTION value='1800'>30 minutes prior</OPTION>
<OPTION value='3600'>1 hour prior</OPTION></select>
EOHTML
                ),

            array('DetailView',''),
        );
    }
    
    /**
     * @dataProvider providerGetReminderTime
     */
	public function testGetReminderTime(
	    $view,
	    $returnValue
	    )
    {
        $focus = new Call();
        
        $this->assertEquals(
            getReminderTime($focus,'','',$view),
            $returnValue
            );
    }
    
    public function testGetReminderTimeNonDefaultValue()
    {
        $focus = new Call();
        $focus->reminder_time = '600';
        
        $this->assertEquals(
            getReminderTime($focus,'','','EditView'),
            <<<EOHTML
<select id="reminder_time" name="reminder_time">
<OPTION value='60'>1 minute prior</OPTION>
<OPTION value='300'>5 minutes prior</OPTION>
<OPTION selected value='600'>10 minutes prior</OPTION>
<OPTION value='900'>15 minutes prior</OPTION>
<OPTION value='1800'>30 minutes prior</OPTION>
<OPTION value='3600'>1 hour prior</OPTION></select>
EOHTML
            );
    }
    
    public function testGetReminderTimeNonDefaultValueDetailView()
    {
        $focus = new Call();
        $focus->reminder_time = '300';
        
        $this->assertEquals(
            getReminderTime($focus,'','','DetailView'),
            '5 minutes prior'
            );
    }
    
    public function testGetReminderTimeFromRequest()
    {
        $focus = new Call();
        $_REQUEST['reminder_time'] = '900';
        $_REQUEST['full_form'] = true;
        
        $this->assertEquals(
            getReminderTime($focus,'','','EditView'),
            <<<EOHTML
<select id="reminder_time" name="reminder_time">
<OPTION value='60'>1 minute prior</OPTION>
<OPTION value='300'>5 minutes prior</OPTION>
<OPTION value='600'>10 minutes prior</OPTION>
<OPTION selected value='900'>15 minutes prior</OPTION>
<OPTION value='1800'>30 minutes prior</OPTION>
<OPTION value='3600'>1 hour prior</OPTION></select>
EOHTML
            );
        
        unset($_REQUEST['reminder_time']);
        unset($_REQUEST['full_form']);
    }
    
    public function testGetReminderTimeFromValue()
    {
        $focus = new Call();
        unset($focus->reminder_time);
        
        $this->assertEquals(
            getReminderTime($focus,'','1800','EditView'),
            <<<EOHTML
<select id="reminder_time" name="reminder_time">
<OPTION value='60'>1 minute prior</OPTION>
<OPTION value='300'>5 minutes prior</OPTION>
<OPTION value='600'>10 minutes prior</OPTION>
<OPTION value='900'>15 minutes prior</OPTION>
<OPTION selected value='1800'>30 minutes prior</OPTION>
<OPTION value='3600'>1 hour prior</OPTION></select>
EOHTML
            );
    }
}
