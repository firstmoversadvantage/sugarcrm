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


require_once 'PHPUnit/Extensions/OutputTestCase.php';

class Bugs39819_39820 extends Sugar_PHPUnit_Framework_TestCase
{
    /**
     * @ticket 39819
     * @ticket 39820
     */
    public function setUp()
    {
        if (!is_dir("custom/modules/Accounts/language")) {
            mkdir("custom/modules/Accounts/language", 0700, TRUE); // Creating nested directories at a glance
        }
    }
    
    public function testLoadEnHelp()
    {
        // en_us help on a standard module.
        file_put_contents("modules/Accounts/language/en_us.help.DetailView.html", "<h1>ENBugs39819-39820</h1>");
        
        $_SERVER['HTTP_HOST'] = "";
        $_SERVER['SCRIPT_NAME'] = "";
        $_SERVER['QUERY_STRING'] = "";

        $_REQUEST['view'] = 'documentation';
        $_REQUEST['lang'] = 'en_us';
        $_REQUEST['help_module'] = 'Accounts';
        $_REQUEST['help_action'] = 'DetailView';

        ob_start();
        require "modules/Administration/SupportPortal.php";

        $tStr = ob_get_contents();
        ob_clean();
        
        unlink("modules/Accounts/language/en_us.help.DetailView.html");
        
        // I expect to get the en_us normal help file....
        $this->assertRegExp("/.*ENBugs39819\-39820.*/", $tStr);
    }
    
    public function testLoadCustomItHelp()
    {
        // Custom help (NOT en_us) on a standard module.
        file_put_contents("custom/modules/Accounts/language/it_it.help.DetailView.html", "<h1>Bugs39819-39820</h1>");

        $_SERVER['HTTP_HOST'] = "";
        $_SERVER['SCRIPT_NAME'] = "";
        $_SERVER['QUERY_STRING'] = "";

        $_REQUEST['view'] = 'documentation';
        $_REQUEST['lang'] = 'it_it';
        $_REQUEST['help_module'] = 'Accounts';
        $_REQUEST['help_action'] = 'DetailView';
        
        ob_start();
        require "modules/Administration/SupportPortal.php";

        $tStr = ob_get_contents();
        ob_clean();

        unlink("custom/modules/Accounts/language/it_it.help.DetailView.html");
        
        // I expect to get the it_it custom help....
        $this->assertRegExp("/.*Bugs39819\-39820.*/", $tStr);
    }
}
