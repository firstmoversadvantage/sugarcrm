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

 
require_once 'tests/service/SOAPTestCase.php';

/**
 * @group bug43696
 */
class Bug31003Test extends SOAPTestCase
{
	private $prospect;
    private $contact;

	public function setUp()
    {
        $this->_soapURL = $GLOBALS['sugar_config']['site_url'].'/soap.php';
        parent::setUp();
        $this->contact = SugarTestContactUtilities::createContact();
        $this->prospect = new Prospect();
        $this->prospect->email1 = $this->contact->email1;
        $this->prospect->save();
    }

    public function testContactByEmail()
    {
    	$result = $this->_soapClient->call('contact_by_email', array('user_name' => $this->_user->user_name, 'password' => $this->_user->user_hash, 'email_address' => $this->contact->email1));
        $this->assertTrue(!empty($result) && count($result) > 0, 'Incorrect number of results returned. HTTP Response: '.$this->_soapClient->response);
    	$this->assertEquals($result[0]['name1'], $this->contact->first_name, 'Incorrect result found');
    }

    public function tearDown()
    {
        parent::tearDown();
        $GLOBALS['db']->query("DELETE FROM prospects WHERE id = '{$this->contact->id}'");
        SugarTestContactUtilities::removeAllCreatedContacts();
    }

}
