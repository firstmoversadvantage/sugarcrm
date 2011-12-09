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

require_once('include/SugarCharts/SugarChart.php');
/**
 * Created: Sep 28, 2011
 */
class Bug44696Test extends Sugar_PHPUnit_Framework_TestCase
{
    public $sugarChartObject;
    
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $sugarChartObject = new SugarChart();
        $sugarChartObject->group_by = array ('sales_stage', 'user_name');
        $sugarChartObject->data_set = $this->getDataSet();
        $sugarChartObject->base_url = array ('module' => 'Opportunities',
                                                                                'action' => 'index',
                                                                                'query' => 'true',
                                                                                'searchFormTab' => 'advanced_search');
        $sugarChartObject->url_params = array ();
        $sugarChartObject->is_currency = true;
        // we have 5 users 
        $sugarChartObject->super_set = array ('will', 'max', 'sarah', 'sally', 'chris');
        $this->sugarChartObject = $sugarChartObject;
    }

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    public function getDataSet() {
        return array (
           array (
                'sales_stage' => 'Proposal/Price Quote',
               'user_name' => 'max',
               'assigned_user_id' => 'seed_max_id',
               'opp_count' => '1',
               'total' => '50',
               'key' => 'Proposal/Price Quote',
               'value' => 'Proposal/Price Quote',
               ), 
            array (
                'sales_stage' => 'Proposal/Price Quote',
               'user_name' => 'sally',
               'assigned_user_id' => 'seed_sally_id',
               'opp_count' => '2',
               'total' => '75',
               'key' => 'Proposal/Price Quote',
               'value' => 'Proposal/Price Quote',
               ),
        );
    }
    
    /**
     * We check, that groups with NULL value remain their order in subgroups tag and won't fall down under not null valued groups. 
     * This way we guarantee that links will be put correctly to each user in whole user list (will, max, etc.). 
     */
    public function testCorrectXml() 
    {
        $actual = $this->sugarChartObject->xmlDataGenericChart();
        $expected = $this->compareXml();
        $order   = array("\r\n", "\n", "\r", "\t");
        $replace = "";
        // remove all break lines and spaces and tabs
            $expected = str_replace($order, $replace, $expected);
            $actual = str_replace($order, $replace, $actual);
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @return xml string  
     */
    public function compareXml() 
    {
        $max = 50;
        $sally = 75;
        $total = $max + $sally;
        $max = $this->convertCurrency($max);
        $sally = $this->convertCurrency($sally);
        $total = $this->convertCurrency($total);
        
        return "<group>
			<title>Proposal/Price Quote</title>
			<value>{$total->subAmount}</value>
			<label>{$total->subAmountFormatted}</label>
			<link>index.php?module=Opportunities&action=index&query=true&searchFormTab=advanced_search&sales_stage=Proposal%2FPrice+Quote</link>
			<subgroups>
				<group>
					<title>will</title>
					<value>NULL</value>
					<label></label>
					<link>index.php?module=Opportunities&action=index&query=true&searchFormTab=advanced_search&sales_stage=Proposal%2FPrice+Quote</link>
				</group>
				<group>
					<title>max</title>
					<value>{$max->subAmount}</value>
					<label>{$max->subAmountFormatted}</label>
					<link>index.php?module=Opportunities&action=index&query=true&searchFormTab=advanced_search&sales_stage=Proposal%2FPrice+Quote&assigned_user_id[]=seed_max_id</link>
				</group>
				<group>
					<title>sarah</title>
					<value>NULL</value>
					<label></label>
					<link>index.php?module=Opportunities&action=index&query=true&searchFormTab=advanced_search&sales_stage=Proposal%2FPrice+Quote</link>
				</group>
				<group>
					<title>sally</title>
					<value>{$sally->subAmount}</value>
					<label>{$sally->subAmountFormatted}</label>
					<link>index.php?module=Opportunities&action=index&query=true&searchFormTab=advanced_search&sales_stage=Proposal%2FPrice+Quote&assigned_user_id[]=seed_sally_id</link>
				</group>
				<group>
					<title>chris</title>
					<value>NULL</value>
					<label></label>
					<link>index.php?module=Opportunities&action=index&query=true&searchFormTab=advanced_search&sales_stage=Proposal%2FPrice+Quote</link>
				</group>
			</subgroups></group>";
    }
    
    public function convertCurrency($value) {
        $sub_amount = $this->sugarChartObject->formatNumber($this->sugarChartObject->convertCurrency($value));
        $sub_amount_formatted = $this->sugarChartObject->currency_symbol . $sub_amount . 'K';
        $sub_amount = $this->sugarChartObject->convertCurrency($value);
        $return = new stdClass();
        $return->subAmount = $sub_amount;
        $return->subAmountFormatted = $sub_amount_formatted;
        return $return;
    }
}