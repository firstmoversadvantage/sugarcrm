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

require_once 'modules/Calls/Call.php';

class CallTest extends Sugar_PHPUnit_Framework_TestCase
{
    /**
     * @var Call our call object
     */
    private $callid;

    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
    }

    public function tearDown()
    {
        if(!empty($this->callid)) {
            $GLOBALS['db']->query("DELETE FROM calls WHERE id='{$this->callid}'");
            $GLOBALS['db']->query("DELETE FROM vcals WHERE user_id='{$GLOBALS['current_user']->id}'");
        }
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset( $GLOBALS['current_user']);
        unset( $GLOBALS['mod_strings']);
    }

    /**
     * @group bug40999
     */
    public function testCallStatus()
    {
         $call = new Call();
         $this->callid = $call->id = create_guid();
         $call->new_with_id = 1;
         $call->status = 'Test';
         $call->save();
         // then retrieve
         $call = new Call();
         $call->retrieve($this->callid);
         $this->assertEquals('Test', $call->status);
    }

    /**
     * @group bug40999
     */
    public function testCallEmptyStatus()
    {
         $call = new Call();
         $this->callid = $call->id = create_guid();
         $call->new_with_id = 1;
         $call->save();
         // then retrieve
         $call = new Call();
         $call->retrieve($this->callid);
         $this->assertEquals('Planned', $call->status);
    }

    /**
     * @group bug40999
     * Check if empty status is handled correctly
     */
    public function testCallEmptyStatusLang()
    {
        $langpack = new SugarTestLangPackCreator();
        $langpack->setModString('LBL_DEFAULT_STATUS','FAILED!','Calls');
        $langpack->save();
        $GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], 'Calls');         
        
         $call = new Call();
         $this->callid = $call->id = create_guid();
         $call->new_with_id = 1;
         $call->save();
         // then retrieve
         $call = new Call();
         $call->retrieve($this->callid);
         $this->assertEquals('Planned', $call->status);
    }

    /**
     * @group bug40999
     * Check if empty status is handled correctly
     */
    public function testCallEmptyStatusLangConfig()
    {
         $langpack = new SugarTestLangPackCreator();
         $langpack->setModString('LBL_DEFAULT_STATUS','FAILED!','Calls');
         $langpack->save();
         $GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], 'Calls');         
        
         $call = new Call();
         $call->field_defs['status']['default'] = 'My Call';
         $call = new Call();
         $this->callid = $call->id = create_guid();
         $call->new_with_id = 1;
         $call->save();
         // then retrieve
         $call = new Call();
         $call->retrieve($this->callid);
         $this->assertEquals('My Call', $call->status);
    }
}