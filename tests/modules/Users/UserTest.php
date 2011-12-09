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

require_once 'modules/Users/User.php';

class UserTest extends Sugar_PHPUnit_Framework_TestCase
{
	protected $_user = null;

	public function setUp() 
    {
    	$this->_user = SugarTestUserUtilities::createAnonymousUser();
    	$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
	}
	
	public function tearDown()
	{
	    unset($GLOBALS['current_user']);
	    SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
	}

	public function testSettingAUserPreference() 
    {
        $this->_user->setPreference('test_pref','dog');

        $this->assertEquals('dog',$this->_user->getPreference('test_pref'));
    }
    
    public function testGettingSystemPreferenceWhenNoUserPreferenceExists()
    {
        $GLOBALS['sugar_config']['somewhackypreference'] = 'somewhackyvalue';
        
        $result = $this->_user->getPreference('somewhackypreference');
        
        unset($GLOBALS['sugar_config']['somewhackypreference']);
        
        $this->assertEquals('somewhackyvalue',$result);
    }
    
    /**
     * @ticket 42667
     */
    public function testGettingSystemPreferenceWhenNoUserPreferenceExistsForEmailDefaultClient()
    {
        if ( isset($GLOBALS['sugar_config']['email_default_client']) ) {
            $oldvalue = $GLOBALS['sugar_config']['email_default_client'];
        }
        $GLOBALS['sugar_config']['email_default_client'] = 'somewhackyvalue';
        
        $result = $this->_user->getPreference('email_link_type');
        
        if ( isset($oldvalue) ) {
            $GLOBALS['sugar_config']['email_default_client'] = $oldvalue;
        }
        else {
            unset($GLOBALS['sugar_config']['email_default_client']);
        }
        
        $this->assertEquals('somewhackyvalue',$result);
    }
    
    public function testResetingUserPreferences()
    {
        $this->_user->setPreference('test_pref','dog');

        $this->_user->resetPreferences();
        
        $this->assertNull($this->_user->getPreference('test_pref'));
    }
    
    /**
     * @ticket 36657
     */
    public function testCertainPrefsAreNotResetWhenResetingUserPreferences()
    {
        $this->_user->setPreference('ut','1');
        $this->_user->setPreference('timezone','GMT');

        $this->_user->resetPreferences();
        
        $this->assertEquals('1',$this->_user->getPreference('ut'));
        $this->assertEquals('GMT',$this->_user->getPreference('timezone'));
    }

    public function testDeprecatedUserPreferenceInterface()
    {
        User::setPreference('deprecated_pref','dog',0,'global',$this->_user);
        
        $this->assertEquals('dog',User::getPreference('deprecated_pref','global',$this->_user));
    }
    
    public function testSavingToMultipleUserPreferenceCategories()
    {
        $this->_user->setPreference('test_pref1','dog',0,'cat1');
        $this->_user->setPreference('test_pref2','dog',0,'cat2');
        
        $this->_user->savePreferencesToDB();
        
        $this->assertEquals(
            'cat1',
            $GLOBALS['db']->getOne("SELECT category FROM user_preferences WHERE assigned_user_id = '{$this->_user->id}' AND category = 'cat1'")
            );
        
        $this->assertEquals(
            'cat2',
            $GLOBALS['db']->getOne("SELECT category FROM user_preferences WHERE assigned_user_id = '{$this->_user->id}' AND category = 'cat2'")
            );
    }
}

