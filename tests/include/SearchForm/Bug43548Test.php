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

 
require_once 'include/SearchForm/SugarSpot.php';

class Bug43548Test extends Sugar_PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    	if(file_exists('custom/modules/Accounts/metadata/SearchFields.php'))
    	{
    	   copy('custom/modules/Accounts/metadata/SearchFields.php', 'custom/modules/Accounts/metadata/SearchFields.php.bak');
    	} else {
    	   if(!file_exists('custom/modules/Accounts/metadata'))
    	   {
    	      mkdir_recursive('custom/modules/Accounts/metadata');
    	   }
    	}    	

    }
    
    public function tearDown()
    {
        if(file_exists('custom/modules/Accounts/metadata/SearchFields.php'))
    	{
    	   unlink('custom/modules/Accounts/metadata/SearchFields.php');
    	} 

    	if(file_exists('custom/modules/Accounts/metadata/SearchFields.php.bak'))
    	{
    	   copy('custom/modules/Accounts/metadata/SearchFields.php.bak', 'custom/modules/Accounts/metadata/SearchFields.php');
    	   unlink('custom/modules/Accounts/metadata/SearchFields.php.bak');
    	}
    }

    
    public function testSugarSpotSearchGetSearchFieldsWithInline()
    {
    	//Load custom file with inline style of custom overrides
    if( $fh = @fopen('custom/modules/Accounts/metadata/SearchFields.php', 'w+') )
	{
$string = <<<EOQ
<?php
\$searchFields['Accounts']['account_type'] = array('query_type'=>'default', 'options' => 'account_type_dom', 'template_var' => 'ACCOUNT_TYPE_OPTIONS');
?>
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }        	
    	$spot = new SugarSpotMock();
    	$searchFields = $spot->getTestSearchFields('Accounts');
    	$this->assertTrue(isset($searchFields['Accounts']['name']), 'Assert that name field is still set');
    	$this->assertTrue(isset($searchFields['Accounts']['account_type']), 'Assert that account_type field is still set');
    }
    
    public function testSugarSpotGetSearchFieldsWithCustomOverride()
    {
    	//Load custom file with override style of custom overrides
    if( $fh = @fopen('custom/modules/Accounts/metadata/SearchFields.php', 'w+') )
	{
$string = <<<EOQ
<?php

\$searchFields['Accounts'] = 
	array (
		'name' => array( 'query_type'=>'default'),
		'account_type'=> array('query_type'=>'default', 'options' => 'account_type_dom', 'template_var' => 'ACCOUNT_TYPE_OPTIONS'),
    );

?>
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }    
    
    	$spot = new SugarSpotMock();
    	$searchFields = $spot->getTestSearchFields('Accounts');
    	$this->assertTrue(isset($searchFields['Accounts']['name']), 'Assert that name field is still set');
    	$this->assertTrue(isset($searchFields['Accounts']['account_type']), 'Assert that account_type field is still set');    	
    }
    
    
}

//Create SugarSpotMock since getSearchFields is protected
class SugarSpotMock extends SugarSpot {
	function getTestSearchFields($moduleName)
	{
		return parent::getSearchFields($moduleName);
	}
}

?>