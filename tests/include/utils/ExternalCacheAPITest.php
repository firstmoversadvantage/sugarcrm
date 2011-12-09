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

 
require_once('include/SugarCache/SugarCache.php');

class ExternalCacheAPITest extends Sugar_PHPUnit_Framework_TestCase
{
    public function setUp() 
    {
        $this->_cacheKey1   = 'test cache key 1 '.date("YmdHis");
        $this->_cacheValue1 = 'test cache value 1'.date("YmdHis");
        $this->_cacheKey2   = 'test cache key 2 '.date("YmdHis");
        $this->_cacheValue2 = 'test cache value 2 '.date("YmdHis");
        $this->_cacheKey3   = 'test cache key 3 '.date("YmdHis");
        $this->_cacheValue3 = array(
            'test cache value 3 key 1 '.date("YmdHis") => 'test cache value 3 value 1 '.date("YmdHis"),
            'test cache value 3 key 2 '.date("YmdHis") => 'test cache value 3 value 2 '.date("YmdHis"),
            'test cache value 3 key 3 '.date("YmdHis") => 'test cache value 3 value 3 '.date("YmdHis"),
            );
    }

    public function tearDown() 
    {
       // clear out the test cache if we haven't already
       if ( sugar_cache_retrieve($this->_cacheKey1) )
           sugar_cache_clear($this->_cacheKey1);
       if ( sugar_cache_retrieve($this->_cacheKey2) )
           sugar_cache_clear($this->_cacheKey2);
       if ( sugar_cache_retrieve($this->_cacheKey3) )
           sugar_cache_clear($this->_cacheKey3);
       SugarCache::$isCacheReset = false;
    }

    public function testSugarCacheValidate()
    {
        $this->assertTrue(sugar_cache_validate());
    }
    
    public function testStoreAndRetrieve()
    {
        sugar_cache_put($this->_cacheKey1,$this->_cacheValue1);
        sugar_cache_put($this->_cacheKey2,$this->_cacheValue2);
        sugar_cache_put($this->_cacheKey3,$this->_cacheValue3);
        $this->assertEquals(
            $this->_cacheValue1,
            sugar_cache_retrieve($this->_cacheKey1));
        $this->assertEquals(
            $this->_cacheValue2,
            sugar_cache_retrieve($this->_cacheKey2));
        $this->assertEquals(
            $this->_cacheValue3,
            sugar_cache_retrieve($this->_cacheKey3));
    }

    public function testStoreClearCacheKeyAndRetrieve()
    {
        sugar_cache_put($this->_cacheKey1,$this->_cacheValue1);
        sugar_cache_put($this->_cacheKey2,$this->_cacheValue2);
        sugar_cache_clear($this->_cacheKey1);
        $this->assertNotEquals(
            $this->_cacheValue1,
            sugar_cache_retrieve($this->_cacheKey1));
        $this->assertEquals(
            $this->_cacheValue2,
            sugar_cache_retrieve($this->_cacheKey2));
    }
    
    public function testStoreResetCacheAndRetrieve()
    {
        sugar_cache_put($this->_cacheKey1,$this->_cacheValue1);
        sugar_cache_put($this->_cacheKey2,$this->_cacheValue2);
        sugar_cache_reset();
        $this->assertNotEquals(
            $this->_cacheValue1,
            sugar_cache_retrieve($this->_cacheKey1));
        $this->assertNotEquals(
            $this->_cacheValue2,
            sugar_cache_retrieve($this->_cacheKey2));
    }
    
    /**
     * @ticket 40797
     */
    public function testRetrieveNonExistantKeyReturnsNull()
    {
        $this->assertNull(sugar_cache_retrieve('iamlookingforakeythatainthere'));
    }
}
