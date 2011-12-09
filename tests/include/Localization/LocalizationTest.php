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

 
require_once 'include/Localization/Localization.php';

class LocalizationTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->_locale = new Localization();
        $this->_user = SugarTestUserUtilities::createAnonymousUser();
    }
    
    public function tearDown()
    {
    	SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
    }
    
    public function providerGetLocaleFormattedName()
    {
        return array(
            array(
                't s f l',
                'Mason',
                'Hu',
                'Mr.',
                'Saler',
                'Saler Mr. Mason Hu',
                ),
            array(
                'l f',
                'Mason',
                'Hu',
                '',
                '',
                'Hu Mason',
                ),
                    
            );
    }
    
    /**
     * @dataProvider providerGetLocaleFormattedName
     */
    public function testGetLocaleFormattedNameUsingFormatInUserPreference($nameFormat,$firstName,$lastName,$salutation,$title,$expectedOutput)
    {
    	$this->_user->setPreference('default_locale_name_format', $nameFormat);
    	$outputName = $this->_locale->getLocaleFormattedName($firstName, $lastName, $salutation, $title, '',$this->_user);
    	$this->assertEquals($expectedOutput, $outputName);
    }
    
    /**
     * @dataProvider providerGetLocaleFormattedName
     */
    public function testGetLocaleFormattedNameUsingFormatSpecified($nameFormat,$firstName,$lastName,$salutation,$title,$expectedOutput)
    {
    	$outputName = $this->_locale->getLocaleFormattedName($firstName, $lastName, $salutation, $title, $nameFormat,$this->_user);
    	$this->assertEquals($expectedOutput, $outputName);
    }
    
    /**
     * @ticket 26803
     */
    public function testGetLocaleFormattedNameWhenNameIsEmpty()
    {
        $this->_user->setPreference('default_locale_name_format', 'l f');
        $expectedOutput = ' ';
        $outputName = $this->_locale->getLocaleFormattedName('', '', '', '', '',$this->_user);
        
        $this->assertEquals($expectedOutput, $outputName);
    }
    
    /**
     * @ticket 26803
     */
    public function testGetLocaleFormattedNameWhenNameIsEmptyAndReturningEmptyString()
    {
        $this->_user->setPreference('default_locale_name_format', 'l f');
        $expectedOutput = '';
        $outputName = $this->_locale->getLocaleFormattedName('', '', '', '', '',$this->_user,true);
        
        $this->assertEquals($expectedOutput, $outputName);
    }
    
    public function testCurrenciesLoadingCorrectly()
    {
        global $sugar_config;
        
        $currencies = $this->_locale->getCurrencies();
        
        $this->assertEquals($currencies['-99']['name'],$sugar_config['default_currency_name']);
        $this->assertEquals($currencies['-99']['symbol'],$sugar_config['default_currency_symbol']);
        $this->assertEquals($currencies['-99']['conversion_rate'],1);
    }
    
    public function testConvertingUnicodeStringBetweenCharsets()
    {
        $string = "アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモガギグゲゴザジズゼゾダヂヅデド";
        
        $convertedString = $this->_locale->translateCharset($string,'UTF-8','EUC-CN');
        $this->assertNotEquals($string,$convertedString);
        
        // test for this working by being able to convert back and the string match
        $convertedString = $this->_locale->translateCharset($convertedString,'EUC-CN','UTF-8');
        $this->assertEquals($string,$convertedString);
    }
    
    public function testCanDetectAsciiEncoding()
    {
        $string = 'string';
        
        $this->assertEquals(
            $this->_locale->detectCharset($string),
            'ASCII'
            );
    }
    
    public function testCanDetectUtf8Encoding()
    {
        $string = 'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモガギグゲゴザジズゼゾダヂヅデド';
        
        $this->assertEquals(
            $this->_locale->detectCharset($string),
            'UTF-8'
            );
    }
    
    public function testGetNameJsCorrectlySpecifiesMissingOrEmptyParameters()
    {
        global $app_strings;
        
        $app_strings = return_application_language($GLOBALS['current_language']);
        
        $first = 'First';
        $last = 'Last';
        $salutation = 'Sal';
        $title = 'Title';
        
        $ret = $this->_locale->getNameJs($first,$last,$salutation);
        
        $this->assertRegExp("/stuff\['s'\] = '$salutation';/",$ret);
        $this->assertRegExp("/stuff\['f'\] = '$first';/",$ret);
        $this->assertRegExp("/stuff\['l'\] = '$last';/",$ret);
        $this->assertRegExp("/stuff\['t'\] = '{$app_strings['LBL_LOCALE_NAME_EXAMPLE_TITLE']}';/",$ret);
        
        $ret = $this->_locale->getNameJs('',$last,$salutation);
        
        $this->assertRegExp("/stuff\['s'\] = '$salutation';/",$ret);
        $this->assertRegExp("/stuff\['f'\] = '{$app_strings['LBL_LOCALE_NAME_EXAMPLE_FIRST']}';/",$ret);
        $this->assertRegExp("/stuff\['l'\] = '$last';/",$ret);
        $this->assertRegExp("/stuff\['t'\] = '{$app_strings['LBL_LOCALE_NAME_EXAMPLE_TITLE']}';/",$ret);
    }
    
    public function testGetPrecedentPreferenceWithUserPreference()
    {
        $backup = $GLOBALS['sugar_config']['export_delimiter'];
        $GLOBALS['sugar_config']['export_delimiter'] = 'John is Cool';
        $this->_user->setPreference('export_delimiter','John is Really Cool');
        
        $this->assertEquals(
            $this->_locale->getPrecedentPreference('export_delimiter',$this->_user),
            $this->_user->getPreference('export_delimiter')
            );
        
        $GLOBALS['sugar_config']['export_delimiter'] = $backup;
    }
    
    public function testGetPrecedentPreferenceWithNoUserPreference()
    {
        $backup = $GLOBALS['sugar_config']['export_delimiter'];
        $GLOBALS['sugar_config']['export_delimiter'] = 'John is Cool';
        
        $this->assertEquals(
            $this->_locale->getPrecedentPreference('export_delimiter',$this->_user),
            $GLOBALS['sugar_config']['export_delimiter']
            );
        
        $GLOBALS['sugar_config']['export_delimiter'] = $backup;
    }
    
    /**
     * @ticket 33086
     */
    public function testGetPrecedentPreferenceWithUserPreferenceAndSpecifiedConfigKey()
    {
        $backup = $GLOBALS['sugar_config']['export_delimiter'];
        $GLOBALS['sugar_config']['export_delimiter'] = 'John is Cool';
        $this->_user->setPreference('export_delimiter','');
        $GLOBALS['sugar_config']['default_random_setting_for_localization_test'] = 'John is not Cool at all';
        
        $this->assertEquals(
            $this->_locale->getPrecedentPreference('export_delimiter',$this->_user,'default_random_setting_for_localization_test'),
            $GLOBALS['sugar_config']['default_random_setting_for_localization_test']
            );
        
        $backup = $GLOBALS['sugar_config']['export_delimiter'];
        unset($GLOBALS['sugar_config']['default_random_setting_for_localization_test']);
    }
    
    /**
     * @ticket 39171
     */
    public function testGetPrecedentPreferenceForDefaultEmailCharset()
    {
        $emailSettings = array('defaultOutboundCharset' => 'something fun');
        $this->_user->setPreference('emailSettings',$emailSettings, 0, 'Emails');
        
        $this->assertEquals(
            $this->_locale->getPrecedentPreference('default_email_charset',$this->_user),
            $emailSettings['defaultOutboundCharset']
            );
    }
    
    /**
     * @ticket 23992
     */
    public function testGetCurrencySymbol()
    {
        $this->_user->setPreference('default_currency_symbol','&&');
        
        $this->assertEquals(
            $this->_locale->getCurrencySymbol($this->_user),
            '&&'
            );
    }
    
    /**
     * @ticket 23992
     */
    public function testGetLocaleFormattedNumberWithNoCurrencySymbolSpecified()
    {
        $this->_user->setPreference('default_currency_symbol','**');
        $this->_user->setPreference('default_decimal_separator','.');
        
        $this->assertEquals(
            $this->_locale->getLocaleFormattedNumber(20,'',true,$this->_user),
            '**20'
            );
    }
}
