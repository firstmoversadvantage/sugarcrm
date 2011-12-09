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

 
require_once 'include/JSON.php';

class JSONTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        unset($_SESSION['asychronous_key']);
    }
    
    public function testCanEncodeBasicArray() 
    {
        $array = array('foo' => 'bar', 'bar' => 'foo');
        $this->assertEquals(
            '{"foo":"bar","bar":"foo"}',
            JSON::encode($array)
        );
    }

    public function testCanEncodeBasicObjects() 
    {
        $obj = new stdClass();
        $obj->foo = 'bar';
        $obj->bar = 'foo';
        $this->assertEquals(
            '{"foo":"bar","bar":"foo"}',
            JSON::encode($obj)
        );
    }
    
    public function testCanEncodeMultibyteData() 
    {
        $array = array('foo' => '契約', 'bar' => '契約');
        $this->assertEquals(
            '{"foo":"\u5951\u7d04","bar":"\u5951\u7d04"}',
            JSON::encode($array)
        );
    }
    
    public function testCanDecodeObjectIntoArray()
    {
        $array = array('foo' => 'bar', 'bar' => 'foo');
        $this->assertEquals(
            JSON::decode('{"foo":"bar","bar":"foo"}'),
            $array
        );
    }
    
    public function testCanDecodeMultibyteData() 
    {
        $array = array('foo' => '契約', 'bar' => '契約');
        $this->assertEquals(
            JSON::decode('{"foo":"\u5951\u7d04","bar":"\u5951\u7d04"}'),
            $array
        );
    }
    
    public function testEncodeRealWorks()
    {
        $array = array('foo' => 'bar', 'bar' => 'foo');
        $this->assertEquals(
            '{"foo":"bar","bar":"foo"}',
            JSON::encodeReal($array)
        );
    }
    
    public function testDecodeRealWorks()
    {
        $array = array('foo' => 'bar', 'bar' => 'foo');
        $this->assertEquals(
            JSON::decodeReal('{"foo":"bar","bar":"foo"}'),
            $array
        );
    }

    public function testCanDecodeHomefinder(){
        $response = '{"data":{"meta":{"currentPage":1,"totalMatched":1,"totalPages":1,"executionTime":0.025315999984741},"affiliates":[{"name":"Los Angeles Times","profileName":"latimes","parentCompany":"Tribune Company","isActive":true,"hasEcommerceEnabled":true,"profileNameLong":"latimes","homePageUrl":"http:\/\/www.latimes.com\/classified\/realestate\/","createDateTime":"2008-07-25T00:00:00-05:00","updateDateTime":"2011-02-16T00:00:00-06:00","id":137}]},"status":{"code":200,"errorStack":null}}';
        $json = new JSON();
        $decode = $json->decode($response);
        $this->assertNotEmpty($decode['data']['affiliates'][0]['profileName'], "Did not decode correctly");
    }

    public function testCanDecodeHomefinderAsObject(){
        $response = '{"data":{"meta":{"currentPage":1,"totalMatched":1,"totalPages":1,"executionTime":0.025315999984741},"affiliates":[{"name":"Los Angeles Times","profileName":"latimes","parentCompany":"Tribune Company","isActive":true,"hasEcommerceEnabled":true,"profileNameLong":"latimes","homePageUrl":"http:\/\/www.latimes.com\/classified\/realestate\/","createDateTime":"2008-07-25T00:00:00-05:00","updateDateTime":"2011-02-16T00:00:00-06:00","id":137}]},"status":{"code":200,"errorStack":null}}';
        $json = new JSON();
        $decode = $json->decode($response, false, false);
        $this->assertNotEmpty($decode->data->affiliates[0]->profileName, "Did not decode correctly");
    }
}
