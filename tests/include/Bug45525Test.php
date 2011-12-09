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


class Bug45525 extends Sugar_PHPUnit_Framework_TestCase
{
   
    /**
     * @group Bug45525
     */
    var $testLangFile = "cache/upload/myLang.php";

    public function setUp()
    {
    }


    public function tearDown()
    {
    }

    public function testOverwriteDropDown()
    {
      global $app_list_strings;
      $app_list_strings = array("TestList" => array ("A" => "Option A", "B" => "Option B", "C" => "Option C"));

      require_once 'include/utils.php';

      file_put_contents($this->testLangFile, "<?php\n\$app_list_strings['TestList']['D'] = 'Option D';\n?>");

      // Initially TestList should have 3 items
      $this->assertEquals(3, count($app_list_strings['TestList']));

      $app_list_strings = _mergeCustomAppListStrings($this->testLangFile, $app_list_strings);

      // After merge with custom language file, TestList should have just 1 item (standard behaviour)
      $this->assertEquals(1, count($app_list_strings['TestList']));

      unlink($this->testLangFile);

      unset($GLOBALS['app_list_strings']);
    }

    public function testAppendDropDown()
    {
      global $app_list_strings;
      $app_list_strings = array("TestList" => array ("A" => "Option A", "B" => "Option B", "C" => "Option C"));

      require_once 'include/utils.php';

      file_put_contents($this->testLangFile, "<?php\n\$exemptDropdowns[] = 'TestList';\n\$app_list_strings['TestList']['D'] = 'Option D';\n?>");

      // Initially TestList should have 3 items
      $this->assertEquals(3, count($app_list_strings['TestList']));

      $app_list_strings = _mergeCustomAppListStrings($this->testLangFile, $app_list_strings);

      // After merge with custom language file, TestList should have 4 items (after-fix behaviour)
      $this->assertEquals(4, count($app_list_strings['TestList']));

      unlink($this->testLangFile);

      unset($GLOBALS['app_list_strings']);
    }

}

