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



/**
 * @brief Try to test download.php for php notices
 * @ticket 45896
 */
class Bug45896Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $curl = null;
    private $sessionName = '';
    private $sessionId = '';
    private $backup = array();
    private $user = null;

    /**
     * @brief Here we create valid session for anonymous user
     * @return void
     */
    public function setUp()
    {
        $this->backup['session.use_cookies'] = ini_get('session.use_cookies');
        ini_set('session.use_cookies', false);
        $this->backup['session.use_only_cookies'] = ini_get('session.use_only_cookies');
        ini_set('session.use_only_cookies', false);

        $this->user = SugarTestUserUtilities::createAnonymousUser();

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $GLOBALS['sugar_config']['site_url']);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_NOBODY, true);
        $headers = curl_exec($this->curl);
        $error = curl_errno($this->curl);
        if ($error != 0)
        {
            $this->fail('Curl returns incorrect code');
        }

        $headers = explode("\r\n", trim($headers));
        foreach ($headers as $header)
        {
            $header = explode(': ', $header, 2);
            if ($header[0] == 'Set-Cookie')
            {
                $header[1] = explode('; ', $header[1]);
                $header = reset($header[1]);
                $header = explode('=', $header, 2);
                $this->sessionName = $header[0];
                $this->sessionId = $header[1];
            }
        }

        session_id($this->sessionId);
        @session_start();
        $_SESSION['authenticated_user_id'] = $this->user->id;
        $_SESSION['authenticated_user_language'] = $GLOBALS['sugar_config']['default_language'];
        $_SESSION['unique_key'] = $GLOBALS['sugar_config']['unique_key'];
        if ($_SESSION['unique_key'] == false)
        {
            $this->fail('You must set unique_key value in config.php');
        }
        session_write_close();
    }

    /**
     * @brief query strings for download random file
     * @return array
     */
    public function getQueryString()
    {
        return array(
            array('entryPoint=download&id=643da5f0-513c-0933-5222-4e521fc84036&type=SugarFieldImage&isTempFile=1'),
            array('entryPoint=download&id=WeShouldTryToDownloadIncorrectFile&type=SugarFieldImage&isTempFile=1'),
            array('entryPoint=download&id=WeShouldTryToDownloadIncorrectFile&type=SugarFieldImage'),
            array('entryPoint=download&id=WeShouldTryToDownloadIncorrectFile&type=2&isTempFile=1')
        );
    }

	/**
     * @brief try to download files and to check response for notices
	 * @dataProvider getQueryString
     * @group 45896
     * 
	 * @param array $queryString query string to download any file url
	 */
	public function testDownload($queryString)
	{
        $this->markTestSkipped('Need mgusev to fix this test');
        return;
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_NOBODY, false);
        curl_setopt($this->curl, CURLOPT_URL, $GLOBALS['sugar_config']['site_url'].'?'.$queryString);
        curl_setopt(
            $this->curl,
            CURLOPT_COOKIE,
            'ck_login_id_20='.$this->user->id.'; '.
                'ck_login_language_20=en_us; '.
                'ck_login_theme_20=Sugar; '.
                'globalLinksOpen=true; '.
                'sugar_theme_gm_current=All; '.
                'sugar_user_theme=Sugar; '.
                $this->sessionName.'='.$this->sessionId
        );
        $content = curl_exec($this->curl);
        $error = curl_errno($this->curl);
        $stat = curl_getinfo($this->curl);
        if ($error != 0) // need only valid curl result
        {
            $this->fail('Curl returns incorrect code');
        }
        elseif ($stat['http_code'] != 200) // need only success header
        {
            $this->fail('Incorrect HTTP code for test');
        }

        // getting headers
        $content = explode("\r\n\r\n", $content, 2);
        $content[0] = explode("\r\n", $content[0]);
        $headers = array();
        foreach ($content[0] as $header)
        {
            $header = explode(': ', $header, 2);
            if (count($header) != 2)
            {
                continue;
            }
            $headers[strtolower($header[0])] = $header[1];
        }
        $content = $content[1];

        // parse for type of content
        $headers['content-type'] = explode('/', $headers['content-type'], 2);
        $headers['content-type'] = strtolower(reset($headers['content-type']));

        // thinking what image and application type is valid, text is our test place, other types are fail
        switch ($headers['content-type']) {
            case 'image' :
            case 'application' :
                {
                    $this->assertNotEmpty($content, 'Content should be not empty');
                }
                break;
            case 'text' :
                {
                    $this->assertContains(
                        $content,
                        array(
                             'Not a Valid Entry Point',
                             'Error. This type is not valid.',
                             'Invalid File Reference'
                        ),
                        'Got php notice'
                    );
                }
                break;
            default :
                {
                    $this->fail('Received unknown content type');
                }
        }
	}

    /**
     * @brief closing curl connection and restore php.ini parameters
     * @return void
     */
    public function tearDown()
    {
        curl_close($this->curl);
        foreach ($this->backup as $k=>$v)
        {
            ini_set($k, $v);
        }
    }
}
