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


require_once 'modules/Leads/views/view.convertlead.php';

class Bug45187Test extends Sugar_PHPUnit_Framework_OutputTestCase
{
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser(true, 1);
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
    }
    
    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    /**
    * @group bug45187
    */
    public function testActivityModuleLabel()
    {
        global $sugar_config;
        global $app_list_strings;

        // init
        $lead = SugarTestLeadUtilities::createLead();
        $account = SugarTestAccountUtilities::createAccount();

        // simulate module renaming
        $org_name = $app_list_strings['moduleListSingular']['Contacts'];
        $app_list_strings['moduleListSingular']['Contacts'] = 'People';

        // set the request/post parameters before converting the lead
        $_REQUEST['module'] = 'Leads';
        $_REQUEST['action'] = 'ConvertLead';
        $_REQUEST['record'] = $lead->id;
        unset($_REQUEST['handle']);
        $_REQUEST['selectedAccount'] = $account->id;
        $sugar_config['lead_conv_activity_opt'] = 'move';

        // call display to trigger conversion
        $vc = new ViewConvertLead();
        $vc->init($lead);
        $vc->display();

        // the activity options dropdown should use the renamed module label
        $this->expectOutputRegex('/.*People<\/OPTION>.*/');

        // cleanup
        $app_list_strings['moduleListSingular']['Contacts'] = $org_name;
        unset($_REQUEST['module']);
        unset($_REQUEST['action']);
        unset($_REQUEST['record']);
        unset($_REQUEST['selectedAccount']);
        SugarTestAccountUtilities::removeAllCreatedAccounts();
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }

}
