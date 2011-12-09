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

 
require_once('modules/Emails/Email.php');
require_once('modules/Notes/Note.php');

/**
 * @ticket 32489
 */
class Bug32489Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $em1 = null;
    var $note1 = null;
    var $note2 = null;
    
	var $outbound_id = null;
	
	public function setUp()
    {
        global $current_user, $currentModule,$timedate ;
		$mod_strings = return_module_language($GLOBALS['current_language'], "Contacts");
		$current_user = SugarTestUserUtilities::createAnonymousUser();
		$this->outbound_id = uniqid();
		$time = date('Y-m-d H:i:s');

		$em = new Email();
		$em->name = 'tst_' . uniqid();
		$em->type = 'inbound';
		$em->intent = 'pick';
		$em->date_sent = $timedate->to_display_date_time(gmdate("Y-m-d H:i:s", (gmmktime() + (3600 * 24 * 2) ))) ; //Two days from today 
		$em->save();
	    $this->em1 = $em;
	    
	    $n = new Note();
	    $n->name = 'tst_' . uniqid();
	    $n->filename = 'file_' . uniqid();
	    $n->parent_type = 'Emails';
	    $n->parent_id = $this->em1->id;
	    $n->save();
	    $this->note1 = $n;
	    
	    
	}

    public function tearDown()
    {
        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['current_user']);
        unset($GLOBALS['mod_strings']);
        $GLOBALS['db']->query("DELETE FROM emails WHERE id= '{$this->em1->id}'");
        $GLOBALS['db']->query("DELETE FROM notes WHERE id= '{$this->note1->id}'");
        if($this->note2 != null)
	        $GLOBALS['db']->query("DELETE FROM notes WHERE id= '{$this->note2->id}'");
        
        unset($this->em1);
        unset($this->note1);
        unset($this->note2);
    }
    
	function testSimpleImportEmailSearch(){
	    global $current_user,$timedate;
	   
	    //Simple search by name
        $_REQUEST['name'] = $this->em1->name;
	    $results = $this->em1->searchImportedEmails();
		$this->assertEquals(1, count($results['out']), "Could not perform a simple search for imported emails" );
		$this->assertEquals(count($results['out']), $results['totalCount'], "Imported emails search, total count of result set and count query not equal.");
		
		//Search should return nothing
		$_REQUEST['name'] =  uniqid() . uniqid(); //Should be enough entropy.	
		$results = $this->em1->searchImportedEmails();	
		$this->assertEquals(0, count($results['out']), "Could not perform a simple search for imported emails, expected no results" );
		
		//Search by date filters.
		$tomm = gmdate('Y-m-d H:i:s',(gmmktime() + 3600 * 24));
		$tommDisplay = $timedate->to_display_date_time($tomm);
		$_REQUEST['dateFrom'] = $tommDisplay;
		unset($_REQUEST['name']);
		$results = $this->em1->searchImportedEmails();
		$this->assertTrue(count($results['out']) >= 1, "Could not perform a simple search for imported emails with a single date filter" );

		$weekFromNow = gmdate('Y-m-d H:i:s',(gmmktime() + (3600 * 24 * 7)));
		$weekFromNowDisplay = $timedate->to_display_date_time($weekFromNow);
		$_REQUEST['dateTo'] = $weekFromNowDisplay;
		$results = $this->em1->searchImportedEmails();
		$this->assertTrue(count($results['out']) >= 1, "Could not perform a simple search for imported emails with a two date filter" );
    }
    
    function testSimpleImportEmailSearchWithAttachments()
    {
        unset($_REQUEST);
        $_REQUEST['name'] = $this->em1->name;
        $_REQUEST['attachmentsSearch'] = 1;
        $results = $this->em1->searchImportedEmails();	
		$this->assertEquals(1, count($results['out']), "Could not perform a simple search for imported emails with single attachment" );
		
		//Add a second note related to same parent, same results should be obtained.
		$n = new Note();
	    $n->name = 'tst2_' . uniqid();
	    $n->filename = 'file2_' . uniqid();
	    $n->parent_type = 'Emails';
	    $n->parent_id = $this->em1->id;
	    $n->save();
	    $this->note2 = $n;
	    $results = $this->em1->searchImportedEmails();	
		$this->assertEquals(1, count($results['out']), "Could not perform a simple search for imported emails with multiple attachment" );
    }
}
?>