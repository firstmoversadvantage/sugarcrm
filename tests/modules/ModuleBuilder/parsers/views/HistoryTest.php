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


require_once("modules/ModuleBuilder/parsers/views/History.php");

class HistoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    private $_path;

    /**
     * @var History
     */
    private $_history;

    public function setUp()
    {
        $this->_path = tempnam(sys_get_temp_dir() . 'tmp', 'history');
        $this->_history = new History($this->_path);
    }

    public function testConstructor()
    {
        $this->assertTrue(is_dir($this->getHistoryDir()), "__constructor() creates unique directory for file history");
    }

    public function testAppendAndRestore()
    {
        $time = $this->_history->append($this->_path);
        $this->assertTrue(file_exists($this->_history->getFileByTimestamp($time)), '->append() creates history file');
        $this->assertEquals($this->_history->restoreByTimestamp( $time ), $time, '->restoreByTimestamp() returns correct timestamp');
    }

    public function testUndoRestore()
    {
        $this->_history->undoRestore();
        $this->assertFalse(file_exists($this->_path), '->undoRestore removes file');
    }

    public function testPositioning()
    {
        $other_file = tempnam(sys_get_temp_dir(), 'history');
        
        $el1 = $this->_history->append($other_file);
        $el2 = $this->_history->append($other_file);
        $el3 = $this->_history->append($other_file);

        $this->assertEquals($this->_history->getCount(), 3);
        $this->assertEquals($this->_history->getFirst(), $el3);
        $this->assertEquals($this->_history->getLast(), $el1);
        $this->assertEquals($this->_history->getNth(1), $el2);
        $this->assertEquals($this->_history->getNext(), $el1);
        $this->assertFalse($this->_history->getNext());

        unlink($other_file);
    }

    private function getHistoryDir()
    {
        return dirname($this->_path);
    }
    
}