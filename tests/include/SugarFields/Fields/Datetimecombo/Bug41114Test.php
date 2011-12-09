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

 
require_once('include/SugarFields/Fields/Relate/SugarFieldRelate.php');

class Bug41114Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $user;
    
	public function setUp()
    {
        $this->user = SugarTestUserUtilities::createAnonymousUser();
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($this->user);
    }
    
    public function _providerEmailTemplateFormat()
    {
        return array(
            array('2010-10-10 13:00:00','2010/10/10 01:00PM', 'Y/m/d', 'h:iA' ),
            array('2010-10-11 13:00:00','2010/10/11 13:00', 'Y/m/d', 'H:i' ),
            
            array('2011-03-25 01:05:22','25.03.2011 01:05AM', 'd.m.Y', 'h:iA'),
            array('2011-04-21 01:05:22','21.04.2011 01:05', 'd.m.Y', 'H:i'),
            
            array('','', 'Y-m-d', 'h:iA'),
            array('','', 'Y-m-d', 'H:i'),
            
        );   
    }
     /**
     * @dataProvider _providerEmailTemplateFormat
     */
	public function testEmailTemplateFormat($unformattedValue, $expectedValue, $dateFormat, $timeFormat)
	{
	    $GLOBALS['sugar_config']['default_date_format'] = $dateFormat;
		$GLOBALS['sugar_config']['default_time_format'] = $timeFormat;
		$this->user->setPreference('datef', $dateFormat);
		$this->user->setPreference('timef', $timeFormat);
		
        require_once('include/SugarFields/SugarFieldHandler.php');
   		$sfr = SugarFieldHandler::getSugarField('datetimecombo');
    	$formattedValue = $sfr->getEmailTemplateValue($unformattedValue,array(), array('notify_user' => $this->user));
    	
   	 	$this->assertEquals($expectedValue, $formattedValue);
    }
}