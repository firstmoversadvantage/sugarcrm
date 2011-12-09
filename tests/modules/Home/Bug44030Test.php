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

 
class Bug44030Test extends Sugar_PHPUnit_Framework_TestCase
{
	var $unified_search_modules_file;
    
    public function setUp() 
    {
	    global $beanList, $beanFiles, $dictionary;
	    	
	    //Add entries to simulate custom module
	    $beanList['Bug44030_TestPerson'] = 'Bug44030_TestPerson';
	    $beanFiles['Bug44030_TestPerson'] = 'modules/Bug44030_TestPerson/Bug44030_TestPerson.php';
	    
	    VardefManager::loadVardef('Contacts', 'Contact');
	    $dictionary['Bug44030_TestPerson'] = $dictionary['Contact'];
	    
	    //Copy over custom SearchFields.php file
        if(!file_exists('custom/modules/Bug44030_TestPerson/metadata')) {
       		mkdir_recursive('custom/modules/Bug44030_TestPerson/metadata');
    	}
    
    if( $fh = @fopen('custom/modules/Bug44030_TestPerson/metadata/SearchFields.php', 'w+') )
    {
$string = <<<EOQ
<?php
\$searchFields['Bug44030_TestPerson']['email'] = array(
'query_type' => 'default',
'operator' => 'subquery',
'subquery' => 'SELECT eabr.bean_id FROM email_addr_bean_rel eabr JOIN email_addresses ea ON (ea.id = eabr.email_address_id) WHERE eabr.deleted=0 AND ea.email_address LIKE',
'db_field' => array('id',),
'vname' =>'LBL_ANY_EMAIL',
);
?>
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }	    
	    
	    
	    //Remove the cached unified_search_modules.php file
	    $this->unified_search_modules_file = $GLOBALS['sugar_config']['cache_dir'] . 'modules/unified_search_modules.php';
    	if(file_exists($this->unified_search_modules_file))
		{
			copy($this->unified_search_modules_file, $this->unified_search_modules_file.'.bak');
			unlink($this->unified_search_modules_file);
		}		
    }
    
    public function tearDown() 
    {
	    global $beanList, $beanFiles, $dictionary;
	    
		if(file_exists($this->unified_search_modules_file . '.bak'))
		{
			copy($this->unified_search_modules_file . '.bak', $this->unified_search_modules_file);
			unlink($this->unified_search_modules_file . '.bak');
		}	
		
		if(file_exists('custom/modules/Bug44030_TestPerson/metadata/SearchFields.php'))
		{
			unlink('custom/modules/Bug44030_TestPerson/metadata/SearchFields.php');
			rmdir_recursive('custom/modules/Bug44030_TestPerson');
		}
		unset($beanFiles['Bug44030_TestPerson']);
		unset($beanList['Bug44030_TestPerson']);
		unset($dictionary['Bug44030_TestPerson']);
    }
	
	public function testUnifiedSearchAdvancedBuildCache()
	{
		require_once('modules/Home/UnifiedSearchAdvanced.php');
		$usa = new UnifiedSearchAdvanced();
		$usa->buildCache();
		
		//Assert we could build the file without problems
		$this->assertTrue(file_exists($this->unified_search_modules_file), "Assert {$this->unified_search_modules_file} file was created");
	
	    include($this->unified_search_modules_file);
	    $this->assertTrue(isset($unified_search_modules['Bug44030_TestPerson']), "Assert that we have the custom module set in unified_search_modules.php file");
	    $this->assertTrue(isset($unified_search_modules['Bug44030_TestPerson']['fields']['email']), "Assert that the email field was set for the custom module");
	}

}

?>