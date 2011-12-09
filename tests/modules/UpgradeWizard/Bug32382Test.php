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

 
class Bug32382Test extends Sugar_PHPUnit_Framework_TestCase 
{
    public function setUp() 
    {
        //Create the language files with bad name
        if(file_exists('custom/include/language/en_us.lang.php')) {
           copy('custom/include/language/en_us.lang.php', 'custom/include/language/en_us.lang.php.backup');
        }
        
        //Simulate the .bak file that was created
        if( $fh = @fopen('custom/include/language/en_us.lang.php.bak', 'w+') )
        {
    $string = <<<EOQ
<?php
\$app_list_strings['this would have been missed!'] = array (
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
);

\$app_list_strings['a_test_1'] = array (
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
);

//a_test_1 is the same, nothing was wrong with it
\$app_list_strings['a_test_that_is_okay'] = array (
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
);
  
//b_test_2 has four entries, but "4"
\$app_list_strings['b_test_2'] = array (
    '0' => 'Zero',
    '1' => 'One',
    '2' => 'Two',
    '4' => 'Four',
);

//c_test_3 has four entries
\$app_list_strings['c_test_3'] = array (
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
    'd' => 'D',
);

\$GLOBALS['app_list_strings']['b_test_2'] = array (
    '0' => 'Zero',
    '1' => 'One',
    '2' => 'Two',
    '3' => 'Three',
);

\$GLOBALS['app_list_strings']['c_test_3'] = array (
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
    'd' => 'D',
    'e' => 'E',
);

\$GLOBALS['app_list_strings']['c_test_3'] = array (
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
    'd' => 'D',
    'f' => 'F',
);
EOQ;
           fputs( $fh, $string);
           fclose( $fh );
        } 
        
        
        //Simulate the .php file that was created
        if( $fh = @fopen('custom/include/language/en_us.lang.php', 'w+') )
        {
    $string = <<<EOQ
<?php
\$GLOBALS['app_list_strings']['a_test_that_is_okay'] = array (
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
  );
  
\$GLOBALS['app_list_strings']['a_test__'] = array (
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
);  
  
\$GLOBALS['app_list_strings']['b_test__'] = array (
    '0' => 'Zero',
    '1' => 'One',
    '2' => 'Two',
    '4' => 'Four',
);
  
\$GLOBALS['app_list_strings']['c_test__'] = array (  
    'a' => 'A',
    'b' => 'B',
    'c' => 'C',
    'd' => 'D',
);
EOQ;
           fputs( $fh, $string);
           fclose( $fh );      
        }
        
    }

    public function tearDown() 
    {
        if(file_exists('custom/include/language/en_us.lang.php.backup')) {
           copy('custom/include/language/en_us.lang.php.backup', 'custom/include/language/en_us.lang.php');
           unlink('custom/include/language/en_us.lang.php.backup');  
        } else {
           unlink('custom/include/language/en_us.lang.php');
        }
        
        if(file_exists('custom/include/language/en_us.lang.php.bak')) {
           unlink('custom/include/language/en_us.lang.php.bak');
        }   
    
        if(file_exists('custom/include/language/en_us.lang.php.php_bak')) {
           unlink('custom/include/language/en_us.lang.php.php_bak');
        }
        
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
    }

    public function test_dropdown_fixed() 
    {	
        require_once('modules/UpgradeWizard/uw_utils.php');
        fix_dropdown_list();
            
        //Check to make sure we don't have the buggy format where '$GLOBALS["app_list_strings"] = array (...' was declared
        $contents = file_get_contents('custom/include/language/en_us.lang.php');
        
        unset($GLOBALS['app_list_strings']);
        require('custom/include/language/en_us.lang.php');
    
        $this->assertTrue(isset($GLOBALS['app_list_strings']['this_would_have_been_missed_']), "Assert that 'this would have been missed! key was fixed");
        
        preg_match_all('/a_test_that_is_okay/', $contents, $matches);
        $this->assertEquals(count($matches[0]), 1, "Assert that a_test_is_okay entry exists");       
        
        $this->assertEquals(count($GLOBALS['app_list_strings']['a_test_that_is_okay']), 3, "Assert that a_test_that_is_okay has 3 items");
        
        preg_match_all('/b_test__/', $contents, $matches);
        $this->assertEquals(count($matches[0]), 2, "Assert that b_test__ is declared twice");    
        
        $this->assertEquals(count($GLOBALS['app_list_strings']['b_test__']), 4, "Assert that b_test__ is additive and has 4 entries");
        
        preg_match_all('/c_test__/', $contents, $matches);
        $this->assertEquals(count($matches[0]), 2, "Assert that c_test__ is declared twice");
        
        $this->assertEquals(count($GLOBALS['app_list_strings']['c_test__']), 5, "Assert that c_test__ is additive and contains 5 entries");  
    }
}