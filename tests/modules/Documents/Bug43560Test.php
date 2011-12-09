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

require_once('modules/Documents/DocumentSoap.php');

class Bug43560Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $doc = null;
	
	public function setUp()
    {
        global $current_user, $currentModule ;
		$mod_strings = return_module_language($GLOBALS['current_language'], "Documents");
		$current_user = SugarTestUserUtilities::createAnonymousUser();

		$document = new Document();
        $document->document_name = 'Bug 43560 Test Document';
        $document->save();
		$this->doc = $document;
	}
	
    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        unset($GLOBALS['mod_strings']);
        
        $GLOBALS['db']->query("DELETE FROM documents WHERE id = '{$this->doc->id}'");
        unset($this->doc);
    }
	
	function testRevisionSave() {
        $ret = $GLOBALS['db']->query("SELECT COUNT(*) AS rowcount1 FROM document_revisions WHERE document_id = '{$this->doc->id}'");
        $row = $GLOBALS['db']->fetchByAssoc($ret);
        $this->assertEquals($row['rowcount1'],0,'We created an empty revision');

        $ret = $GLOBALS['db']->query("SELECT document_revision_id FROM documents WHERE id = '{$this->doc->id}'");
        $row = $GLOBALS['db']->fetchByAssoc($ret);
        $this->assertTrue(empty($row['document_revision_id']),'We linked the document to a fake document_revision');
        
        $ds = new DocumentSoap();
        $revision_stuff = array('file' => base64_encode('Pickles has an extravagant beard of pine fur.'), 'filename' => 'a_file_about_pickles.txt', 'id' => $this->doc->id, 'revision' => '1');
        $revisionId = $ds->saveFile($revision_stuff);

        $ret = $GLOBALS['db']->query("SELECT COUNT(*) AS rowcount1 FROM document_revisions WHERE document_id = '{$this->doc->id}'");
        $row = $GLOBALS['db']->fetchByAssoc($ret);
        $this->assertEquals($row['rowcount1'],1,'We didn\'t create a revision when we should have');
        
        $ret = $GLOBALS['db']->query("SELECT document_revision_id FROM documents WHERE id = '{$this->doc->id}'");
        $row = $GLOBALS['db']->fetchByAssoc($ret);
        $this->assertEquals($row['document_revision_id'],$revisionId,'We didn\'t link the newly created document revision to the document');

        // Double saving doesn't work because save doesn't reset the new_with_id
        $newDoc = new Document();
        $newDoc->retrieve($this->doc->id);

        $newDoc->document_revision_id = $revisionId;
        $newDoc->save(FALSE);

        $ret = $GLOBALS['db']->query("SELECT COUNT(*) AS rowcount1 FROM document_revisions WHERE document_id = '{$this->doc->id}'");
        $row = $GLOBALS['db']->fetchByAssoc($ret);
        $this->assertEquals($row['rowcount1'],1,'We didn\'t create a revision when we should have');
        
        $ret = $GLOBALS['db']->query("SELECT document_revision_id FROM documents WHERE id = '{$this->doc->id}'");
        $row = $GLOBALS['db']->fetchByAssoc($ret);
        $this->assertEquals($row['document_revision_id'],$revisionId,'We didn\'t link the newly created document revision to the document');


	}

}
