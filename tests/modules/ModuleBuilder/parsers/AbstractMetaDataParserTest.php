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

 
require_once("modules/ModuleBuilder/parsers/views/AbstractMetaDataParser.php");

class AbstractMetaDataParserTest extends Sugar_PHPUnit_Framework_TestCase
{
	
	public function setUp() 
    {                       
	
    }
    
    public function tearDown() 
    {
       
    }
    
    public function testValidField()
    {
    	$validDef = array (
		    'name' => 'status',
		    'vname' => 'LBL_STATUS',
		    'type' => 'enum',
		    'len' => '25',
		    'options' => 'meeting_status_dom',
		    'comment' => 'Meeting status (ex: Planned, Held, Not held)'
		);
		
		$invalidDef = array (
		    'name' => 'direction',
		    'vname' => 'LBL_DIRECTION',
		    'type' => 'enum',
		    'len' => '25',
		    'options' => 'call_direction_dom',
		    'comment' => 'Indicates whether call is inbound or outbound',
		    'source' => 'non-db',
		    'importable' => 'false',
		    'massupdate'=>false,
		    'reportable'=>false
		);
		
		$this->assertTrue(AbstractMetaDataParser::validField($validDef));
		$this->assertFalse(AbstractMetaDataParser::validField($invalidDef));
		
		//Test the studio override property
		$invalidDef['studio'] = 'visible';
		$validDef['studio'] = false;
		
		$this->assertFalse(AbstractMetaDataParser::validField($validDef));
        $this->assertTrue(AbstractMetaDataParser::validField($invalidDef));
		
		$invalidDef['studio'] = array('editview' => 'visible');
        
        $this->assertTrue(AbstractMetaDataParser::validField($invalidDef, 'editview'));
		$this->assertFalse(AbstractMetaDataParser::validField($invalidDef, 'detailview'));
    }
    
}