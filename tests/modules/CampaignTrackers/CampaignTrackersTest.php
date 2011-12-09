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

require_once 'modules/Campaigns/Campaign.php';
require_once 'modules/CampaignTrackers/CampaignTracker.php';

class CampaignTrackersTest extends Sugar_PHPUnit_Framework_TestCase
{
	var $campaign = 'campaignforcamplogunittest';
	var $campaign_tracker;

    
    public function setup()
    {
		global $current_user;	

		$current_user = SugarTestUserUtilities::createAnonymousUser();
		//for the purpose of this test, we need to create a campaign and relate it to a campaign tracker object

		//create campaign
    	$c = new Campaign();
    	$c->name = 'CT test ' . time();
    	$c->campaign_type = 'Email';
    	$c->status = 'Active';
    	$timeDate = new TimeDate();
    	$c->end_date = $timeDate->to_display_date(date('Y')+1 .'-01-01');
    	$c->assigned_id = $current_user->id;
    	$c->team_id = '1';
    	$c->team_set_id = '1';
    	$c->save();		
		$this->campaign = $c;
		
		//create campaign tracker
		$ct = new CampaignTracker();
		$ct->tracker_name ='CampaignTrackerTest' . time();
		$ct->tracker_url = 'sugarcrm.com';
		$ct->campaign_id = $this->campaign->id;
		$ct->save();
		$this->campaign_tracker = $ct;



		
    }
    
    public function tearDown()
    {
		//delete the campaign and campaign tracker
		$GLOBALS['db']->query('DELETE FROM campaign_log WHERE campaign_id = \''.$this->campaign->id.'\' ');
		$GLOBALS['db']->query('DELETE FROM campaign_trkrs WHERE id = \''.$this->campaign_tracker->id.'\' ');
		unset($this->campaign_tracker);
        unset($this->campaign_log );
        unset($current_user);
    }
	

	public function testSave(){
		//save was already performed, so just confirm that the http protocol got added on save
		$this->assertSame('http://sugarcrm.com', $this->campaign_tracker->tracker_url, 'http protocol was not added to campaign_tracker->tracker_url on save');
		
	}
	
	

	public function testFillInAdditionalDetailFields(){
		global $current_user;

		$this->campaign_tracker->fill_in_additional_detail_fields();

		//test that campaign name gets filled in
		$this->assertSame($this->campaign->name, $this->campaign_tracker->campaign_name, 'campaign name was not set properly during function call');

		//test that message url gets filed out
		$this->assertFalse(empty($this->campaign_tracker->message_url), 'message url was not populated correctly during function call');		
	}

	public function testGetSummaryText(){
		//test that tracker name is returned
		$this->assertSame($this->campaign_tracker->tracker_name, $this->campaign_tracker->get_summary_text(), 'campaign tracker name is not set properly in summary text');
		
	}


	

}