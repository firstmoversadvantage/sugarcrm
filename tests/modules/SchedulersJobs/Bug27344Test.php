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


require_once 'modules/SchedulersJobs/SchedulersJob.php';

class Bug27344Test extends Sugar_PHPUnit_Framework_TestCase 
{
	private $_url;
    private $_initial_server_port;	
    private $_has_initial_server_port;
    private $_cron_test_file = 'cronUnitTestBug27344.php';

    public function setUp() 
    {        

        $this->_has_initial_server_port = isset($_SERVER['SERVER_PORT']);
        if ($this->_has_initial_server_port) {
            $this->_initial_server_port = $_SERVER['SERVER_PORT'];
        }

        sugar_file_put_contents($this->_cron_test_file, "<?php echo 'Hello World!';");        
    }

    public function tearDown() 
    {
        unlink($this->_cron_test_file);

        if ($this->_has_initial_server_port) {
            $_SERVER['SERVER_PORT'] = $this->_initial_server_port;
        } else {
            unset($_SERVER['SERVER_PORT']);
        }
    }
        
    public function testLocalServerPortNotUsed() 
    {

        $url = $GLOBALS['sugar_config']['site_url'] . '/' . $this->_cron_test_file;

        $_SERVER['SERVER_PORT'] = '9090';                
        $sJob = new SchedulersJob(FALSE);
        $this->assertTrue($sJob->fireUrl($url));                
    }

}
