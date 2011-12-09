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

 
require_once('modules/Emails/EmailUI.php');

/**
 * Test cases for Bug 9755
 */
class FindEmailFromBeanIdTest extends Sugar_PHPUnit_Framework_TestCase
{
	private $emailUI;
	private $beanIds, $beanType, $whereArr;
	private $resultQuery, $expectedQuery;
	
	function setUp()
	{
		global $current_user, $currentModule ;
		$current_user = SugarTestUserUtilities::createAnonymousUser();
		$this->emailUI = new EmailUI();
		$this->beanIds[] = '8744c7d9-9e4b-2338-cb76-4ab0a3d0a651';
		$this->beanIds[] = '8749a110-1d85-4562-fa23-4ab0a3c65e12';
		$this->beanIds[] = '874c1242-4645-898d-238a-4ab0a3f7e7c3';
		$this->beanType = 'users';
		$this->whereArr['first_name'] = 'testfn';
		$this->whereArr['last_name'] = 'testln';
		$this->whereArr['email_address'] = 'test@example.com';
		$this->expectedQuery = <<<EOQ
SELECT users.id, users.first_name, users.last_name, eabr.primary_address, ea.email_address, 'Users' module FROM users JOIN email_addr_bean_rel eabr ON (users.id = eabr.bean_id and eabr.deleted=0) JOIN email_addresses ea ON (eabr.email_address_id = ea.id)  WHERE (users.deleted = 0 AND eabr.primary_address = 1 AND users.id in ('8744c7d9-9e4b-2338-cb76-4ab0a3d0a651','8749a110-1d85-4562-fa23-4ab0a3c65e12','874c1242-4645-898d-238a-4ab0a3f7e7c3')) AND (first_name LIKE 'testfn%' OR last_name LIKE 'testln%' OR email_address LIKE 'test@example.com%')
EOQ;
	}
	
	function tearDown()
	{
		unset($this->emailUI);
		SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
	}
	
	function testFindEmailFromBeanIdTest()
	{
		//$this->resultQuery = $this->emailUI->findEmailFromBeanIds('', $this->beanType, $this->whereArr);
		$this->resultQuery = $this->emailUI->findEmailFromBeanIds($this->beanIds, $this->beanType, $this->whereArr);
		$this->assertEquals($this->expectedQuery, $this->resultQuery);
	}
}

?>