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


require_once('tests/service/APIv3Helper.php');
require_once 'include/SugarOAuthServer.php';

class OAuthTest extends Sugar_PHPUnit_Framework_TestCase
{
    protected static $_user;
    protected static $_consumer;
    protected $_admin_user;
    protected $_lastRawResponse;

    private static $helperObject;

    protected $aclRole;
    protected $aclField;

    public static function setUpBeforeClass()
    {
        $beanList = array();
		$beanFiles = array();
		require('include/modules.php');
		$GLOBALS['beanList'] = $beanList;
		$GLOBALS['beanFiles'] = $beanFiles;

        //Reload langauge strings
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
        $GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], 'Accounts');
        //Create an anonymous user for login purposes/
        $GLOBALS['current_user'] = self::$_user = SugarTestUserUtilities::createAnonymousUser();

        self::$helperObject = new APIv3Helper();
        // create our own customer key
        $GLOBALS['db']->query("DELETE FROM oauth_consumer where c_key='TESTCUSTOMER'");
	    $GLOBALS['db']->query("DELETE FROM oauth_nonce where conskey='TESTCUSTOMER'");
        self::$_consumer = new OAuthKey();
	    self::$_consumer->c_key = "TESTCUSTOMER";
        self::$_consumer->c_secret = "TESTSECRET";
        self::$_consumer->save();
    }

    public static function tearDownAfterClass()
	{
	    unset($GLOBALS['beanList']);
		unset($GLOBALS['beanFiles']);
		unset($GLOBALS['app_list_strings']);
	    unset($GLOBALS['app_strings']);
	    unset($GLOBALS['mod_strings']);
	    unset($GLOBALS['current_user']);
	    $GLOBALS['db']->query("DELETE FROM oauth_consumer where c_key='TESTCUSTOMER'");
	    $GLOBALS['db']->query("DELETE FROM oauth_nonce where conskey='TESTCUSTOMER'");
	    $GLOBALS['db']->query("DELETE FROM oauth_tokens where consumer='".self::$_consumer->id."'");
	    SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
	}

	public function setUp()
	{
	    if(!SugarOAuthServer::enabled() || !extension_loaded('oauth')) {
            $this->markTestSkipped("No OAuth support");
        }
        $this->oauth = new OAuth('TESTCUSTOMER','TESTSECRET',OAUTH_SIG_METHOD_HMACSHA1,OAUTH_AUTH_TYPE_URI);
        $this->url = $GLOBALS['sugar_config']['site_url'].'service/v4/rest.php';
	    $GLOBALS['current_user'] = self::$_user;
	}

    protected function _returnLastRawResponse()
    {
        return "Error in web services call. Response was: {$this->_lastRawResponse}";
    }

    public function testOauthRequestToken()
    {
        $request_token_info = $this->oauth->getRequestToken($this->url."?method=oauth_request_token");
        $this->assertEquals($GLOBALS['sugar_config']['site_url'].'index.php?module=OAuthTokens&action=authorize', $request_token_info["authorize_url"]);
        $this->assertEquals("true", $request_token_info["oauth_callback_confirmed"]);
        $this->assertNotEmpty($request_token_info['oauth_token']);
        $this->assertNotEmpty($request_token_info['oauth_token_secret']);
        $rtoken = OAuthToken::load($request_token_info['oauth_token']);
        $this->assertInstanceOf('OAuthToken', $rtoken);
        $this->assertEquals(OAuthToken::REQUEST, $rtoken->tstate);
    }

    public function testOauthAccessToken()
    {
        global $current_user;
        $request_token_info = $this->oauth->getRequestToken($this->url."?method=oauth_request_token");
        $this->assertNotEmpty($request_token_info['oauth_token']);
        $this->assertNotEmpty($request_token_info['oauth_token_secret']);
        $token = $request_token_info['oauth_token'];
        $secret = $request_token_info['oauth_token_secret'];

        $c_token = OAuthToken::load($token);
        $this->assertInstanceOf('OAuthToken', $c_token);
        // check token is in the right state
        $this->assertEquals(OAuthToken::REQUEST, $c_token->tstate, "Request token has wrong state");
        $verify = $c_token->authorize(array("user" => $current_user->id));

        $this->oauth->setToken($token, $secret);
        $access_token_info = $this->oauth->getAccessToken($this->url."?method=oauth_access_token&oauth_verifier=$verify");
        $this->assertNotEmpty($access_token_info['oauth_token']);
        $this->assertNotEmpty($access_token_info['oauth_token_secret']);

        $atoken = OAuthToken::load($access_token_info['oauth_token']);
        $this->assertInstanceOf('OAuthToken', $atoken);
        $this->assertEquals($current_user->id, $atoken->assigned_user_id);
        // check this is an access token
        $this->assertEquals(OAuthToken::ACCESS, $atoken->tstate, "Access token has wrong state");
        // check old token was invalidated
        $rtoken = OAuthToken::load($token);
        $this->assertInstanceOf('OAuthToken', $rtoken);
        $this->assertEquals(OAuthToken::INVALID, $rtoken->tstate, "Request token was not invalidated");
    }

    protected function _makeRESTCall($method,$parameters)
    {
        // specify the REST web service to interact with
        $url = $GLOBALS['sugar_config']['site_url'].'/service/v4/rest.php';
        // Open a curl session for making the call
        $curl = curl_init($url);
        // set URL and other appropriate options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
        // build the request URL
        $json = json_encode($parameters);
        $postArgs = "method=$method&input_type=JSON&response_type=JSON&rest_data=$json";
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
        // Make the REST call, returning the result
        $response = curl_exec($curl);
        // Close the connection
        curl_close($curl);

        $this->_lastRawResponse = $response;

        // Convert the result from JSON format to a PHP array
        return json_decode($response,true);
    }

    public function testOauthServiceAccess()
    {
        global $current_user;
        $request_token_info = $this->oauth->getRequestToken($this->url."?method=oauth_request_token");
        $token = $request_token_info['oauth_token'];
        $secret = $request_token_info['oauth_token_secret'];

        $c_token = OAuthToken::load($token);
        $verify = $c_token->authorize(array("user" => $current_user->id));

        $this->oauth->setToken($token, $secret);
        $access_token_info = $this->oauth->getAccessToken($this->url."?method=oauth_access_token&oauth_verifier=$verify");
        $token = $access_token_info['oauth_token'];
        $secret = $access_token_info['oauth_token_secret'];
        $this->oauth->setToken($token, $secret);

        $res = $this->oauth->fetch($this->url."?method=oauth_access&input_type=JSON&response_type=JSON");
        $this->assertTrue($res);
        $session = json_decode($this->oauth->getLastResponse(), true);
        $this->assertNotEmpty($session["id"]);

        // test fetch through OAuth
        $res = $this->oauth->fetch($this->url."?method=get_user_id&input_type=JSON&response_type=JSON");
        $this->assertTrue($res);
        $id = json_decode($this->oauth->getLastResponse(), true);
        $this->assertEquals($current_user->id, $id);
        // test fetch through session initiated by OAuth
        $id2 = $this->_makeRESTCall('get_user_id', array("session" => $session["id"]));
        $this->assertEquals($current_user->id, $id2);
    }
}