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

class DBManagerFactoryTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_oldSugarConfig;
    
    public function setUp()
    {
        $this->_oldSugarConfig = $GLOBALS['sugar_config'];
    }
    
    public function tearDown() 
    {
        $GLOBALS['sugar_config'] = $this->_oldSugarConfig;
    }
    
    public function testGetInstance()
    {
        $db = &DBManagerFactory::getInstance();
        
        $this->assertTrue($db instanceOf DBManager,"Should return a DBManger object");
    }
    
    public function testGetInstanceCheckMysqlDriverChoosen()
    {
        if ( $GLOBALS['db']->dbType != 'mysql' )
            $this->markTestSkipped('Only applies to SQL Server');
        
        $db = &DBManagerFactory::getInstance();
        
        if ( function_exists('mysqli_connect') )
            $this->assertTrue($db instanceOf MysqliManager,"Should return a MysqliManager object");
        else
            $this->assertTrue($db instanceOf MysqlManager,"Should return a MysqlManager object");
    }
    
    /**
     * @ticket 27781
     */
    public function testGetInstanceMssqlDefaultSelection()
    {
        if ( $GLOBALS['db']->dbType != 'mssql' )
            $this->markTestSkipped('Only applies to SQL Server');
        
        $GLOBALS['sugar_config']['db_mssql_force_driver'] = '';
        
        $db = &DBManagerFactory::getInstance();
        
        if ( function_exists('sqlsrv_connect') )
            $this->assertTrue($db instanceOf SqlsrvManager,"Should return a SqlsrvManager object");
        elseif ( is_freetds() )
            $this->assertTrue($db instanceOf FreeTDSManager,"Should return a FreeTDSManager object");
        else
            $this->assertTrue($db instanceOf MssqlManager,"Should return a MssqlManager object");
    }
    
    /**
     * @ticket 27781
     */
    public function testGetInstanceMssqlForceFreetdsSelection()
    {
        if ( $GLOBALS['db']->dbType != 'mssql' || !is_freetds() )
            $this->markTestSkipped('Only applies to SQL Server FreeTDS');
        
        $GLOBALS['sugar_config']['db_mssql_force_driver'] = 'freetds';
        
        $db = &DBManagerFactory::getInstance();
        
        $this->assertTrue($db instanceOf FreeTDSManager,"Should return a FreeTDSManager object");
    }
    
    /**
     * @ticket 27781
     */
    public function testGetInstanceMssqlForceMssqlSelection()
    {
        if ( $GLOBALS['db']->dbType != 'mssql' || !function_exists('mssql_connect') )
            $this->markTestSkipped('Only applies to SQL Server with the Native PHP mssql Driver');
        
        $GLOBALS['sugar_config']['db_mssql_force_driver'] = 'mssql';
        
        $db = &DBManagerFactory::getInstance();
        
        if ( is_freetds() )
            $this->assertTrue($db instanceOf MssqlManager,"Should return a MssqlManager object");
        elseif ( function_exists('mssql_connect') )
        $this->assertTrue($db instanceOf MssqlManager,"Should return a MssqlManager object");
        else
            $this->assertTrue($db instanceOf SqlsrvManager,"Should return a SqlsrvManager object");
    }
    
    /**
     * @ticket 27781
     */
    public function testGetInstanceMssqlForceSqlsrvSelection()
    {
        if ( $GLOBALS['db']->dbType != 'mssql' || !function_exists('sqlsrv_connect') )
            $this->markTestSkipped('Only applies to SQL Server');
        
        $GLOBALS['sugar_config']['db_mssql_force_driver'] = 'sqlsrv';
        
        $db = &DBManagerFactory::getInstance();
        
        if ( is_freetds() && !function_exists('sqlsrv_connect') )
            $this->assertTrue($db instanceOf FreeTDSManager,"Should return a FreeTDSManager object");
        elseif ( function_exists('mssql_connect') && !function_exists('sqlsrv_connect') )
            $this->assertTrue($db instanceOf MssqlManager,"Should return a MssqlManager object");
        else
        $this->assertTrue($db instanceOf SqlsrvManager,"Should return a SqlsrvManager object");
    }
}
