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

 
if(!defined('sugarEntry')) define('sugarEntry', true);

set_include_path(
    dirname(__FILE__) . PATH_SEPARATOR .
    dirname(__FILE__) . '/..' . PATH_SEPARATOR .
    get_include_path()
);

// constant to indicate that we are running tests
if (!defined('SUGAR_PHPUNIT_RUNNER'))
    define('SUGAR_PHPUNIT_RUNNER', true);

// initialize the various globals we use
global $sugar_config, $db, $fileName, $current_user, $locale, $current_language;

if ( !isset($_SERVER['HTTP_USER_AGENT']) )
    // we are probably running tests from the command line
    $_SERVER['HTTP_USER_AGENT'] = 'cli';

// move current working directory
chdir(dirname(__FILE__) . '/..');

require_once('include/entryPoint.php');

require_once('include/utils/layout_utils.php');

$GLOBALS['db'] = DBManagerFactory::getInstance();

$current_language = $sugar_config['default_language'];
// disable the SugarLogger
$sugar_config['logger']['level'] = 'fatal';

$GLOBALS['sugar_config']['default_permissions'] = array (
		'dir_mode' => 02770,
		'file_mode' => 0777,
		'chown' => '',
		'chgrp' => '',
	);

$GLOBALS['js_version_key'] = 'testrunner';

if ( !isset($_SERVER['SERVER_SOFTWARE']) )
    $_SERVER["SERVER_SOFTWARE"] = 'PHPUnit';

// helps silence the license checking when running unit tests.
$_SESSION['VALIDATION_EXPIRES_IN'] = 'valid';

$GLOBALS['startTime'] = microtime(true);

// clean out the cache directory
require_once('modules/Administration/QuickRepairAndRebuild.php');
$repair = new RepairAndClear();
$repair->module_list = array();
$repair->show_output = false;
$repair->clearJsLangFiles();
$repair->clearJsFiles();

// mark that we got by the admin wizard already
$focus = new Administration();
$focus->retrieveSettings();
$focus->saveSetting('system','adminwizard',1);

// include the other test tools
require_once 'SugarTestObjectUtilities.php';
require_once 'SugarTestProjectUtilities.php';
require_once 'SugarTestProjectTaskUtilities.php';
require_once 'SugarTestUserUtilities.php';
require_once 'SugarTestLangPackCreator.php';
require_once 'SugarTestThemeUtilities.php';
require_once 'SugarTestContactUtilities.php';
require_once 'SugarTestEmailUtilities.php';
require_once 'SugarTestCampaignUtilities.php';
require_once 'SugarTestLeadUtilities.php';
require_once 'SugarTestStudioUtilities.php';
require_once 'SugarTestMeetingUtilities.php';
require_once 'SugarTestCallUtilities.php';
require_once 'SugarTestAccountUtilities.php';
require_once 'SugarTestTrackerUtility.php';
require_once 'SugarTestImportUtilities.php';
require_once 'SugarTestMergeUtilities.php';
require_once 'SugarTestTaskUtilities.php';

// define our testcase subclass
class Sugar_PHPUnit_Framework_TestCase extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = FALSE;

    protected $useOutputBuffering = true;

    protected function assertPreConditions()
    {
        if(isset($GLOBALS['log'])) {
            $GLOBALS['log']->info("START TEST: {$this->getName(false)}");
        }
    }

    protected function assertPostConditions() {
        if(!empty($_REQUEST)) {
            foreach(array_keys($_REQUEST) as $k) {
		        unset($_REQUEST[$k]);
		    }
        }

        if(!empty($_POST)) {
            foreach(array_keys($_POST) as $k) {
		        unset($_POST[$k]);
		    }
        }

        if(!empty($_GET)) {
            foreach(array_keys($_GET) as $k) {
		        unset($_GET[$k]);
		    }
        }
        if(isset($GLOBALS['log'])) {
            $GLOBALS['log']->info("DONE TEST: {$this->getName(false)}");
        }
    }

    public static function tearDownAfterClass()
    {
        unset($GLOBALS['disable_date_format']);
        unset($GLOBALS['saving_relationships']);
        unset($GLOBALS['updating_relationships']);
        $GLOBALS['timedate']->clearCache();
    }
}

// define output testcase subclass
class Sugar_PHPUnit_Framework_OutputTestCase extends PHPUnit_Extensions_OutputTestCase
{
    protected $backupGlobals = FALSE;

    protected $_notRegex;
    protected $_outputCheck;

    protected function assertPostConditions() {
        if(!empty($_REQUEST)) {
            foreach(array_keys($_REQUEST) as $k) {
		        unset($_REQUEST[$k]);
		    }
        }

        if(!empty($_POST)) {
            foreach(array_keys($_POST) as $k) {
		        unset($_POST[$k]);
		    }
        }

        if(!empty($_GET)) {
            foreach(array_keys($_GET) as $k) {
		        unset($_GET[$k]);
		    }
        }
    }

    protected function NotRegexCallback($output)
    {
        if(empty($this->_notRegex)) {
            return true;
        }
        $this->assertNotRegExp($this->_notRegex, $output);
        return true;
    }

    public function setOutputCheck($callback)
    {
        if (!is_callable($callback)) {
            throw new PHPUnit_Framework_Exception;
        }

        $this->_outputCheck = $callback;
    }

    protected function runTest()
    {
		$testResult = parent::runTest();
        if($this->_outputCheck) {
            $this->assertTrue(call_user_func($this->_outputCheck, $this->output));
        }
        return $testResult;
    }

    public function expectOutputNotRegex($expectedRegex)
    {
        if (is_string($expectedRegex) || is_null($expectedRegex)) {
            $this->_notRegex = $expectedRegex;
        }

        $this->setOutputCheck(array($this, "NotRegexCallback"));
    }

}

// define a mock logger interface; used for capturing logging messages emited
// the test suite
class SugarMockLogger
{
	private $_messages = array();

	public function __call($method, $message)
	{
		$this->messages[] = strtoupper($method) . ': ' . $message[0];
	}

	public function getLastMessage()
	{
		return end($this->messages);
	}

	public function getMessageCount()
	{
		return count($this->messages);
	}
}
