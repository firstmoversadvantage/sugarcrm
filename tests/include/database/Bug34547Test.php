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


require_once 'include/database/DBManagerFactory.php';

class Bug34547Test extends Sugar_PHPUnit_Framework_TestCase 
{
	
    private $_has_mysqli_disabled;	
    private $_db;

    public function setUp() 
    {
        $this->_db = DBManagerFactory::getInstance();
        if(get_class($this->_db) != 'MysqlManager' && get_class($this->_db) != 'MysqliManager') {
            $this->markTestSkipped("Skipping test if not mysql or mysqli configuration");
        }
        
        unset($GLOBALS['dbinstances']);

        $this->_has_mysqli_disabled = (!empty($GLOBALS['sugar_config']['mysqli_disabled']) && $GLOBALS['sugar_config']['mysqli_disabled'] === TRUE);
        if(!$this->_has_mysqli_disabled) {
            $GLOBALS['sugar_config']['mysqli_disabled'] = TRUE;
        }
    }

    public function tearDown() 
    {
        if(!$this->_has_mysqli_disabled) {
           unset($GLOBALS['sugar_config']['mysqli_disabled']);
        }
        unset($GLOBALS['dbinstances']);
    }
        
    public function testMysqliDisabledInGetInstance() 
    {
        $this->_db = DBManagerFactory::getInstance();
        $this->assertEquals('MysqlManager', get_class($this->_db), "Assert that MysqliManager is not disabled");
    }

}