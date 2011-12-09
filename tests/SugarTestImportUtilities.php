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

 
require_once 'modules/Import/ImportCacheFiles.php';

class SugarTestImportUtilities
{
    public static  $_createdFiles = array();

    private function __construct() {}

    public function __destruct()
    {
        self::removeAllCreatedFiles();
    }

    public static function createFile($lines = 2000,$columns = 3, $dir = 'upload_dir')
    {
        $filename = $GLOBALS['sugar_config'][$dir].'test'. uniqid();
        $fp = fopen($filename,"w");
        for ($i = 0; $i < $lines; $i++) {
            $line = array();
            for ($j = 0; $j < $columns; $j++)
                $line[] = "foo{$i}{$j}";
            fputcsv($fp,$line);
        }
        fclose($fp);
        
        self::$_createdFiles[] = $filename;
        
        return $filename;
    }
	
    public static function createFileWithEOL(
        $lines = 2000,
        $columns = 3
        ) 
    {
        $filename = $GLOBALS['sugar_config']['upload_dir'].'test'.date("YmdHis");
        $fp = fopen($filename,"w");
        for ($i = 0; $i < $lines; $i++) {
            $line = array();
            for ($j = 0; $j < $columns; $j++) {
            	// test both end of lines: \r\n (windows) and \n (unix)
                $line[] = "start{$i}\r\n{$j}\nend";
            }
            fputcsv($fp,$line);
        }
        fclose($fp);
        
        self::$_createdFiles[] = $filename;
        
        return $filename;
    }
	
    public static function createFileWithWhiteSpace() 
    {
        $filename = $GLOBALS['sugar_config']['upload_dir'].'testWhiteSpace'.date("YmdHis");
        $contents = <<<EOTEXT
account2,foo bar
EOTEXT;
        file_put_contents($filename, $contents);
        
        self::$_createdFiles[] = $filename;
        
        return $filename;
    }
    
    public static function removeAllCreatedFiles()
    {
        foreach ( self::$_createdFiles as $file ) {
            @unlink($file);
            $i = 0;
            while(true) {
                if ( is_file($file.'-'.$i) ) 
                    unlink($file.'-'.$i++);
                else 
                    break;
            }
        }
        ImportCacheFiles::clearCacheFiles();
    }
}
