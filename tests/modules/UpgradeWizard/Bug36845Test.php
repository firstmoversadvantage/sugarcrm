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

 
class Bug36845Test extends Sugar_PHPUnit_Framework_TestCase 
{
    var $has_custom_unified_search_modules_display = false;
    var $has_custom_unified_search_modules = false;	
    var $module_dir = 'modules/clabc_Bug36845Test';
    var $module = 'clabc_Bug36845Test';

    public function setUp() 
    {
        //$this->useOutputBuffering = false;
        require('include/modules.php');
        global $beanFiles, $beanList;

        if(file_exists('cache/modules/unified_search_modules.php'))
        {
            $this->has_custom_unified_search_modules = true;
            copy('cache/modules/unified_search_modules.php', 'cache/modules/unified_search_modules.php.bak');
        }
    
        if(file_exists('custom/modules/unified_search_modules_display.php'))
        {
            $this->has_custom_unified_search_modules_display = true;
            copy('custom/modules/unified_search_modules_display.php', 'custom/modules/unified_search_modules_display.php.bak');
        }	
        
        if(file_exists($this->module_dir))
        {
           rmdir_recursive($this->module_dir);
        }
        
        mkdir_recursive($this->module_dir . '/metadata');
        
        $the_string = <<<EOQ
<?php
\$module_name = "{$this->module}";
\$searchFields[\$module_name] = 
    array (
        'name' => array( 'query_type'=>'default'),
        'account_type'=> array('query_type'=>'default', 'options' => 'account_type_dom', 'template_var' => 'ACCOUNT_TYPE_OPTIONS'),
        'industry'=> array('query_type'=>'default', 'options' => 'industry_dom', 'template_var' => 'INDUSTRY_OPTIONS'),
        'annual_revenue'=> array('query_type'=>'default'),
        'address_street'=> array('query_type'=>'default','db_field'=>array('billing_address_street','shipping_address_street')),
        'address_city'=> array('query_type'=>'default','db_field'=>array('billing_address_city','shipping_address_city')),
        'address_state'=> array('query_type'=>'default','db_field'=>array('billing_address_state','shipping_address_state')),
        'address_postalcode'=> array('query_type'=>'default','db_field'=>array('billing_address_postalcode','shipping_address_postalcode')),
        'address_country'=> array('query_type'=>'default','db_field'=>array('billing_address_country','shipping_address_country')),
        'rating'=> array('query_type'=>'default'),
        'phone'=> array('query_type'=>'default','db_field'=>array('phone_office')),
        'email'=> array('query_type'=>'default','db_field'=>array('email1','email2')),
        'website'=> array('query_type'=>'default'),
        'ownership'=> array('query_type'=>'default'),
        'employees'=> array('query_type'=>'default'),
        'ticker_symbol'=> array('query_type'=>'default'),
        'current_user_only'=> array('query_type'=>'default','db_field'=>array('assigned_user_id'),'my_items'=>true, 'vname' => 'LBL_CURRENT_USER_FILTER', 'type' => 'bool'),
        'assigned_user_id'=> array('query_type'=>'default'),
        'favorites_only' => array(
            'query_type'=>'format',
            'operator' => 'subquery',
            'subquery' => 'SELECT sugarfavorites.record_id FROM sugarfavorites 
                                WHERE sugarfavorites.deleted=0 
                                    and sugarfavorites.module = \''.\$module_name.'\' 
                                    and sugarfavorites.assigned_user_id = \'{0}\'',
            'db_field'=>array('id')),
    );
?>
EOQ;
    
        $fp = sugar_fopen($this->module_dir . '/metadata/SearchFields.php', "w");
        fwrite( $fp, $the_string );
        fclose( $fp );	
            
        $table_name = strtolower($this->module);
        $the_string = <<<EOQ
<?php
\$dictionary["{$this->module}"] = array(
    'table'=>"{$table_name}",
    'audited'=>true,
    'fields'=>array (
),
    'relationships'=>array (
),
    'optimistic_locking'=>true,
);
if (!class_exists('VardefManager')){
        require_once('include/SugarObjects/VardefManager.php');
}
VardefManager::createVardef("{$this->module}","{$this->module}", array('basic','team_security','assignable','company'));
?>
EOQ;
    
        $fp = sugar_fopen($this->module_dir . '/vardefs.php', "w");
        fwrite( $fp, $the_string );
        fclose( $fp );
        
        $the_string = <<<EOQ
<?php
class clabc_Bug36845Test extends Basic
{
}
?>
EOQ;

        $fp = sugar_fopen($this->module_dir . '/clabc_Bug36845Test.php', "w");
        fwrite( $fp, $the_string );
        fclose( $fp );

        $beanFiles['clabc_Bug36845Test'] = 'modules/clabc_Bug36845Test/clabc_Bug36845Test.php';
        $beanList['clabc_Bug36845Test'] = 'clabc_Bug36845Test';

    }
    
    public function tearDown()
    {
        if(file_exists('cache/modules/unified_search_modules.php'))
        {
            unlink('cache/modules/unified_search_modules.php');
        }
    
        if(file_exists('custom/modules/unified_search_modules_display.php'))
        {
            unlink('custom/modules/unified_search_modules_display.php');
        }	
        
        if($this->has_custom_unified_search_modules)
        {
            copy('cache/modules/unified_search_modules.php.bak', 'cache/modules/unified_search_modules.php');
            unlink('cache/modules/unified_search_modules.php.bak');
        }
    
        if($this->has_custom_unified_search_modules_display)
        {
            copy('custom/modules/unified_search_modules_display.php.bak', 'custom/modules/unified_search_modules_display.php');
            unlink('custom/modules/unified_search_modules_display.php.bak');
        }	

        
        if(file_exists("custom/{$this->module_dir}/metadata"))
        {
            rmdir_recursive("custom/{$this->module_dir}/metadata");
        }

        if(file_exists($this->module_dir))
        {
           rmdir_recursive($this->module_dir);
        }
    }

    public function test_update_custom_vardefs()
    {
        $this->assertTrue(file_exists("{$this->module_dir}/metadata/SearchFields.php"), 'Assert that we have a SearchFields.php file');
        $this->assertTrue(file_exists("{$this->module_dir}/vardefs.php"), 'Assert that we have a vardefs.php file');
        require_once('modules/UpgradeWizard/uw_utils.php');
        add_unified_search_to_custom_modules_vardefs();
        require_once('modules/Home/UnifiedSearchAdvanced.php');
        $usa = new UnifiedSearchAdvanced();
        $usa->buildCache();
        $this->assertTrue(file_exists('cache/modules/unified_search_modules.php'), 'Assert that we have a unified_search_modules.php file');
        include('cache/modules/unified_search_modules.php');
        $this->assertTrue(isset($unified_search_modules['clabc_Bug36845Test']), 'Assert that the custom module was added to unified_search_modules.php');
        $this->assertEquals(false, $unified_search_modules['clabc_Bug36845Test']['default'], 'Assert that the custom module was set to not be searched on by default');
    }
    
    
    public function test_update_custom_vardefs_without_searchfields()
    {
        if(!file_exists("custom/{$this->module_dir}/metadata"))
        {
            mkdir_recursive("custom/{$this->module_dir}/metadata");
        }
        copy("{$this->module_dir}/metadata/SearchFields.php", "custom/{$this->module_dir}/metadata/SearchFields.php");
        unlink("{$this->module_dir}/metadata/SearchFields.php");
        $this->assertTrue(!file_exists("{$this->module_dir}/metadata/SearchFields.php"), 'Assert that we do not have a SearchFields.php file in modules directory');
        $this->assertTrue(file_exists("{$this->module_dir}/vardefs.php"), 'Assert that we have a vardefs.php file');
        require_once('modules/UpgradeWizard/uw_utils.php');
        add_unified_search_to_custom_modules_vardefs();
        require_once('modules/Home/UnifiedSearchAdvanced.php');
        $usa = new UnifiedSearchAdvanced();
        $usa->buildCache();
        $this->assertTrue(file_exists("cache/modules/unified_search_modules.php"), 'Assert that we have a unified_search_modules.php file');
        include('cache/modules/unified_search_modules.php');
        //echo var_export($unified_search_modules['clabc_Bug36845Test'], true);
        $this->assertTrue(isset($unified_search_modules['clabc_Bug36845Test']), 'Assert that the custom module was added to unified_search_modules.php');
        
    }
    
    
    public function test_create_unified_search_modules_display()
    {
        if(file_exists('custom/modules/unified_search_modules_display.php'))
        {
            unlink('custom/modules/unified_search_modules_display.php');
        }		
        
        require_once('modules/UpgradeWizard/uw_utils.php');
        $usa = new UnifiedSearchAdvanced();
        $_REQUEST['enabled_modules'] = 'Accounts,Bug36845Test';
        $usa->saveGlobalSearchSettings();
        $this->assertTrue(file_exists('custom/modules/unified_search_modules_display.php'), 'Assert that unified_search_modules_display.php file was created');        
    }
}