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


 
require_once 'modules/Import/CsvAutoDetect.php';

class CsvAutoDetectTest extends Sugar_PHPUnit_Framework_TestCase
{
    private static $CsvContent = array (
        0 => "\"date_entered\",\"description\"\n\"3/26/2011 10:02am\",\"test description\"",
        1 => "\"date_entered\"\t\"description\"\n\"2011-3-26 10:2 am\"\t\"test description\"",
        2 => "\"date_entered\",\"description\"\n\"3.26.2011 15.02\",\"test description\"",
        3 => "\"3/26/2011 10:02am\",\"some text\"\n\"4/26/2011 11:20am\",\"some more jim's text\"",
        4 => "\"date_entered\",\"description\"\n\"2010/03/26 10:2am\",\"test description\"",
        5 => "'date_entered','description'\n'26/3/2011 15:02','test description'",
        6 => "\"date_entered\"|\"description\"\n\"3/26/2011 10:02am\"|\"test description\"",
    );

    public function setUp()
    {
        // if beanList got unset, set it back
        if (!isset($GLOBALS['beanList'])) {
            require('include/modules.php');
            $GLOBALS['beanList'] = $beanList;
        }
    }

    public function tearDown()
    {
    }

    public function providerCsvData()
    {
        return array(
            array(0, ',', '"', 'm/d/Y', 'h:ia', true),
            array(1, "\t", '"', 'Y-m-d', 'h:i a', true),
            array(2, ",", '"', 'm.d.Y', 'H.i', true),
            array(3, ',', '"', 'm/d/Y', 'h:ia', false),
            array(4, ',', '"', 'Y/m/d', 'h:ia', true),
            array(5, ',', "'", 'd/m/Y', 'H:i', true),
            array(6, '|', '"', 'm/d/Y', 'h:ia', true),
            );
    }

    /**
     * @dataProvider providerCsvData
     */
    public function testGetCsvProperties($content_idx, $delimiter, $enclosure, $date, $time, $header)
    {
        $file = $GLOBALS['sugar_config']['tmp_dir'].'test.csv';
        $ret = file_put_contents($file, self::$CsvContent[$content_idx]);
        $this->assertGreaterThan(0, $ret, 'Failed to write to '.$file .' for content '.$content_idx);

        $auto = new CsvAutoDetect($file);
        $del = $enc = $hasHeader = false;
        $ret = $auto->getCsvSettings($del, $enc);
        $this->assertEquals(true, $ret, 'Failed to parse and get csv properties');

        // delimiter
        $this->assertEquals($delimiter, $del, 'Incorrect delimiter');

        // enclosure
        $this->assertEquals($enclosure, $enc, 'Incorrect enclosure');

        // date format
        $date_format = $auto->getDateFormat();
        $this->assertEquals($date, $date_format, 'Incorrect date format');

        // time format
        $time_format = $auto->getTimeFormat();
        $this->assertEquals($time, $time_format, 'Incorrect time format');

        // header
        $ret = $auto->hasHeader($hasHeader, 'Contacts');
        $this->assertTrue($ret, 'Failed to detect header');
        $this->assertEquals($header, $hasHeader, 'Incorrect header');

        // remove temp file
        unlink($GLOBALS['sugar_config']['tmp_dir'].'test.csv');
    }

}
