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

class Bug36978Test extends Sugar_PHPUnit_Framework_TestCase {

var $rel_guid;	
var $has_custom_table_dictionary;	
var $moduleList;

function setUp() 
{
    $this->markTestSkipped("Skipping unless otherwise specified");
    
    $admin = new User();
    $GLOBALS['current_user'] = $admin->retrieve('1');	
	
    $GLOBALS['app_list_strings'] = return_app_list_strings_language('en_us');
    
    //Create the custom relationships
    if(!file_exists('custom/Extension/modules/abc_Test/Ext/Vardefs')) {
       mkdir_recursive('custom/Extension/modules/abc_Test/Ext/Vardefs');
    }

    if(!file_exists('custom/Extension/modules/abc_Test/Ext/Layoutdefs')) {
       mkdir_recursive('custom/Extension/modules/abc_Test/Ext/Layoutdefs');
    }    
    
    if(!file_exists('modules/abc_Test/metadata')) {
       mkdir_recursive('modules/abc_Test/metadata');
    }
    
    if( $fh = @fopen('modules/abc_Test/metadata/studio.php', 'w+') )
    {
$string = <<<EOQ
\$GLOBALS['studioDefs']['abc_Test'] = array(

);
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }
    
    if( $fh = @fopen('custom/Extension/modules/abc_Test/Ext/Vardefs/test.php', 'w+') )
    {
$string = <<<EOQ

<?php
\$dictionary["abc_Test"]["fields"]["abc_test_abc_test"] = array (
  'name' => 'abc_test_abc_test',
  'type' => 'link',
  'relationship' => 'abc_test_abc_test',
  'source' => 'non-db',
  'side' => 'right',
  'vname' => 'LBL_ABC_TEST_ABC_TEST_FROM_ABC_TEST_L_TITLE',
);
?>
<?php
\$dictionary["abc_Test"]["fields"]["abc_test_abc_test_name"] = array (
  'name' => 'abc_test_abc_test_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_ABC_TEST_ABC_TEST_FROM_ABC_TEST_L_TITLE',
  'save' => true,
  'id_name' => 'abc_test_ab6dabc_test_ida',
  'link' => 'abc_test_abc_test',
  'table' => 'abc_test',
  'module' => 'abc_Test',
  'rname' => 'name',
);
?>
<?php
\$dictionary["abc_Test"]["fields"]["abc_test_ab6dabc_test_ida"] = array (
  'name' => 'abc_test_ab6dabc_test_ida',
  'type' => 'link',
  'relationship' => 'abc_test_abc_test',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_ABC_TEST_ABC_TEST_FROM_ABC_TEST_R_TITLE',
);
?>

EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    } 
    
    //Create the custom relationships
    if(!file_exists('custom/metadata')) {
       mkdir_recursive('custom/metadata');
    }    
    
    if( $fh = @fopen('custom/metadata/abc_test_abc_testMetaData.php', 'w+') )
    {
$string = <<<EOQ

<?php
\$dictionary["abc_test_abc_test"] = array (
  'true_relationship_type' => 'one-to-many',
  'relationships' => 
  array (
    'abc_test_abc_test' => 
    array (
      'lhs_module' => 'abc_Test',
      'lhs_table' => 'abc_test',
      'lhs_key' => 'id',
      'rhs_module' => 'abc_Test',
      'rhs_table' => 'abc_test',
      'rhs_key' => 'id',
      'relationship_type' => 'one-to-many',
      'join_table' => 'abc_test_abc_test_c',
      'join_key_lhs' => 'abc_test_ab6dabc_test_ida',
      'join_key_rhs' => 'abc_test_aed49bc_test_idb',
    ),
  ),
  'table' => 'abc_test_abc_test_c',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'id',
      'type' => 'varchar',
      'len' => 36,
    ),
    1 => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    2 => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
      'required' => true,
    ),
    3 => 
    array (
      'name' => 'abc_test_ab6dabc_test_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'abc_test_aed49bc_test_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'abc_test_abc_testspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'abc_test_abc_test_ida1',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'abc_test_ab6dabc_test_ida',
      ),
    ),
    2 => 
    array (
      'name' => 'abc_test_abc_test_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'abc_test_aed49bc_test_idb',
      ),
    ),
  ),
);
?>


EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }

    
if(!file_exists('custom/Extension/application/Ext/TableDictionary'))  {
   mkdir_recursive('custom/Extension/application/Ext/TableDictionary');
}
    

if( $fh = @fopen('custom/Extension/application/Ext/TableDictionary/abc_test_abc_test.php', 'w+') )
{
$string = <<<EOQ
<?php
include('custom/metadata/abc_test_abc_testMetaData.php');
?>
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
}

    $this->rel_guid = create_guid();
    $sql = "INSERT INTO relationships (id, relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, reverse, deleted) VALUES ('{$this->rel_guid}', 'abc_test_abc_test', 'abc_Test', 'abc_test', 'id', 'abc_Test', 'abc_test', 'id', 'abc_test_abc_test_c', 'abc_test_ab6abc_test_id', 'abc_test_aed49bc_test_id', 'one-to-many', 0, 0)";
    $GLOBALS['db']->query($sql); 

    $rel = new Relationship();
    Relationship::delete_cache();
    $rel->build_relationship_cache();
    
    $this->moduleList = $GLOBALS['moduleList'];
}

function tearDown() {
    if(file_exists('custom/Extension/modules/abc_Test/Ext/Vardefs/test.php')) {
       unlink('custom/Extension/modules/abc_Test/Ext/Vardefs/test.php'); 
    }

    if(file_exists('custom/metadata/abc_test_abc_testMetaData.php')) {
       unlink('custom/metadata/abc_test_abc_testMetaData.php'); 
    }    
    
    if(file_exists('custom/Extension/application/Ext/TableDictionary/abc_test_abc_test.php')) {
       unlink('custom/Extension/application/Ext/TableDictionary/abc_test_abc_test.php'); 
    }

    if(file_exists('modules/abc_Test/metadata/studio.php')) {
       unlink('modules/abc_Test/metadata/studio.php'); 
    }    
    
    if(is_dir('custom/Extension/modules/abc_Test/Ext/Vardefs')) {
        rmdir_recursive('custom/Extension/modules/abc_Test/Ext/Vardefs');
    }
    if(is_dir('custom/Extension/modules/abc_Test/Ext/Layoutdefs')) {
        rmdir_recursive('custom/Extension/modules/abc_Test/Ext/Layoutdefs');
    }
    if(is_dir('custom/Extension/modules/abc_Test/Ext')) {
        rmdir_recursive('custom/Extension/modules/abc_Test/Ext');
    }
    if(is_dir('custom/Extension/modules/abc_Test')) {
        rmdir_recursive('custom/Extension/modules/abc_Test');
    }
    if(is_dir('modules/abc_Test/metadata')) {
        rmdir_recursive('modules/abc_Test/metadata');
    }
    if(is_dir('modules/abc_Test')) {
        rmdir_recursive('modules/abc_Test');
    }
	
    if ( !empty($this->rel_guid) ) {
        $sql = "DELETE FROM relationships WHERE id = '{$this->rel_guid}'";
        $GLOBALS['db']->query($sql);
	}
	
	if ( !empty($this->moduleList) ) {
        $GLOBALS['moduleList'] = $this->moduleList;
    }
}


function test_upgrade_custom_relationships() {	
	$GLOBALS['moduleList'] = array();
	$GLOBALS['moduleList'][] = 'abc_Test';
	$GLOBALS['beanList']['abc_Test'] = 'abc_Test';
	/*
    include('modules/Administration/upgrade_custom_relationships.php');
	upgrade_custom_relationships();
	include('custom/Extension/modules/abc_Test/Ext/Vardefs/test.php');
	*/
}


}
?>