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

class CustomSugarChartFactoryTest extends Sugar_PHPUnit_Framework_TestCase {

public static function setUpBeforeClass()
{
    $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
}

public static function tearDownAfterClass()
{
    SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
    unset($GLOBALS['current_user']);
}


public function setUp()
{

mkdir_recursive('custom/include/SugarCharts/CustomSugarChartFactory');
	
$the_string = <<<EOQ
<?php

require_once("include/SugarCharts/JsChart.php");

class CustomSugarChartFactory extends JsChart {
	
	function __construct() {
		parent::__construct();
	}
	
	function getChartResources() {
		return '
		<link type="text/css" href="'.getJSPath('include/SugarCharts/Jit/css/base.css').'" rel="stylesheet" />
		<!--[if IE]><script language="javascript" type="text/javascript" src="'.getJSPath('include/SugarCharts/Jit/js/Jit/Extras/excanvas.js').'"></script><![endif]-->
		<script language="javascript" type="text/javascript" src="'.getJSPath('include/SugarCharts/Jit/js/Jit/jit.js').'"></script>
		<script language="javascript" type="text/javascript" src="'.getJSPath('include/SugarCharts/Jit/js/sugarCharts.js').'"></script>
		';
	}
	
	function getMySugarChartResources() {
		return '
		<script language="javascript" type="text/javascript" src="'.getJSPath('include/SugarCharts/Jit/js/mySugarCharts.js').'"></script>
		';
	}
	

	function display(\$name, \$xmlFile, \$width='320', \$height='480', \$resize=false) {
	
		parent::display(\$name, \$xmlFile, \$width, \$height, \$resize);

		return \$this->ss->fetch('include/SugarCharts/Jit/tpls/chart.tpl');	
	}
	

	function getDashletScript(\$id,\$xmlFile="") {
		
		parent::getDashletScript(\$id,\$xmlFile);
		return \$this->ss->fetch('include/SugarCharts/Jit/tpls/DashletGenericChartScript.tpl');
	}

}

?>
EOQ;

$fp = sugar_fopen('custom/include/SugarCharts/CustomSugarChartFactory/CustomSugarChartFactory.php', "w");
fwrite($fp, $the_string );
fclose($fp );

}

public function tearDown()
{
	rmdir_recursive('custom/include/SugarCharts/CustomSugarChartFactory');
}


public function testCustomFactory()
{
	$sugarChart = SugarChartFactory::getInstance('CustomSugarChartFactory');
	$name = get_class($sugarChart);
	$this->assertEquals('CustomSugarChartFactory', $name, 'Assert engine is CustomSugarChartFactory');
}

}
