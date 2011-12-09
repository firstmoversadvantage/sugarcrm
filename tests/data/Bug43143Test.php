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

 
class Bug43143Test extends Sugar_PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
	}

	public static function tearDownAfterClass()
	{
	    SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
	}

	public function setUp()
	{
	    $this->bean = new Opportunity();
	    $this->defs = $this->bean->field_defs;
	    $this->timedate = $GLOBALS['timedate'];
	}

	public function tearDown()
	{
	    $this->bean->field_defs = $this->defs;
        $GLOBALS['timedate']->clearCache();
	}

	public function defaultDates()
	{
	    return array(
	        array('-1 day', '2010-12-31'),
	        array('now', '2011-01-01'),
	        array('+1 day', '2011-01-02'),
	        array('+1 week', '2011-01-08'),
	        array('next monday', '2011-01-03'),
	        array('next friday', '2011-01-07'),
	        array('+2 weeks', '2011-01-15'),
	        array('+1 month', '2011-02-01'),
	        array('first day of next month', '2011-02-01'),
	        array('+3 months', '2011-04-01'),
	        array('+6 months', '2011-07-01'),
	        array('+1 year', '2012-01-01'),
	        );
	}

	/**
	 * @dataProvider defaultDates
	 * @param string $default
	 * @param string $value
	 */
	public function testDefaults($default, $value)
	{
        $this->timedate->allow_cache = true;
        $this->timedate->setNow($this->timedate->fromDb('2011-01-01 00:00:00'));
	    $this->bean->field_defs['date_closed']['display_default'] = $default;
	    $this->bean->populateDefaultValues(true);
	    $this->assertEquals($value, $this->timedate->to_db_date($this->bean->date_closed));
	}

    /*
     * @group bug43143
     */
    public function testUnpopulateData()
    {
        $this->bean->field_defs['date_closed']['display_default'] = 'next friday';
	    $this->bean->populateDefaultValues(true);
        $this->assertNotNull($this->bean->date_closed);
        $this->bean->unPopulateDefaultValues();
        $this->assertNull($this->bean->name);
        $this->assertNull($this->bean->date_closed);
    }
}
