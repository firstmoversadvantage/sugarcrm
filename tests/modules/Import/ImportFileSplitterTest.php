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

 
require_once('modules/Import/sources/ImportFile.php');
require_once 'modules/Import/ImportFileSplitter.php';

class ImportFileSplitterTest extends Sugar_PHPUnit_Framework_TestCase
{
    protected $_goodFile;
    protected $_badFile;
    
    public function setUp()
    {
    	$GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
    	$this->_goodFile = SugarTestImportUtilities::createFile();
		$this->_badFile  = $GLOBALS['sugar_config']['import_dir'].'thisfileisntthere'.date("YmdHis");
		$this->_whiteSpaceFile  = SugarTestImportUtilities::createFileWithWhiteSpace();
    }
    
    public function tearDown()
    {
        SugarTestImportUtilities::removeAllCreatedFiles();
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
    }
    
    public function testLoadNonExistantFile()
    {
        $importFileSplitter = new ImportFileSplitter($this->_badFile);
        $this->assertFalse($importFileSplitter->fileExists());
    }
    
    public function testLoadGoodFile()
    {
        $importFileSplitter = new ImportFileSplitter($this->_goodFile);
        $this->assertTrue($importFileSplitter->fileExists());
    }
    
    public function testSplitSourceFile()
    {
        $importFileSplitter = new ImportFileSplitter($this->_goodFile);
        $importFileSplitter->splitSourceFile(',','"');
        
        $this->assertEquals($importFileSplitter->getRecordCount(),2000);
        $this->assertEquals($importFileSplitter->getFileCount(),2);
    }
    
    public function testSplitSourceFileNoEnclosure()
    {
        $importFileSplitter = new ImportFileSplitter($this->_goodFile);
        $importFileSplitter->splitSourceFile(',','');
        
        $this->assertEquals($importFileSplitter->getRecordCount(),2000);
        $this->assertEquals($importFileSplitter->getFileCount(),2);
    }
    
    public function testSplitSourceFileWithHeader()
    {
        $importFileSplitter = new ImportFileSplitter($this->_goodFile);
        $importFileSplitter->splitSourceFile(',','"',true);
        
        $this->assertEquals($importFileSplitter->getRecordCount(),1999);
        $this->assertEquals($importFileSplitter->getFileCount(),2);
    }
    
    public function testSplitSourceFileWithThreshold()
    {
        $importFileSplitter = new ImportFileSplitter($this->_goodFile,500);
        $importFileSplitter->splitSourceFile(',','"');
        
        $this->assertEquals($importFileSplitter->getRecordCount(),2000);
        $this->assertEquals($importFileSplitter->getFileCount(),4);
    }
    
    public function testGetSplitFileName()
    {
        $importFileSplitter = new ImportFileSplitter($this->_goodFile);
        $importFileSplitter->splitSourceFile(',','"');
        
        $this->assertEquals($importFileSplitter->getSplitFileName(0),"{$this->_goodFile}-0");
        $this->assertEquals($importFileSplitter->getSplitFileName(1),"{$this->_goodFile}-1");
        $this->assertEquals($importFileSplitter->getSplitFileName(2),false);
    }
	
	/**
	 * @ticket 25119
	 */
    public function testTrimSpaces()
    {
        $splitter = new ImportFileSplitter($this->_whiteSpaceFile);
        $splitter->splitSourceFile(',',' ',false);
        
        $this->assertEquals(
            trim(file_get_contents("{$this->_whiteSpaceFile}-0")),
            trim(file_get_contents("{$this->_whiteSpaceFile}"))
            );
    }
}
