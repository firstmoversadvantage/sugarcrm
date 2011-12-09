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

class Bug30709_Part_2_Test extends  Sugar_PHPUnit_Framework_TestCase {

function setUp() {
    //Create the language files with bad name
    if(file_exists('custom/include/language/en_us.lang.php')) {
       copy('custom/include/language/en_us.lang.php', 'custom/include/language/en_us.lang.php.backup');
    }
	
    if( $fh = @fopen('custom/include/language/en_us.lang.php', 'w+') )
    {
$string = <<<EOQ
<?php
\$GLOBALS['app_list_strings'] = array (
  'test'=>array(
    'abc' => 'ABC',
    'cbs' => 'CBS',
    'nbc' => 'NBC',
  ),
  'lead_source_dom' =>
  array (
    '' => '',
    'Cold Call' => 'Cold Call',
    'Existing Customer' => 'Existing Customer',
    'Self Generated' => 'Self Generated',
    'Employee' => 'Employee',
    'Partner' => 'Partner',
    'Public Relations' => 'Public Relations',
    'Direct Mail' => 'Direct Mail',
    'Conference' => 'Conference',
    'Trade Show' => 'Trade Show',
    'Web Site' => 'Web Site',
    'Word of mouth' => 'Word of mouth',
    'Email' => 'Email',
    'Campaign'=>'Campaign',
    'Other' => 'Other',
  ),
  'opportunity_type_dom' =>
  array (
    '' => '',
    'Existing Business' => 'Existing Business',
    'New Business' => 'New Business',
  ),
  'moduleList' =>
  array (
    'Home' => 'Home',
    'Dashboard' => 'Dashboard',
    'Contacts' => 'Contacts',
    'Accounts' => 'Accounts Module',
    'Opportunities' => 'Opportunities',
    'Cases' => 'Cases',
    'Notes' => 'Notes',
    'Calls' => 'Calls',
    'Emails' => 'Emails',
    'Meetings' => 'Meetings',
    'Tasks' => 'Tasks',
    'Calendar' => 'Calendar',
    'Leads' => 'Leads',
    'Currencies' => 'Currencies',
    'Contracts' => 'Contracts',
    'Quotes' => 'Quotes',
    'Products' => 'Products',
    'ProductCategories' => 'Product Categories',
    'ProductTypes' => 'Product Types',
    'ProductTemplates' => 'Product Catalog',
    'Reports' => 'Reports',
    'Reports_1' => 'Reports',
    'Forecasts' => 'Forecasts',
    'ForecastSchedule' => 'Forecast Schedule',
    'MergeRecords' => 'Merge Records',
    'Quotas' => 'Quotas',
    'Teams' => 'Teams',
    'Activities' => 'Activities',
    'Bugs' => 'Bug Tracker',
    'Feeds' => 'RSS',
    'iFrames' => 'My Portal',
    'TimePeriods' => 'Time Periods',
    'Project' => 'Projects',
    'ProjectTask' => 'Project Tasks',
    'Campaigns' => 'Campaigns',
    'CampaignLog' => 'Campaign Log',
    'Documents' => 'Documents',
    'Sync' => 'Sync',
    'WorkFlow' => 'Work Flow',
    'Users' => 'Users',
    'Releases' => 'Releases',
    'Prospects' => 'Targets',
    'Queues' => 'Queues',
    'EmailMarketing' => 'Email Marketing',
    'EmailTemplates' => 'Email Templates',
    'ProspectLists' => 'Target Lists',
    'SavedSearch' => 'Saved Searches',
    'Trackers' => 'Trackers',
    'TrackerPerfs' => 'Tracker Performance',
    'TrackerSessions' => 'Tracker Sessions',
    'TrackerQueries' => 'Tracker Queries',
    'FAQ' => 'FAQ',
    'Newsletters' => 'Newsletters',
    'SugarFeed' => 'Sugar Feed',
    'Library' => 'Library',
    'EmailAddresses' => 'Email Address',
    'KBDocuments' => 'Knowledge Base',
    'my_personal_module' => 'My Personal Module',    
  ),
);

\$GLOBALS['app_strings']['LBL_TEST'] = 'This is a test';
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }
}

function tearDown() {
    if(file_exists('custom/include/language/en_us.lang.php.backup')) {
       copy('custom/include/language/en_us.lang.php.backup', 'custom/include/language/en_us.lang.php');
       unlink('custom/include/language/en_us.lang.php.backup');  
    } else {
       unlink('custom/include/language/en_us.lang.php');
    }
    
    if(file_exists('custom/include/language/en_us.lang.php.bak')) {
       unlink('custom/include/language/en_us.lang.php.bak');
    }   

    $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
    $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
}

function test_dropdown_fixed() {	
    require_once('modules/UpgradeWizard/uw_utils.php');
    fix_dropdown_list();
        
    //Check to make sure we don't have the buggy format where '$GLOBALS["app_list_strings"] = array (...' was declared
    $contents = file_get_contents('custom/include/language/en_us.lang.php');
    //$this->assertFalse(preg_match('/\$GLOBALS\[\s*[\"|\']app_list_strings[\"|\']\s*\]\s+=\s+array\s+\(/', $contents));

    unset($GLOBALS['app_list_strings']);
    require('custom/include/language/en_us.lang.php');
    $this->assertEquals(count($GLOBALS['app_list_strings']),2);
    $this->assertTrue(isset($GLOBALS['app_list_strings']['moduleList']['my_personal_module']));
    $this->assertEquals($GLOBALS['app_list_strings']['moduleList']['Accounts'],'Accounts Module');
    $this->assertEquals(count($GLOBALS['app_strings']),1);
    $this->assertEquals($GLOBALS['app_strings']['LBL_TEST'],'This is a test');
}


}

?>