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

 
class SugarTestLangPackCreatorTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        SugarCache::$isCacheReset = false;

        if( empty($GLOBALS['current_language']) )
            $GLOBALS['current_language'] = $GLOBALS['sugar_config']['default_language'];
    }
    
    public function testSetAnyLanguageStrings() 
    {
        $langpack = new SugarTestLangPackCreator();
        
        $langpack->setAppString('NTC_WELCOME','stringname');
        $langpack->setAppListString('checkbox_dom',array(''=>'','1'=>'Yep','2'=>'Nada'));
        $langpack->setModString('LBL_MODULE_NAME','stringname','Contacts');
        $langpack->save();
        
        $app_strings = return_application_language($GLOBALS['current_language']);
        $app_list_strings = return_app_list_strings_language($GLOBALS['current_language']);
        $mod_strings = return_module_language($GLOBALS['current_language'], 'Contacts');
        
        $this->assertEquals($app_strings['NTC_WELCOME'],'stringname');
        
        $this->assertEquals($app_list_strings['checkbox_dom'],
            array(''=>'','1'=>'Yep','2'=>'Nada'));
        
        $this->assertEquals($mod_strings['LBL_MODULE_NAME'],'stringname');
    }
    
    public function testUndoStringsChangesMade()
    {
        $langpack = new SugarTestLangPackCreator();
        
        $app_strings = return_application_language($GLOBALS['current_language']);
        $prevString = $app_strings['NTC_WELCOME'];
        
        $langpack->setAppString('NTC_WELCOME','stringname');
        $langpack->save();
        
        $app_strings = return_application_language($GLOBALS['current_language']);
        
        $this->assertEquals($app_strings['NTC_WELCOME'],'stringname');
        
        // call the destructor directly to undo our changes
        unset($langpack);
        
        $app_strings = return_application_language($GLOBALS['current_language']);
        
        $this->assertEquals($app_strings['NTC_WELCOME'],$prevString);
    }
}
