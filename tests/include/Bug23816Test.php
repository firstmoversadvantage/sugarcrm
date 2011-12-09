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


/**
 * @ticket 23816
 *
 *		Original Bug Steps to reproduce:
 *		1) Start on the Contacts listview, click on a contact name to open the record in the detailview.
 *		2) Notice the VCR controls in the upper right of the layout - now click the edit button to go to the editview.
 *		3) Save an edit - which will return you back to the detailview
 *
 */
class Bug23816Test extends Sugar_PHPUnit_Framework_TestCase
{
    public function testVcrAfterSave()
    {
        $return_id = '1bb73165-dcd7-21b2-b648-4ded8dce0bf8';
        $_REQUEST['return_action'] = 'DetailView';
        $_REQUEST['return_module'] = 'Accounts';
        $_REQUEST['return_id'] = $return_id;
        $_REQUEST['offset'] = 4;
        
        require_once('include/formbase.php');
        $url = buildRedirectURL($return_id,'Accounts');
        
        unset($_REQUEST['return_action']);
        unset($_REQUEST['return_module']);
        unset($_REQUEST['return_id']);
        unset($_REQUEST['offset']);
        
        $this->assertContains('offset=4',$url,"Offset was not included in the redirect url");     
    }
}
