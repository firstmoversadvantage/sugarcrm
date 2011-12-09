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

 
require_once('modules/InboundEmail/InboundEmail.php');

/**
 * @ticket 43554
 */
class Bug43554Test extends Sugar_PHPUnit_Framework_TestCase
{

	static $ie = null;
    static $_user = null;

	static public function setUpBeforeClass()
    {
        self::$_user = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['current_user'] = self::$_user;

		self::$ie = new InboundEmail();
	}

    static public function tearDownAfterClass()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }

    public function getUrls()
    {
        return array(
            array("http://localhost:8888/sugarent/index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array("http://localhost:8888/index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array(to_html("http://localhost:8888/index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1")),
            array("/index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array("index.php?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array("/?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            array("https://localhost/?composeLayoutId=composeLayout1&fromAccount=1&module=Emails&action=EmailUIAjax&emailUIAction=sendEmail&setEditor=1"),
            );
    }

    /**
     * @dataProvider getUrls
     * @param string $url
     */
	function testEmailCleanup($url)
	{
        $data = "Test: <img src=\"$url\">";
        $res = str_replace("<img />", "", self::$ie->cleanContent($data));
        $this->assertNotContains("<img", $res);
	}
}