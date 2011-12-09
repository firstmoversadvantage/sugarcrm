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

 
require_once 'include/ListView/ListViewSmarty.php';

/**
 * Bug45566Test
 * 
 * A simple test to verify that we still have a uid form element even when the ListViewSmarty multiSelect class variable is set to false
 * Other verifications will be needed, but this was a critical variable that was missing
 *
 */
class Bug45566Test extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['current_user']->is_admin = 1;
    }

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
    }


    public function testListViewDisplayMultiSelect()
    {
        $lv = new ListViewSmarty();
        $lv->multiSelect = false;
        $lv->should_process = true;
        $account = new Account();
        $lv->seed = $account;
        $lv->displayColumns = array();
        $mockData = array();
        $mockData['data'] = array();
        $mockData['pageData'] = array('ordering'=>'ASC', 'offsets' => array('current'=>0, 'next'=>0, 'total'=>0), 'bean'=>array('moduleDir'=>$account->module_dir));
        $lv->process('include/ListView/ListViewGeneric.tpl', $mockData, $account->module_dir);
        $this->assertEquals('<textarea style="display: none" name="uid"></textarea>', $lv->ss->_tpl_vars['multiSelectData'], 'Assert that multiSelectData Smarty variable was still assigned');
    }

}

?>