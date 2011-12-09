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

 
require_once('include/SugarFields/Fields/Relate/SugarFieldRelate.php');

class SugarFieldRelateTest extends Sugar_PHPUnit_Framework_TestCase
{
	public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
	public function testFormatContactNameWithFirstName()
	{
        $GLOBALS['current_user']->setPreference('default_locale_name_format','l f');
        
	    $vardef = array('name' => 'contact_name');
	    $value = 'John Mertic';
	    
	    $sfr = new SugarFieldRelate('relate');
	    
	    $this->assertEquals(
            $sfr->formatField($value,$vardef),
            'Mertic John'
            );
    }
    
    /**
     * @ticket 35265
     */
    public function testFormatContactNameWithoutFirstName()
	{
        $GLOBALS['current_user']->setPreference('default_locale_name_format','l f');
        
	    $vardef = array('name' => 'contact_name');
	    $value = 'Mertic';
	    
	    $sfr = new SugarFieldRelate('relate');
	    
	    $this->assertEquals(
            trim($sfr->formatField($value,$vardef)),
            'Mertic'
            );
    }
    
    /**
     * @ticket 35265
     */
    public function testFormatContactNameThatIsEmpty()
	{
        $GLOBALS['current_user']->setPreference('default_locale_name_format','l f');
        
	    $vardef = array('name' => 'contact_name');
	    $value = '';
	    
	    $sfr = new SugarFieldRelate('relate');
	    
	    $this->assertEquals(
            trim($sfr->formatField($value,$vardef)),
            ''
            );
    }
    
    public function testFormatOtherField()
	{
        $GLOBALS['current_user']->setPreference('default_locale_name_format','l f');
        
	    $vardef = array('name' => 'account_name');
	    $value = 'John Mertic';
	    
	    $sfr = new SugarFieldRelate('relate');
	    
	    $this->assertEquals(
            $sfr->formatField($value,$vardef),
            'John Mertic'
            );
    }
    
    /**
     * @group bug38548
    */
    public function testGetSearchViewSmarty(){
    	$vardef = array (
			'name' => 'assigned_user_id',
			'rname' => 'user_name',
			'id_name' => 'assigned_user_id',
			'vname' => 'LBL_ASSIGNED_TO_ID',
			'group'=>'assigned_user_name',
			'type' => 'relate',
			'table' => 'users',
			'module' => 'Users',
			'reportable'=>true,
			'isnull' => 'false',
			'dbType' => 'id',
			'audited'=>true,
			'comment' => 'User ID assigned to record',
            'duplicate_merge'=>'disabled'           
		);
		$displayParams = array();
		$sfr = new SugarFieldRelate('relate');
		$output = $sfr->getSearchViewSmarty(array(), $vardef, $displayParams, 0);
		$this->assertContains('name="{$Array.assigned_user_id', $output, 'Testing that the name property is in the form for thr assigned_user_id field');
		
		$vardef =  array (
				    'name' => 'account_name',
				    'rname' => 'name',
				    'id_name' => 'account_id',
				    'vname' => 'LBL_ACCOUNT_NAME',
				    'type' => 'relate',
				    'table' => 'accounts',
				    'join_name'=>'accounts',
				    'isnull' => 'true',
				    'module' => 'Accounts',
				    'dbType' => 'varchar',
				    'link'=>'accounts',
				    'len' => '255',
				   	 'source'=>'non-db',
				   	 'unified_search' => true,
				   	 'required' => true,
				   	 'importable' => 'required',
				     'required' => true,
				  );
		$displayParams = array();
		$sfr = new SugarFieldRelate('relate');
		$output = $sfr->getSearchViewSmarty(array(), $vardef, $displayParams, 0);
		$this->assertNotContains('name="{$Array.account_id', $output, 'Testing that the name property for account_id is not in the form.');
    }
}