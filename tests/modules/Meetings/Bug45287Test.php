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


require_once 'modules/Accounts/Account.php';
require_once 'modules/Meetings/Meeting.php';
require_once 'include/SearchForm/SearchForm2.php';


class Bug45287Test extends Sugar_PHPUnit_Framework_TestCase
{
    var $meetingsArr;
    var $searchDefs;
    var $searchFields;
    var $timedate;
    
    public function setup()
    {
        global $current_user;
        // Create Anon User setted on GMT+2 TimeZone
        $current_user = SugarTestUserUtilities::createAnonymousUser();
        $current_user->setPreference('datef', "d/m/Y");
        $current_user->setPreference('timef', "H:i:s");
        $current_user->setPreference('timezone', "Europe/Rome");

        // new object to avoid TZ caching
        $this->timedate = new TimeDate();

        $this->meetingsArr = array();

        // Create a Bunch of Meetings
        $d = 12;
        $cnt = 0;
        while ($d < 15)
        {
          $this->meetingsArr[$cnt] = new Meeting();
          $this->meetingsArr[$cnt]->name = 'Bug45287 Meeting ' . ($cnt + 1);
          $this->meetingsArr[$cnt]->date_start = $this->timedate->to_display_date_time(gmdate("Y-m-d H:i:s", mktime(10+$cnt, 30, 00, 7, $d, 2011)));
          $this->meetingsArr[$cnt]->save();
          $d++;
          $cnt++;
        }

        $this->searchDefs = array("Meetings" => array("layout" => array("basic_search" => array("name" => array("name" => "name",
                                                                                                                "default" => true,
                                                                                                                "width" => "10%",
                                                                                                               ),
                                                                                                "date_start" => array("name" => "date_start",
                                                                                                                      "default" => true,
                                                                                                                      "width" => "10%",
                                                                                                                      "type" => "datetimecombo",
                                                                                                                     ), 
                                                                                               ),
                                                                       ),
                                                     ),
                                 );

        $this->searchFields = array("Meetings" => array("name" => array("query_type" => "default"),
                                                        "date_start" => array("query_type" => "default"),
                                                        "range_date_start" => array("query_type" => "default",
                                                                                    "enable_range_search" => 1,
                                                                                    "is_date_field" => 1),
                                                        "range_date_start" => array("query_type" => "default",
                                                                                    "enable_range_search" => 1,
                                                                                    "is_date_field" => 1),
                                                        "start_range_date_start" => array("query_type" => "default",
                                                                                          "enable_range_search" => 1,
                                                                                          "is_date_field" => 1),
                                                        "end_range_date_start" => array("query_type" => "default",
                                                                                        "enable_range_search" => 1,
                                                                                        "is_date_field" => 1),
                                                       ),
                                   );
    }
    
    public function tearDown()
    {

        foreach ($this->meetingsArr as $m)
        {
            $GLOBALS['db']->query('DELETE FROM meetings WHERE id = \'' . $m->id . '\' ');
        }

        unset($m);
        unset($this->meetingsArr);
        unset($this->searchDefs);
        unset($this->searchFields);
        unset($this->timedate);

        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
    }
	
    
    public function testRetrieveByExactDate()
    {
        global $current_user;

        $_REQUEST = $_POST = array("module" => "Meetings",
                                   "action" => "index",
                                   "searchFormTab" => "basic_search",
                                   "query" => "true",
                                   "name_basic" => "",
                                   "current_user_only_basic" => "0",
                                   "favorites_only_basic" => "0",
                                   "open_only_basic" => "0",
                                   "date_start_basic_range_choice" => "=",
                                   "range_date_start_basic" => "14/07/2011",
                                   "start_range_date_start_basic" => "",
                                   "end_range_date_start_basic" => "", 
                                   "button" => "Search",
                                  );

        $srch = new SearchForm(new Meeting(), "Meetings");
        $srch->setup($this->searchDefs, $this->searchFields, "");
        $srch->populateFromRequest();
        $w = $srch->generateSearchWhere();

        // Due to daylight savings, I cannot hardcode intervals...
        $GMTDates = $this->timedate->getDayStartEndGMT("2011-07-14");

        // Current User is on GMT+2.
        // Asking for meeting of 14 July 2011, I expect to search (GMT) from 13 July at 22:00 until 14 July at 22:00 (excluded)
        $expectedWhere = "meetings.date_start >= '" . $GMTDates['start'] . "' AND meetings.date_start <= '" . $GMTDates['end'] . "'";
        $this->assertEquals($w[0], $expectedWhere);
    }
	

    public function testRetrieveByDaterange()
    {
        global $current_user;

        $_REQUEST = $_POST = array("module" => "Meetings",
                                   "action" => "index",
                                   "searchFormTab" => "basic_search",
                                   "query" => "true",
                                   "name_basic" => "",
                                   "current_user_only_basic" => "0",
                                   "favorites_only_basic" => "0",
                                   "open_only_basic" => "0",
                                   "date_start_basic_range_choice" => "between",
                                   "range_date_start_basic" => "",
                                   "start_range_date_start_basic" => "13/07/2011",
                                   "end_range_date_start_basic" => "14/07/2011", 
                                   "button" => "Search",
                                  );


        $srch = new SearchForm(new Meeting(), "Meetings");
        $srch->setup($this->searchDefs, $this->searchFields, "");
        $srch->populateFromRequest();
        $w = $srch->generateSearchWhere();

        // Due to daylight savings, I cannot hardcode intervals...
        $GMTDatesStart = $this->timedate->getDayStartEndGMT("2011-07-13");
        $GMTDatesEnd = $this->timedate->getDayStartEndGMT("2011-07-14");

        // Current User is on GMT+2.
        // Asking for meeting between 13 and 14 July 2011, I expect to search (GMT) from 12 July at 22:00 until 14 July at 22:00 (excluded)
        $expectedWhere = "meetings.date_start >= '" . $GMTDatesStart['start'] . "' AND meetings.date_start <= '" . $GMTDatesEnd['end'] . "'";
        $this->assertEquals($w[0], $expectedWhere);
   }
	

}
