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

 
require_once('include/externalAPI/Google/ExtAPIGoogle.php');


/**
 * @ticket 43652
 */
class Bug43652Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $fileData1;
    private $extAPI;

    public function setUp()
    {
        //Just need base class but its abstract so we use the google implementation for this test.
        $this->extAPI = new ExtAPIGoogle();
        $this->fileData1 = $GLOBALS['sugar_config']['upload_dir'] . DIRECTORY_SEPARATOR . 'unittest';
        file_put_contents($this->fileData1, "Unit test for mime type");
    }

    public function tearDown()
	{
        unlink($this->fileData1);
	}

    function _fileMimeProvider()
    {
        return array(
            array( array('name' => 'te.st.png','type' => 'img/png'),'img/png'),
            array( array('name' => 'test.jpg','type' => 'img/jpeg'),'img/jpeg'),
            array( array('name' => 'test.out','type' => 'application/octet-stream'),'application/octet-stream'),
            array( array('name' => 'test_again','type' => 'img/png'),'img/png'),
        );
    }

    /**
     * Test the getMime function for the use case where the mime type is already provided.
     *
     * @dataProvider _fileMimeProvider
     */
    public function testUploadFileWithMimeType($file_info, $expectedMime)
    {
        $uf = new UploadFile('');
        $mime = $uf->getMime($file_info);

        $this->assertEquals($expectedMime, $mime);
    }

    /**
     * Test file with no extension but with provided mime-type
     *
     * @return void
     */
    public function testUploadFileWithEmptyFileExtension()
    {
        $file_info = array('name' => 'test', 'type' => 'application/octet-stream', 'tmp_name' => $this->fileData1);
        $expectedMime = $this->extAPI->isMimeDetectionAvailable() ? 'text/plain' : 'application/octet-stream';
        $uf = new UploadFile('');
        $mime = $uf->getMime($file_info);
        $this->assertEquals($expectedMime, $mime);
    }


    /**
     * Test file with no extension and no provided mime-type
     *
     * @return void
     */
    public function testUploadFileWithEmptyFileExtenEmptyMime()
    {
        $file_info = array('name' => 'test','tmp_name' => $this->fileData1);
        $expectedMime = $this->extAPI->isMimeDetectionAvailable() ? 'text/plain' : 'application/octet-stream';
        $uf = new UploadFile('');
        $mime = $uf->getMime($file_info);
        $this->assertEquals($expectedMime, $mime);
    }
}