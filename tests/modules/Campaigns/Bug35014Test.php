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

 
class Bug35014Test extends Sugar_PHPUnit_Framework_TestCase
{
	private $campaign_id;
	
	public function setUp()
    {

        $this->markTestSkipped('SugarTestCampaignUtilities does not exist');
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $campaign = SugarTestCampaignUtilities::createCampaign();
        $this->campaign_id = $campaign->id;
	}

    public function tearDown()
    {
        //SugarTestCampaignUtilities::removeAllCreatedCampaigns();
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    public function testLeadCaptureResponse()
    {
        // SET GLOBAL PHP VARIABLES
        $_POST = array
        (
            'first_name' => 'Sadek',
            'last_name' => 'Baroudi',
            'campaign_id' => $this->campaign_id,
            'redirect_url' => 'http://www.sugarcrm.com/index.php',
            'assigned_user_id' => 1,
            'team_id' => '1',
            'team_set_id' => 'Global',
            'req_id' => 'last_name;',
        );
        
        // RUN TEST 1
        $postString = '';
        foreach($_POST as $k => $v)
        {
            $postString .= "{$k}=".urlencode($v)."&";
        }
        $postString = rtrim($postString, "&");
        
        $ch = curl_init("{$GLOBALS['sugar_config']['site_url']}/index.php?entryPoint=WebToLeadCapture");
        curl_setopt($ch, CURLOPT_POST, count($_POST) + 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        ob_start();
        $return = curl_exec($ch);
        $output = ob_get_clean();
        
        $matches = array();
        preg_match("/Location: .*/", $output, $matches);
        $this->assertTrue(count($matches) > 0, "Could not get the header information for the response");
        
        $location = '';
        if(count($matches) > 0){
            $location = str_replace("Location :", "", $matches[0]);
        }
        
        $query_string = substr($location, strpos($location, "?") + 1);
        $query_string_array = explode("&", $query_string);
        
        $post_compare_array = array();
        $skipKeys = array('module', 'action', 'entryPoint', 'client_id_address');
        foreach($query_string_array as $key_val)
        {
            $key_val_array = explode("=", $key_val);
            if(in_array($key_val_array[0], $skipKeys))
                continue;
            $post_compare_array[$key_val_array[0]] = $key_val_array[1];
        }
        
        // the redirect_url doesn't get returned, so we unset it
        unset($_POST['redirect_url']);
        
        $this->assertEquals($_POST, $post_compare_array, "The returned get location doesn't match that of the post passed in");
        
        
        // SET GLOBAL PHP VARIABLES
        $_POST = array
        (
            'first_name' => 'Sadek',
            'last_name' => 'Baroudi',
            'campaign_id' => $this->campaign_id,
            'redirect_url' => 'http://www.sugarcrm.com/index.php',
            'assigned_user_id' => 1,
            'team_id' => '1',
            'team_set_id' => 'Global',
            'req_id' => 'last_name;',
            'SuperLongGetVar' => 
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
            	'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis'.
        		'PneumonoultramicroscopicsilicovolcanoconiosisPneumonoultramicroscopicsilicovolcanoconiosis',
        );
        
        
        // RUN TEST 1
        $postString = '';
        foreach($_POST as $k => $v)
        {
            $postString .= "{$k}=".urlencode($v)."&";
        }
        $postString = rtrim($postString, "&");
        
        $ch = curl_init("{$GLOBALS['sugar_config']['site_url']}/index.php?entryPoint=WebToLeadCapture");
        curl_setopt($ch, CURLOPT_POST, count($_POST) + 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        ob_start();
        $return = curl_exec($ch);
        $output = ob_get_clean();
        
        $matches = array();
        preg_match('/form name="redirect"/', $output, $matches);
        $this->assertTrue(count($matches) > 0, "Should have output a form since we have a long get string");
    }
}
?>