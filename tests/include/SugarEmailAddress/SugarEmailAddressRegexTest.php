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

 
require_once 'include/SugarEmailAddress/SugarEmailAddress.php';

class SugarEmailAddressRegexTest extends Sugar_PHPUnit_Framework_TestCase
{
    public function providerEmailAddressRegex()
	{
	    return array(
	        array('john@john.com',true),
	        array('----!john.com',false),
	        // For Bug 13765
	        array('st.-annen-stift@t-online.de',true),
	        // For Bug 39186
	        array('qfflats-@uol.com.br',true),
	        array('atendimento-hd.@uol.com.br',true),
	        // For Bug 44338
	        array('jo&hn@john.com',true),
	        array('joh#n@john.com.br',true),
	        array('&#john@john.com', true),
	        array('atendimento-hd.?uol.com.br',false),
	        array('atendimento-hd.?uol.com.br;aaa@com.it',false),
	        array('f.grande@pokerspa.it',true),
	        array('fabio.grande@softwareontheroad.it',true),
	        array('fabio$grande@softwareontheroad.it',true),
	        // For Bug 44473
	        array('ettingshallprimaryschool@wolverhampton.gov.u',false),
	        // For Bug 13018
	        array('Ert.F.Suu.-PA@pumpaudio.com',true),
	        // For Bug 23202
	        array('test--user@example.com',true),
	        // For Bug 42403
	        array('test@t--est.com',true),
	        // For Bug 42404
	        array('t.-est@test.com',true),
	        );
	}
    
    /**
     * @ticket 13765
     * @ticket 39186
     * @ticket 44338
     * @ticket 44473
     * @ticket 13018
     * @ticket 23202
     * @ticket 42403
     * @ticket 42404
     * @dataProvider providerEmailAddressRegex
     */
	public function testEmailAddressRegex($email, $valid) 
    {
        $startTime = microtime(true);
        $sea = new SugarEmailAddress;
        
        if ( $valid ) {
            $this->assertRegExp($sea->regex,$email);
        }
        else {
            $this->assertNotRegExp($sea->regex,$email);
        }
        
        // Checking for elapsed time. I expect that evaluation takes less than a second.
        $timeElapsed = microtime(true) - $startTime;
        $this->assertLessThan(1.0, $timeElapsed);
    }
}
