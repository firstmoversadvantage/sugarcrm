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


require_once('modules/Studio/wizards/RenameModules.php');


class RenameModulesTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $language;

    public function setup()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->language = 'en_us';
    }

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }


    public function testGetRenamedModules()
    {
        $rm = new RenameModules();
        $this->assertEquals(0, count($rm->getRenamedModules()) );
    }

    
    public function testRenameContactsModule()
    {
        $module = 'Accounts';
        $newSingular = 'Company';
        $newPlural = 'Companies';

        $rm = new RenameModules();

        $_REQUEST['slot_0'] = 0;
        $_REQUEST['key_0'] = $module;
        $_REQUEST['svalue_0'] = $newSingular;
        $_REQUEST['value_0'] = $newPlural;
        $_REQUEST['delete_0'] = '';
        $_REQUEST['dropdown_lang'] = $this->language;
        $_REQUEST['dropdown_name'] = 'moduleList';

        global $app_list_strings;
        if (!isset($app_list_strings['parent_type_display'][$module])) {
            $app_list_strings['parent_type_display'][$module] = 'Account';
        }
        $rm->save(FALSE);

        //Test app list strings
        $app_list_string = return_app_list_strings_language('en_us');
        $this->assertEquals($newSingular, $app_list_string['moduleListSingular'][$module] );
        $this->assertEquals($newPlural, $app_list_string['moduleList'][$module] );
        $this->assertEquals($newSingular, $app_list_string['parent_type_display'][$module] );

        //Test module strings for account
        $accountStrings = return_module_language('en_us',$module, TRUE);
        $this->assertEquals('Create Company', $accountStrings['LNK_NEW_ACCOUNT'], "Rename module failed for modules modStrings.");
        $this->assertEquals('View Companies', $accountStrings['LNK_ACCOUNT_LIST'], "Rename module failed for modules modStrings.");
        $this->assertEquals('Import Companies', $accountStrings['LNK_IMPORT_ACCOUNTS'], "Rename module failed for modules modStrings.");
        $this->assertEquals('Company Search', $accountStrings['LBL_SEARCH_FORM_TITLE'], "Rename module failed for modules modStrings.");

        //Test related link renames
        $contactStrings = return_module_language('en_us','Contacts', TRUE);
        $this->assertEquals('Company Name:', $contactStrings['LBL_ACCOUNT_NAME'], "Rename related links failed for module.");
        $this->assertEquals('Company ID:', $contactStrings['LBL_ACCOUNT_ID'], "Rename related links failed for module.");

        //Test subpanel renames
        $campaignStrings = return_module_language('en_us','Campaigns', TRUE);
        $this->assertEquals('Companies', $campaignStrings['LBL_CAMPAIGN_ACCOUNTS_SUBPANEL_TITLE'], "Renaming subpanels failed for module.");
        // bug 45554: ensure labels are changed
        $this->assertEquals('Companies', $campaignStrings['LBL_ACCOUNTS'], 'Renaming labels failed for module.');
    
        //Ensure we recorded which modules were modified.
        $renamedModules = $rm->getRenamedModules();
        $this->assertTrue( count($renamedModules) > 0 );

        $this->removeCustomAppStrings();
        $this->removeModuleStrings( $renamedModules );
    }

    public function testRenameNonExistantModule()
    {
        $module = 'UnitTestDNEModule';
        $newSingular = 'UnitTest';
        $newPlural = 'UnitTests';

        $rm = new RenameModules();

        $_REQUEST['slot_0'] = 0;
        $_REQUEST['key_0'] = $module;
        $_REQUEST['svalue_0'] = $newSingular;
        $_REQUEST['value_0'] = $newPlural;
        $_REQUEST['delete_0'] = '';
        $_REQUEST['dropdown_lang'] = $this->language;
        $_REQUEST['dropdown_name'] = 'moduleList';
        $_REQUEST['use_push'] = TRUE;

        $rm->save(FALSE);

        //Ensure no modules were modified
        $renamedModules = $rm->getRenamedModules();
        $this->assertTrue( count($renamedModules) == 0 );

        //Ensure none of the app list strings were modified.
        $app_list_string = return_app_list_strings_language('en_us');
        $this->assertNotEquals($newSingular, $app_list_string['moduleListSingular'][$module] );
        $this->assertNotEquals($newPlural, $app_list_string['moduleList'][$module] );
         
    }


    private function removeCustomAppStrings()
    {
        $fileName = 'custom'. DIRECTORY_SEPARATOR . 'include'. DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $this->language . '.lang.php';
        if( file_exists($fileName) )
        {
            @unlink($fileName);
        }
    }

    private function removeModuleStrings($modules)
    {
        foreach($modules as $module => $v)
        {
            $fileName = 'custom'. DIRECTORY_SEPARATOR . 'modules'. DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $this->language . '.lang.php';
            if( file_exists($fileName) )
            {
                @unlink($fileName);
            }

        }

    }

    /**
     * @group bug46880
     * making sure subpanel is not renamed twice by both plural name and singular name
     */
    public function testSubpanelRenaming()
    {
        $this->markTestSkipped('Because of bug 47239,  Skipping test.');

        $module = 'Accounts';
        $newSingular = 'Account1';
        $newPlural = 'Accounts2';

        $rm = new RenameModules();

        $_REQUEST['slot_0'] = 0;
        $_REQUEST['key_0'] = $module;
        $_REQUEST['svalue_0'] = $newSingular;
        $_REQUEST['value_0'] = $newPlural;
        $_REQUEST['delete_0'] = '';
        $_REQUEST['dropdown_lang'] = $this->language;
        $_REQUEST['dropdown_name'] = 'moduleList';

        global $app_list_strings;
        if (!isset($app_list_strings['parent_type_display'][$module])) {
            $app_list_strings['parent_type_display'][$module] = 'Account';
        }
        $rm->save(FALSE);

        //Test subpanel renames
        $bugStrings = return_module_language('en_us','Bugs', TRUE);
        $this->assertEquals('Accounts2', $bugStrings['LBL_ACCOUNTS_SUBPANEL_TITLE'], "Renaming subpanels failed for module.");

        //Ensure we recorded which modules were modified.
        $renamedModules = $rm->getRenamedModules();
        $this->assertTrue( count($renamedModules) > 0 );

        //cleanup
        $this->removeCustomAppStrings();
        $this->removeModuleStrings( $renamedModules );
    }

    /**
     * @group bug45804
     */
    public function testDashletsRenaming()
    {
        $this->markTestSkipped('Because of bug 47239,  Skipping test.');

        $module = 'Accounts';
        $newSingular = 'Account1';
        $newPlural = 'Accounts2';

        $rm = new RenameModules();

        $_REQUEST['slot_0'] = 0;
        $_REQUEST['key_0'] = $module;
        $_REQUEST['svalue_0'] = $newSingular;
        $_REQUEST['value_0'] = $newPlural;
        $_REQUEST['delete_0'] = '';
        $_REQUEST['dropdown_lang'] = $this->language;
        $_REQUEST['dropdown_name'] = 'moduleList';

        global $app_list_strings;
        if (!isset($app_list_strings['parent_type_display'][$module])) {
            $app_list_strings['parent_type_display'][$module] = 'Account';
        }
        $rm->save(FALSE);

        //Test dashlets renames
        $callStrings = return_module_language('en_us', 'Accounts', TRUE);
        $this->assertEquals('My Accounts2', $callStrings['LBL_HOMEPAGE_TITLE'], "Renaming dashlets failed for module.");

        //Ensure we recorded which modules were modified.
        $renamedModules = $rm->getRenamedModules();
        $this->assertTrue( count($renamedModules) > 0 );

        //cleanup
        $this->removeCustomAppStrings();
        $this->removeModuleStrings( $renamedModules );
    }
}
