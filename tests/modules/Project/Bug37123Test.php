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



class Bug37123Test extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        global $current_user, $currentModule ;
        $current_user = SugarTestUserUtilities::createAnonymousUser();
        $unid = uniqid();
        $time = date('Y-m-d H:i:s');

        $contact = new Contact();
        $contact->id = 'c_'.$unid;
        $contact->first_name = 'testfirst';
        $contact->last_name = 'testlast';
        $contact->new_with_id = true;
        $contact->disable_custom_fields = true;
        $contact->save();
        $this->contact = $contact;

        $account = new Account();
        $account->id = 'a_'.$unid;
        $account->first_name = 'testfirst';
        $account->last_name = 'testlast';
        $account->assigned_user_id = 'SugarUser';
        $account->new_with_id = true;
        $account->disable_custom_fields = true;
        $account->save();
        $this->account = $account;

        $ac_id = 'ac_'.$unid;
        $this->ac_id = $ac_id;//Accounts to Contacts
        $GLOBALS['db']->query("INSERT INTO accounts_contacts (id , contact_id, account_id, date_modified, deleted) values ('{$ac_id}', '{$contact->id}', '{$account->id}', '$time', 0)");
    }

    public function testRelationshipSave()
    {
        global $current_user, $currentModule ;
        $_REQUEST['relate_id'] = $this->contact->id;
        $_REQUEST['relate_to'] = 'projects_contacts';
        $unid = uniqid();
        $project = new Project();
        $project->id = 'p_' . $unid;
        $project->name = 'test project ' . $unid;
        $project->estimated_start_date = date('Y-m-d H:i:s');
        $project->estimated_end_date = date('Y-m-d H:i:s', (time() + 24*3600*7));
        $project->new_with_id = true;
        $project->disable_custom_fields = true;
        $newProjectId = $project->save();
        $this->project = $project;
        $savedProjectId =  $GLOBALS['db']->getOne("
                                SELECT project_id FROM projects_accounts
                                WHERE project_id= '{$newProjectId}'
                                AND account_id='{$this->account->id}'"
                            );
        $this->assertEquals($newProjectId, $savedProjectId);
    }

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        unset($GLOBALS['mod_strings']);

        $GLOBALS['db']->query("DELETE FROM contacts WHERE id= '{$this->contact->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts WHERE id = '{$this->account->id}'");
        $GLOBALS['db']->query("DELETE FROM accounts_contacts WHERE id = '{$this->ac_id}'");
        $GLOBALS['db']->query("DELETE FROM projects_accounts
                               WHERE project_id= '{$this->project->id}'
                               AND account_id = '{$this->account->id}'");
        unset($this->account);
        unset($this->contact);
        unset($this->project);
        unset($this->ac_id);
    }



}