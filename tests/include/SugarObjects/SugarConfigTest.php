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

 
require_once 'include/SugarObjects/SugarConfig.php';

class SugarConfigTest extends Sugar_PHPUnit_Framework_TestCase
{
    private $_old_sugar_config = null;

    public function setUp() 
    {
        $this->_old_sugar_config = $GLOBALS['sugar_config'];
        $GLOBALS['sugar_config'] = array();
    }

    public function tearDown() 
    {
        $config = SugarConfig::getInstance();
        $config->clearCache();
        $GLOBALS['sugar_config'] = $this->_old_sugar_config;
    }

    /**
     * Stores a key/value pair in the config
     *
     * @internal override this in sub-classes if you are testing with the
     *           config data stored somewhere other than the $sugar_config
     *           super global
     * @param string $key
     * @param string $value
     */
    private function _addKeyValueToConfig(
        $key, 
        $value
        ) 
    {
        $GLOBALS['sugar_config'][$key] = $value;
    }

    private function _generateRandomValue() 
    {
        $this->_random = 'Some Random Foobar: ' . rand(10000, 20000);
        return $this->_getLastRandomValue();
    }

    private function _getLastRandomValue() 
    {
        return $this->_random;
    }

    public function testGetInstanceReturnsASugarConfigObject() 
    {
        $this->assertTrue(SugarConfig::getInstance() instanceOf SugarConfig, 'Returned object is not a SugarConfig object');
    }

    public function testGetInstanceReturnsASingleton() 
    {
        $one = SugarConfig::getInstance();
        $two = SugarConfig::getInstance();
        $this->assertSame($one, $two);
    }

    public function testReadsGlobalSugarConfigArray() 
    {
        for ($i = 0; $i < 10; $i++) {
            $anonymous_key = 'key-' . $i;
            $random_value = rand(10000, 20000);
            $rawConfigArray[$anonymous_key] = $random_value;
            $this->_addKeyValueToConfig($anonymous_key, $random_value);
        }

        $config = SugarConfig::getInstance();
        foreach ($rawConfigArray as $key => $value) {
            $this->assertEquals(
                $config->get($key), $value,
                "SugarConfig::get({$key}) should be equal to {$value}, got " . $config->get($key)
            );
        }
    }

    public function testAllowDotNotationForSubValuesWithinTheConfig() 
    {
        $random_value = 'Some Random Integer: ' . rand(1000, 2000);
        $this->_addKeyValueToConfig('grandparent', array(
                'parent' => array(
                'child' => $random_value,
            ),
        ));

        $config = SugarConfig::getInstance();
        $this->assertEquals($random_value, $config->get('grandparent.parent.child'));
    }

    public function testReturnsNullOnUnknownKey() 
    {
        $config = SugarConfig::getInstance();
        $this->assertNull($config->get('unknown-and-unknowable'));
    }

    public function testReturnsNullOnUnknownKeyWithinAHeirarchy() 
    {
        $this->_addKeyValueToConfig('grandparent', array(
            'parent' => array(
                'child' => 'foobar',
            ),
        ));
        $config= SugarConfig::getInstance();

        $this->assertNull($config->get('some-unknown-grandparent.parent.child'));
        $this->assertNull($config->get('grandparent.some-unknown-parent.child'));
        $this->assertNull($config->get('grandparent.parent.some-unknown-child'));
    }

    public function testAllowSpecifyingDefault() 
    {
        $config = SugarConfig::getInstance();

        $random = rand(10000, 20000);
        $this->assertSame($random, $config->get('unknown-and-unknowable', $random));
    }

    public function testAllowSpecifyingDefaultForSubValues() 
    {
        $this->_addKeyValueToConfig('grandparent', array(
            'parent' => array(
                'child' => 'foobar',
            ),
        ));
        $config = SugarConfig::getInstance();

        $this->assertEquals(
            $this->_generateRandomValue(),
            $config->get(
                'some-unknown-grandparent.parent.child',
                $this->_getLastRandomValue()
            )
        );
        $this->assertEquals(
            $this->_generateRandomValue(),
            $config->get(
                'grandparent.some-unknown-parent.child',
                $this->_getLastRandomValue()
            )
        );
        $this->assertEquals(
            $this->_generateRandomValue(),
            $config->get(
                'grandparent.parent.some-unknown-child',
                $this->_getLastRandomValue()
            )
        );
    }

    public function testStoresValuesInMemoryAfterFirstLookup() 
    {
        $this->_addKeyValueToConfig('foobar', 'barfoo');

        $config = SugarConfig::getInstance();
        $this->assertEquals($config->get('foobar'), 'barfoo');

        $this->_addKeyValueToConfig('foobar', 'foobar');
        $this->assertEquals($config->get('foobar'), 'barfoo', 'should still be equal "barfoo": got ' . $config->get('foobar'));
    }

    public function testCanClearsCachedValues() 
    {
        $this->_addKeyValueToConfig('foobar', 'barfoo');

        $config = SugarConfig::getInstance();
        $this->assertEquals($config->get('foobar'), 'barfoo', 'sanity check');
        $this->_addKeyValueToConfig('foobar', 'foobar');
        $this->assertEquals($config->get('foobar'), 'barfoo', 'sanity check');

        $config->clearCache();
        $this->assertEquals($config->get('foobar'), 'foobar', 'after clearCache() call, new value should be used');
    }

    public function testCanCherryPickKeyToClear() 
    {
        $this->_addKeyValueToConfig('foobar', 'barfoo');
        $this->_addKeyValueToConfig('barfoo', 'barfoo');

        $config = SugarConfig::getInstance();
        $this->assertEquals($config->get('foobar'), 'barfoo', 'sanity check, got: ' . $config->get('foobar'));
        $this->assertEquals($config->get('barfoo'), 'barfoo', 'sanity check');

        $this->_addKeyValueToConfig('foobar', 'foobar');
        $this->_addKeyValueToConfig('barfoo', 'foobar');
        $this->assertEquals($config->get('foobar'), 'barfoo', 'should still be equal to "barfoo", got: ' . $config->get('barfoo'));
        $this->assertEquals($config->get('barfoo'), 'barfoo', 'should still be equal to "barfoo", got: ' . $config->get('barfoo'));

        $config->clearCache('barfoo');
        $this->assertEquals($config->get('barfoo'), 'foobar', 'should be equal to "foobar" after cherry picked for clearing');
        $this->assertEquals($config->get('foobar'), 'barfoo', 'should not be effected by cherry picked clearCache() call');
    }

    public function testDemonstrateGrabbingSiblingNodes() 
    {
        $this->_addKeyValueToConfig('foobar', array(
            'foo' => array(
                array(
                    'first' => 'one',
                ),
                array(
                    'first' => 'uno',
                ),
            ),
        ));

        $config = SugarConfig::getInstance();
        $this->assertEquals($config->get('foobar.foo.0.first'), 'one');
        $this->assertEquals($config->get('foobar.foo.1.first'), 'uno');
    }
}

