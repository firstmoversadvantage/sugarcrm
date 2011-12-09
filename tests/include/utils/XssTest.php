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

 
require_once 'include/utils.php';

class XssTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function xssData()
    {
        return array(
            array("some data", "some data"),

            array("test <a href=\"http://www.digitalbrandexpressions.com\">link</a>", "test <a href=\"http://www.digitalbrandexpressions.com\">link</a>"),
            array("some data<script>alert('xss!')</script>", "some data<>alert('xss!')</>"),
            array("some data<script src=\" http://localhost/xss.js\"></script>", "some data< src=\" http://localhost/xss.js\"></>"),
            array("some data<applet></applet><script src=\" http://localhost/xss.js\"></script>", "some data<></>< src=\" http://localhost/xss.js\"></>"),
            array('some data before<img alt="<script>" src="http://www.symbolset.org/images/peace-sign-2.jpg"; onload="alert(35)" width="1" height="1"/>some data after', 'some data before<img alt="<>" src="http://www.symbolset.org/images/peace-sign-2.jpg"; ="alert(35)" width="1" height="1"/>some data after'),
            array('some data before<img src="http://www.symbolset.org/images/peace-sign-2.jpg"; onload="alert(35)" width="1" height="1"/>some data after', 'some data before<img src="http://www.symbolset.org/images/peace-sign-2.jpg"; ="alert(35)" width="1" height="1"/>some data after'),
            array('some data before<img src="http://www.symbolset.org/images/peace-sign-2.jpg"; width="1" height="1"/>some data after', 'some data before<img src="http://www.symbolset.org/images/peace-sign-2.jpg"; width="1" height="1"/>some data after'),
            array('<div style="font-family:Calibri;">Roger Smith</div>', '<div style="font-family:Calibri;">Roger Smith</div>'),
            array('some data before<img onmouseover onload onmouseover=\'alert(8)\' src="http://www.docspopuli.org/images/Symbol.jpg";\'/>some data after', 'some data before<img   =\'alert(8)\' src="http://www.docspopuli.org/images/Symbol.jpg";\'/>some data after'),
                        
            );
    }

    protected function clean($str) {
        $potentials = clean_xss($str, false);
        if(is_array($potentials) && !empty($potentials)) {
             foreach($potentials as $bad) {
                 $str = str_replace($bad, "", $str);
             }
        }
        return $str;
    }

    /**
     * @dataProvider xssData
     */
    public function testXssFilter($before, $after)
    {
        $this->assertEquals($after, $this->clean($before));
    }

    /**
     * @dataProvider xssData
     */
    public function testXssFilterBean($before, $after)
    {
        $bean = new EmailTemplate();
		$bean->body_html = to_html($before);
        $bean->cleanBean();
        $this->assertEquals(to_html($after), $bean->body_html);
    }
}
