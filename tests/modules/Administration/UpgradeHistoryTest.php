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

require_once 'modules/Administration/UpgradeHistory.php';

class UpgradeHistoryTest extends Sugar_PHPUnit_Framework_TestCase
{
	public function testCheckForExistingSQL()
    {
        $patchToCheck = new stdClass();
        $patchToCheck->name = 'abc';
        $patchToCheck->id = '';
            $GLOBALS['db']->query("INSERT INTO upgrade_history (id, name, md5sum, date_entered) VALUES
('444','abc','444','2008-12-20 08:08:20') ");
            $GLOBALS['db']->query("INSERT INTO upgrade_history (id, name, md5sum, date_entered) VALUES
('555','abc','555','2008-12-20 08:08:20')");
		$uh = new UpgradeHistory();
    	$return = $uh->checkForExisting($patchToCheck);
		$this->assertContains($return->id, array('444','555'));
    	
    	$patchToCheck->id = '555';
    	$return = $uh->checkForExisting($patchToCheck);
    	$this->assertEquals($return->id, '444');
    	
    	$GLOBALS['db']->query("delete from upgrade_history where id='444'");
   		$GLOBALS['db']->query("delete from upgrade_history where id='555'");
    }
    
    /**
     * @ticket 44075
     */
    public function testTrackerVisibilityBug44075()
    {
        $uh = new UpgradeHistory();
        $this->assertFalse($uh->tracker_visibility);
    }
}