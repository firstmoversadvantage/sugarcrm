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


require_once('data/SugarBean.php');
require_once('modules/Accounts/Account.php');

class FixUpFormattingTest extends Sugar_PHPUnit_Framework_TestCase
{
    protected $myBean;

	public function setUp()
	{
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        
        $this->myBean = new SugarBean();
        
        $this->myBean->field_defs = array( 
            'id' => array('name' => 'id', 'vname' => 'LBL_ID', 'type' => 'id', 'required' => true, ),
            'name' => array('name' => 'name', 'vname' => 'LBL_NAME', 'type' => 'varchar', 'len' => '255', 'required' => true, ),
            'bool_field' => array('name' => 'bool_field', 'vname' => 'LBL_BOOL_FIELD', 'type' => 'bool', ),
            'int_field' => array('name' => 'int_field', 'vname' => 'LBL_INT_FIELD', 'type' => 'int', ),
            'float_field' => array('name' => 'float_field', 'vname' => 'LBL_FLOAT_FIELD', 'type' => 'float', 'precision' => 2, ),
            'date_field' => array('name' => 'date_field', 'vname' => 'LBL_DATE_FIELD', 'type' => 'date', ),
            'time_field' => array('name' => 'time_field', 'vname' => 'LBL_TIME_FIELD', 'type' => 'time', ),
            'datetime_field' => array('name' => 'datetime_field', 'vname' => 'LBL_DATETIME_FIELD', 'type' => 'datetime', ),
        );

        $this->myBean->id = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        $this->myBean->name = 'Fake Bean';
        $this->myBean->bool_field = 1;
        $this->myBean->int_field = 2001;
        $this->myBean->float_field = 20.01;
        $this->myBean->date_field = '2001-07-28';
        $this->myBean->time_field = '21:19:37';
        $this->myBean->datetime_field = '2001-07-28 21:19:37';

	}

	public function tearDown()
	{
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        unset($this->time_date);
	}

	public function providerBoolFixups()
	{
	    return array(
            array(true,true),
            array(false,false),
            array('',false),
            array(1,true),
            array(0,false),
            array('1',true),
            array('0',false),
            array('true',true),
            array('false',false),
            array('on',true),
            array('off',false),
            array('yes',true),
            array('no',false),
	        );
	}

	/**
     * @ticket 34562
     * @dataProvider providerBoolFixups
     */
	public function testBoolFixups($from, $to)
	{
        $this->myBean->bool_field = $from;
        $this->myBean->fixUpFormatting();
        $this->assertEquals($to,$this->myBean->bool_field,'fixUpFormatting did not adjust from ('.gettype($from).') "'.$from.'"');
    }

    /**
     * @group bug43321
     */
	public function testStringNULLFixups()
	{
        $bean = new SugarBean();

        $bean->field_defs = array('date_field'=>array('type'=>'date'),
                                 'datetime_field'=>array('type'=>'datetime'),
                                 'time_field'=>array('type'=>'time'),
                                 'datetimecombo_field'=>array('type'=>'datetimecombo')
        );
        $bean->date_field = 'NULL';
        $bean->datetime_field = 'NULL';
        $bean->time_field = 'NULL';
        $bean->datetimecombo_field = 'NULL';
        $bean->fixUpFormatting();
        $this->assertEquals('', $bean->date_field,'fixUpFormatting did not reset string NULL for date');
        $this->assertEquals('', $bean->datetime_field,'fixUpFormatting did not reset string NULL for time');
        $this->assertEquals('', $bean->time_field,'fixUpFormatting did not reset string NULL for datetime');
        $this->assertEquals('', $bean->datetimecombo_field,'fixUpFormatting did not reset string NULL for datetimecombo');
	}
}