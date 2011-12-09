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

 
class Bug33284_Test extends Sugar_PHPUnit_Framework_TestCase
{
    var $max_display_set = false;
    var $max_display_length;
    
    public function setUp() {
    	if(isset($sugar_config['tracker_max_display_length'])) {
    	   $this->max_display_set = true;
    	   $this->max_display_length = $sugar_config['tracker_max_display_length'];
    	}
    }
    
    public function tearDown() {
        if($this->max_display_set) {
           global $sugar_config; 
           $sugar_config['tracker_max_display_length'] = $this->max_display_length;
        }
    }

    public function test_get_tracker_substring1()
    {
        global $sugar_config;       
        
        $default_length = 15;
    	
        $sugar_config['tracker_max_display_length'] = $default_length;
        
        $test_string = 'The quick brown fox jumps over lazy dogs';
        $display_string = getTrackerSubstring($test_string);
        $this->assertEquals(strlen($display_string), $default_length, 'Assert that the string length is equal to ' . $default_length . ' characters');
    }
    
    
    public function test_get_tracker_substring2()
    {
    	global $sugar_config;       
        $test_string = '"Hello There How Are You? " This has quotes too';
        
        $default_length = 15;
 
        $sugar_config['tracker_max_display_length'] = $default_length;
        
        $display_string = getTrackerSubstring($test_string);  
        $this->assertEquals(strlen($display_string), $default_length, 'Assert that the string length is equal to ' . $default_length . ' characters (default)');

		$test_string = '早前於美國完成民族音樂學博士學位回港後在大專院校的音樂系任教123456789';
        $display_string = getTrackerSubstring($test_string);

        $this->assertEquals(mb_strlen($display_string, 'UTF-8'), $default_length, 'Assert that the string length is equal to ' . $default_length . ' characters (default)');    
    }  
}

?>