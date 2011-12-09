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


require_once('include/SugarCharts/SugarChartFactory.php');

class Bug42326Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $sugarChart;

	public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->sugarChart = SugarChartFactory::getInstance('Jit', 'Reports');
    }

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }

    /**
     * @dataProvider xmlDataBuilder
     */
    public function testStackedBarChartHasCorrectLabelJSON($xmldata, $expectedjson) {
        $json = $this->sugarChart->buildLabelsBarChart($xmldata);
        $this->assertSame($expectedjson, $json);
    }

    public function xmlDataBuilder() {
        $dataset = array(
            // check labels for regression of normal bar chart
            array('<?xml version="1.0" encoding="UTF-8"?><sugarcharts version="1.0"><data><group><title>Label1</title><value>4</value><label>4</label><subgroups></subgroups></group><group><title>Label2</title><value>3</value><label>3</label><subgroups></subgroups></group></data></sugarcharts>',
                  "\t'label': [\n\n\t\t'Label1'\n,\n\t\t'Label2'\n\n\t],\n\n",),

            // check labels on stacked bar chart generate correct JSON
            // before the fix, this would have resulted in "\t'label': [\n\n\t\t'Name1'\n],\n\n"
            array('﻿<?xml version="1.0" encoding="UTF-8"?><sugarcharts version="1.0"><data><group><title>Name1</title><value>1</value><label>1</label><subgroups><group><title>Label1</title><value>1</value><label>1</label><link></link></group><group><title>Label2</title><value>NULL</value><label></label><link></link></group></subgroups></group></data></sugarcharts>',
                  "\t'label': [\n\n\t\t'Label1'\n,\n\t\t'Label2'\n\n\t],\n\n"),
        );
        return $dataset;
    }
}

?>