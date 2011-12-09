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


 
require_once 'modules/Import/Importer.php';
require_once 'modules/Import/sources/ImportFile.php';

class ImporterTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_importModule;
    private $_importObject;

    // date_entered and last_name
    private static $CsvContent = array (
        0 => "\"3/26/2011 10:02am\",\"Doe\"",
        1 => "\"2011-3-26 10:2 am\",\"Doe\"",
        2 => "\"3.26.2011 10.02\",\"Doe\"",
    );

    public function setUp()
    {
        $beanList = array();
        $beanFiles = array();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $this->_importModule = 'Contacts';
        $this->_importObject = 'Contact';
    }
    
    public function tearDown() 
    {
        $GLOBALS['db']->query("DELETE FROM contacts where created_by='{$GLOBALS['current_user']->id}'");

        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
    }
    
    public function providerCsvData()
    {
        return array(
            array(0, '2011-03-26 10:02:00', 'm/d/Y', 'h:ia'),
            array(1, '2011-03-26 10:02:00', 'Y-m-d', 'h:ia'),
            array(2, '2011-03-26 10:02:00', 'm.d.Y', 'H.i'),
            );
    }

    /**
     * @dataProvider providerCsvData
     */
    public function testDateTimeImport($content_idx, $expected_datetime, $date_format, $time_format)
    {
        $file = $GLOBALS['sugar_config']['upload_dir'].'test.csv';
        $ret = file_put_contents($file, self::$CsvContent[$content_idx]);
        $this->assertGreaterThan(0, $ret, 'Failed to write to '.$file .' for content '.$content_idx);

        $importSource = new ImportFile($file, ',', '"');

        $bean = loadBean($this->_importModule);

        $_REQUEST['columncount'] = 2;
        $_REQUEST['colnum_0'] = 'date_entered';
        $_REQUEST['colnum_1'] = 'last_name';
        $_REQUEST['import_module'] = 'Contacts';
        $_REQUEST['importlocale_charset'] = 'UTF-8';
        $_REQUEST['importlocale_dateformat'] = $date_format;
        $_REQUEST['importlocale_timeformat'] = $time_format;
        $_REQUEST['importlocale_timezone'] = 'GMT';
        $_REQUEST['importlocale_default_currency_significant_digits'] = '2';
        $_REQUEST['importlocale_currency'] = '-99';
        $_REQUEST['importlocale_dec_sep'] = '.';
        $_REQUEST['importlocale_currency'] = '-99';
        $_REQUEST['importlocale_default_locale_name_format'] = 's f l';
        $_REQUEST['importlocale_num_grp_sep'] = ',';

        $importer = new Importer($importSource, $bean);
        $importer->import();

        $query = "SELECT date_entered from contacts where created_by='{$GLOBALS['current_user']->id}'";
        $result = $GLOBALS['db']->query($query);
        $row = $GLOBALS['db']->fetchByAssoc($result);

        $this->assertEquals($expected_datetime, $row['date_entered'], 'Got incorrect date_entered.');

    }
}
    
