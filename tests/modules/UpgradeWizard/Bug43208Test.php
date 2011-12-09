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

/**
 * Bug43208Test
 * 
 * This test checks to see if the function repairTableDictionaryExtFile in uw_utils.php is working correctly.
 * There were some scenarios in 6.0.x whereby the files loaded in the extension tabledictionary.ext.php file 
 * did not exist.  This would cause warnings to appear during the upgrade.  As a result, we added the 
 * repairTableDictionaryExtFile function to scan the contents of tabledictionary.ext.php and then remove entries
 * where the file does not exist.
 */
class Bug43208Test extends Sugar_PHPUnit_Framework_TestCase 
{

var $tableDictionaryExtFile1 = 'custom/Extension/application/Ext/TableDictionary/tabledictionary.ext.php';		
var $tableDictionaryExtFile2 = 'custom/application/Ext/TableDictionary/tabledictionary.ext.php';	
var $corruptExtModuleFile = 'custom/Extension/application/Ext/TableDictionary/Bug43208_module.php';

function setUp() {

    if(file_exists($this->tableDictionaryExtFile1)) {
       copy($this->tableDictionaryExtFile1, $this->tableDictionaryExtFile1 . '.backup');
       unlink($this->tableDictionaryExtFile1);
    } else if(!file_exists('custom/Extension/application/Ext/TableDictionary')){
       mkdir_recursive('custom/Extension/application/Ext/TableDictionary');
    }

    if( $fh = @fopen($this->tableDictionaryExtFile1, 'w+') )
    {
$string = <<<EOQ
<?php

//WARNING: The contents of this file are auto-generated
include('custom/metadata/bug43208Test_productsMetaData.php');

//WARNING: The contents of this file are auto-generated
include('custom/Extension/application/Ext/TableDictionary/Bug43208_module.php');
?>
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }     
    

    if(file_exists($this->tableDictionaryExtFile2)) {
       copy($this->tableDictionaryExtFile2, $this->tableDictionaryExtFile2 . '.backup');
       unlink($this->tableDictionaryExtFile2);
    } else if(!file_exists('custom/application/Ext/TableDictionary')){
       mkdir_recursive('custom/application/Ext/TableDictionary');
    }    
    
    if( $fh = @fopen($this->tableDictionaryExtFile2, 'w+') )
    {
$string = <<<EOQ
<?php

//WARNING: The contents of this file are auto-generated
include('custom/metadata/bug43208Test_productsMetaData.php');

//WARNING: The contents of this file are auto-generated
include('custom/Extension/application/Ext/TableDictionary/Bug43208_module.php');
?>
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    } 
    
    if( $fh = @fopen($this->corruptExtModuleFile, 'w+') )
    {
$string = <<<EOQ
<?php
 //WARNING: The contents of this file are auto-generated
 	include( "custom/metadata/bug43208Test_productsMetaData.php" ); 
?>
EOQ;
       fputs( $fh, $string);
       fclose( $fh );
    }        
    
}

function tearDown() {
    if(file_exists($this->tableDictionaryExtFile1 . '.backup')) 
    {
       copy($this->tableDictionaryExtFile1 . '.backup', $this->tableDictionaryExtFile1);
       unlink($this->tableDictionaryExtFile1 . '.backup');  
    } else {
       unlink($this->tableDictionaryExtFile1);
    }

    if(file_exists($this->tableDictionaryExtFile2 . '.backup')) 
    {
       copy($this->tableDictionaryExtFile2 . '.backup', $this->tableDictionaryExtFile2);
       unlink($this->tableDictionaryExtFile2 . '.backup');  
    } else {
       unlink($this->tableDictionaryExtFile2);
    }    
    
    if(file_exists($this->corruptExtModuleFile)) {
       unlink($this->corruptExtModuleFile);
    }
    
}


function testRepairTableDictionaryExtFile() 
{	
	require_once('ModuleInstall/ModuleInstaller.php');
	repairTableDictionaryExtFile();
	
	if(function_exists('sugar_fopen'))
	{
		$fp = @sugar_fopen($this->tableDictionaryExtFile1, 'r');
	} else {
		$fp = fopen($this->tableDictionaryExtFile1, 'r');
	}			
		
	$matches = 0;
    if($fp)
    {
         while($line = fgets($fp))
	     {
	    	if(preg_match('/\s*include\s*\(\s*[\'|\"](.*?)[\'\"]\s*\)\s*;/', $line, $match))
	    	{
	    	   $matches++;
	    	   $this->assertTrue(file_exists($match[1]), 'Assert that entry for file ' . $line . ' exists');
	    	}
	     }  
		 fclose($fp); 
   }	
   
   $this->assertEquals($matches, 1, 'Assert that there was one match for correct entries in file ' . $this->tableDictionaryExtFile1);

   
	if(function_exists('sugar_fopen'))
	{
		$fp = @sugar_fopen($this->tableDictionaryExtFile2, 'r');
	} else {
		$fp = fopen($this->tableDictionaryExtFile2, 'r');
	}			
		
	$matches = 0;
    if($fp)
    {
         while($line = fgets($fp))
	     {
	    	if(preg_match('/\s*include\s*\(\s*[\'|\"](.*?)[\'\"]\s*\)\s*;/', $line, $match))
	    	{
	    	   $matches++;
	    	   $this->assertTrue(file_exists($match[1]), 'Assert that entry for file ' . $line . ' exists');
	    	}
	     }  
		 fclose($fp); 
   }	
   
   $this->assertEquals($matches, 1, 'Assert that there was one match for correct entries in file ' . $this->tableDictionaryExtFile2);
      
   
	if(function_exists('sugar_fopen'))
	{
		$fp = @sugar_fopen($this->corruptExtModuleFile, 'r');
	} else {
		$fp = fopen($this->corruptExtModuleFile, 'r');
	}			
		
	$matches = 0;
    if($fp)
    {
         while($line = fgets($fp))
	     {
	    	if(preg_match('/\s*include\s*\(\s*[\'|\"](.*?)[\'\"]\s*\)\s*;/', $line, $match))
	    	{
	    	   $matches++;
	    	   $this->assertTrue(file_exists($match[1]), 'Assert that entry for file ' . $line . ' exists');
	    	}
	     }  
		 fclose($fp); 
   }	
   
   $this->assertEquals($matches, 0, 'Assert that there was one match for correct entries in file ' . $this->corruptExtModuleFile);   
   
}


}

/**
 * repairTableDictionaryExtFile
 * 
 * There were some scenarios in 6.0.x whereby the files loaded in the extension tabledictionary.ext.php file 
 * did not exist.  This would cause warnings to appear during the upgrade.  As a result, this
 * function scans the contents of tabledictionary.ext.php and then remove entries where the file does exist.
 */
function repairTableDictionaryExtFile()
{
	$tableDictionaryExtDirs = array('custom/Extension/application/Ext/TableDictionary', 'custom/application/Ext/TableDictionary');
	
	foreach($tableDictionaryExtDirs as $tableDictionaryExt)
	{
	
		if(is_dir($tableDictionaryExt) && is_writable($tableDictionaryExt)){
			$dir = dir($tableDictionaryExt);
			while(($entry = $dir->read()) !== false)
			{
				$entry = $tableDictionaryExt . '/' . $entry;
				if(is_file($entry) && preg_match('/\.php$/i', $entry) && is_writeable($entry))
				{
			
						if(function_exists('sugar_fopen'))
						{
							$fp = @sugar_fopen($entry, 'r');
						} else {
							$fp = fopen($entry, 'r');
						}			
						
						
					    if($fp)
				        {
				             $altered = false;
				             $contents = '';
						     
				             while($line = fgets($fp))
						     {
						    	if(preg_match('/\s*include\s*\(\s*[\'|\"](.*?)[\"|\']\s*\)\s*;/', $line, $match))
						    	{
						    	   if(!file_exists($match[1]))
						    	   {
						    	      $altered = true;
						    	   } else {
						    	   	  $contents .= $line;
						    	   }
						    	} else {
						    	   $contents .= $line;
						    	}
						     }
						     
						     fclose($fp); 
				        }
				        
				        
					    if($altered)
					    {
							if(function_exists('sugar_fopen'))
							{
								$fp = @sugar_fopen($entry, 'w');
							} else {
								$fp = fopen($entry, 'w');
							}		    	
				            
							if($fp && fwrite($fp, $contents))
							{
								fclose($fp);
							}
					    }					
				} //if
			} //while
		} //if
	}
}

?>