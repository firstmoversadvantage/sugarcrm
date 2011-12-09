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

 
require_once 'include/vCard.php';

class vCardTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $beanList = array();
        $beanFiles = array();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        $GLOBALS['beanList']['vCardMockModule'] = 'vCardMockModule';
        $GLOBALS['beanFiles']['vCardMockModule'] = 'tests/include/vCard/vCardTest.php';
    }

    public function tearDown()
    {
        unset($GLOBALS['current_user']);
        unset($GLOBALS['beanList']);
        unset($GLOBALS['beanFiles']);
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
    }

    /**
     * @ticket 10419
     */
	public function testImportedVcardWithDifferentCharsetIsTranslatedToTheDefaultCharset()
    {
        $filename  = dirname(__FILE__)."/ISO88591SampleFile.vcf";
        $module = "vCardMockModule";

        $vcard = new vCard();
        $record = $vcard->importVCard($filename,$module);

        $bean = new vCardMockModule;
        $bean = $bean->retrieve($record);

        $this->assertEquals('Hans Müster',$bean->first_name.' '.$bean->last_name);
    }

    public function testImportedVcardWithSameCharsetIsNotTranslated()
    {
        $filename  = dirname(__FILE__)."/UTF8SampleFile.vcf";
        $module = "vCardMockModule";

        $vcard = new vCard();
        $record = $vcard->importVCard($filename,$module);

        $bean = new vCardMockModule;
        $bean = $bean->retrieve($record);

        $this->assertEquals('Hans Müster',$bean->first_name.' '.$bean->last_name);
    }

    public function vCardNames()
    {
        return array(
            array('', "Last Name"),
            array('First Name', "Last Name"),
            array("Иван", "Č, Ć ŐŐŐ Lastname"),
        );
    }

    /**
     * @ticket 24487
	 * @dataProvider vCardNames
     */
    public function testExportVcard($fname, $lname)
    {
        $vcard = new vCard();

        $data = new vCardMockModule();
        $data->first_name = $fname;
        $data->last_name = $lname;
        $GLOBALS['current_user']->setPreference('default_export_charset', 'UTF-8');
        $id = $data->save();

        $vcard->loadContact($id, 'vCardMockModule');
        $cardtext = $vcard->toString();

        $this->assertContains("N;CHARSET=utf-8:$lname;$fname", $cardtext, "Cannot find N name", true);
        $this->assertContains("FN;CHARSET=utf-8: $fname $lname", $cardtext, "Cannot find FN name", true);
    }
    
    public function testClear()
    {
        $vcard = new vCard();
        $vcard->setProperty('dog','cat');
        $vcard->clear();
        
        $this->assertNull($vcard->getProperty('dog'));
    }
    
    public function testSetProperty()
    {
        $vcard = new vCard();
        $vcard->setProperty('dog','cat');
        
        $this->assertEquals('cat',$vcard->getProperty('dog'));
    }
    
    public function testGetPropertyThatDoesNotExist()
    {
        $vcard = new vCard();
        
        $this->assertNull($vcard->getProperty('dog'));
    }
    
    public function testSetTitle()
    {
        $vcard = new vCard();
        $vcard->setTitle('cat');
        
        $this->assertEquals('cat',$vcard->getProperty('TITLE'));
    }
    
    public function testSetORG()
    {
        $vcard = new vCard();
        $vcard->setORG('foo','bar');
        
        $this->assertEquals('foo;bar',$vcard->getProperty('ORG'));
    }
}

class vCardMockModule extends Person
{
    public static $_savedObjects = array();
    
    public $first_name;
    public $last_name;
    public $salutation;
    public $phone_fax;
    public $phone_home;
    public $phone_mobile;
    public $phone_work;
    public $email1;
    public $primary_address_street;
    public $primary_address_city;
    public $primary_address_state;
    public $primary_address_postalcode;
    public $primary_address_country;
    public $department;
    public $title;

    public function save()
    {
        $this->id = create_guid();

        self::$_savedObjects[$this->id] = $this;

        return $this->id;
    }

    public function retrieve($id = -1, $encode=true,$deleted=true)
	{
        if ( isset(self::$_savedObjects[$id]) ) {
            foreach(get_object_vars(self::$_savedObjects[$id]) as $var => $val) {
                $this->$var = $val;
            }
            return self::$_savedObjects[$id];
        }

        return null;
    }

    public function ACLFilterFields()
    {
    }
}