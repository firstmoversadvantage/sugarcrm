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


class Bug44515 extends Sugar_PHPUnit_Framework_TestCase
{
   
    /**
     * @group Bug44515
     */
    var $customDir = "custom/modules/ProductTemplates/formulas";

    public function setUp()
    {
        
        if (!is_dir($this->customDir))
          mkdir($this->customDir, 0700, TRUE); // Creating nested directories at a glance

        file_put_contents($this->customDir . "/customformula1.php", "<?php\nclass Customformula1 {\n}\n?>");
        file_put_contents($this->customDir . "/customformula2.php", "<?php\nclass Customformula2 {\n}\n?>");
    }


    public function tearDown()
    {
        unset($GLOBALS['price_formulas']['Customformula1']);
        unset($GLOBALS['price_formulas']['Customformula2']);
        unlink($this->customDir . "/customformula1.php");
        unlink($this->customDir . "/customformula2.php");
        rmdir($this->customDir);
    }

    public function testLoadCustomFormulas()
    {
      require_once "modules/ProductTemplates/Formulas.php";

      // At this point I expect to have 7 formulas (5 standard and 2 custom).
      $expectedIndexes = 7;
      $this->assertEquals($expectedIndexes, count($GLOBALS['price_formulas']));

      // Check if standard formulas are still in the array
      $this->assertArrayHasKey("Fixed", $GLOBALS['price_formulas']);
      $this->assertArrayHasKey("ProfitMargin", $GLOBALS['price_formulas']);
      $this->assertArrayHasKey("PercentageMarkup", $GLOBALS['price_formulas']);
      $this->assertArrayHasKey("PercentageDiscount", $GLOBALS['price_formulas']);
      $this->assertArrayHasKey("IsList", $GLOBALS['price_formulas']);
      // Check if custom formulas are in the array
      $this->assertArrayHasKey("Customformula1", $GLOBALS['price_formulas']);
      $this->assertArrayHasKey("Customformula2", $GLOBALS['price_formulas']);

      // Check if CustomFormula1 point to the right file (/custom/modules/ProductTemplates/formulas/customformula1.php)
      $_customFormula1FileName = "custom/modules/ProductTemplates/formulas/customformula1.php";
      $this->assertEquals($_customFormula1FileName, $GLOBALS['price_formulas']['Customformula1']);
    }
}

