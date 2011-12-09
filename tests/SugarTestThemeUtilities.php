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

 
require_once 'include/SugarTheme/SugarTheme.php';
require_once 'include/dir_inc.php';

class SugarTestThemeUtilities
{
    private static  $_createdThemes = array();

    private function __construct() {}

    public static function createAnonymousTheme() 
    {
        $themename = 'TestTheme'.mt_rand();
        
        sugar_mkdir("themes/$themename/images",null,true);
        sugar_mkdir("themes/$themename/css",null,true);
        sugar_mkdir("themes/$themename/js",null,true);
        sugar_mkdir("themes/$themename/tpls",null,true);
        
        sugar_file_put_contents("themes/$themename/css/style.css","h2 { display: inline; }");
        sugar_file_put_contents("themes/$themename/css/yui.css",".yui { display: inline; }");
        sugar_file_put_contents("themes/$themename/js/style.js",'var dog = "cat";');
        sugar_touch("themes/$themename/images/Accounts.gif");
        sugar_touch("themes/$themename/images/fonts.big.icon.gif");
        sugar_touch("themes/$themename/tpls/header.tpl");
        
        $themedef = "<?php\n";
        $themedef .= "\$themedef = array(\n";
        $themedef .= "'name'  => '$themename',";
        $themedef .= "'dirName'  => '$themename',";
        $themedef .= "'description' => '$themename',";
        $themedef .= "'version' => array('regex_matches' => array('.*')),";
        $themedef .= ");";
        sugar_file_put_contents("themes/$themename/themedef.php",$themedef);
        
        self::$_createdThemes[] = $themename;
        
        SugarThemeRegistry::buildRegistry();        
        
        return $themename;
    }
    
    public static function createAnonymousOldTheme() 
    {
        $themename = 'TestTheme'.mt_rand();
        
        sugar_mkdir("themes/$themename/images",null,true);
        sugar_mkdir("themes/$themename/css",null,true);
        sugar_mkdir("themes/$themename/js",null,true);
        sugar_mkdir("themes/$themename/tpls",null,true);
        
        sugar_file_put_contents("themes/$themename/css/style.css","h2 { display: inline; }");
        sugar_file_put_contents("themes/$themename/css/yui.css",".yui { display: inline; }");
        sugar_file_put_contents("themes/$themename/js/style.js",'var dog = "cat";');
        sugar_touch("themes/$themename/images/Accounts.gif");
        sugar_touch("themes/$themename/images/fonts.big.icon.gif");
        sugar_touch("themes/$themename/tpls/header.tpl");
        
        $themedef = "<?php\n";
        $themedef .= "\$themedef = array(\n";
        $themedef .= "'name'  => '$themename',";
        $themedef .= "'dirName'  => '$themename',";
        $themedef .= "'description' => '$themename',";
        $themedef .= "'version' => array('exact_matches' => array('5.5.1')),";
        $themedef .= ");";
        sugar_file_put_contents("themes/$themename/themedef.php",$themedef);
        
        self::$_createdThemes[] = $themename;
        
        SugarThemeRegistry::buildRegistry();        
        
        return $themename;
    }
    
    public static function createAnonymousCustomTheme(
        $themename = ''
        )
    {
        if ( empty($themename) )
            $themename = 'TestThemeCustom'.mt_rand();
        
        create_custom_directory("themes/$themename/images/");
        create_custom_directory("themes/$themename/css/");
        create_custom_directory("themes/$themename/js/");
        
        sugar_touch("custom/themes/$themename/css/style.css");
        sugar_touch("custom/themes/$themename/js/style.js");
        sugar_touch("custom/themes/$themename/images/Accounts.gif");
        sugar_touch("custom/themes/$themename/images/fonts.big.icon.gif");
        
        $themedef = "<?php\n";
        $themedef .= "\$themedef = array(\n";
        $themedef .= "'name'  => 'custom $themename',";
        $themedef .= "'dirName'  => '$themename',";
        $themedef .= "'description' => 'custom $themename',";
        $themedef .= "'version' => array('regex_matches' => array('.*')),";
        $themedef .= ");";
        sugar_file_put_contents("custom/themes/$themename/themedef.php",$themedef);
        
        self::$_createdThemes[] = $themename;
        
        SugarThemeRegistry::buildRegistry();        
        
        return $themename;
    }
    
    public static function createAnonymousChildTheme(
        $parentTheme
        )
    {
        $themename = 'TestThemeChild'.mt_rand();
        
        sugar_mkdir("themes/$themename/images",null,true);
        sugar_mkdir("themes/$themename/css",null,true);
        sugar_mkdir("themes/$themename/js",null,true);
        
        sugar_file_put_contents("themes/$themename/css/style.css","h3 { display: inline; }");
        sugar_file_put_contents("themes/$themename/css/yui.css",".yui { display: inline; }");
        sugar_file_put_contents("themes/$themename/js/style.js",'var bird = "frog";');
        
        $themedef = "<?php\n";
        $themedef .= "\$themedef = array(\n";
        $themedef .= "'name'  => '$themename',";
        $themedef .= "'dirName' => '$themename',";
        $themedef .= "'parentTheme' => '".$parentTheme."',";
        $themedef .= "'description' => '$themename',";
        $themedef .= "'version' => array('regex_matches' => array('.*')),";
        $themedef .= ");";
        sugar_file_put_contents("themes/$themename/themedef.php",$themedef);
        
        self::$_createdThemes[] = $themename;
        
        SugarThemeRegistry::buildRegistry();        
        
        return $themename;
    }
    
    public static function createAnonymousRTLTheme() 
    {
        $themename = 'TestTheme'.mt_rand();
        
        sugar_mkdir("themes/$themename/images",null,true);
        sugar_mkdir("themes/$themename/css",null,true);
        sugar_mkdir("themes/$themename/js",null,true);
        sugar_mkdir("themes/$themename/tpls",null,true);
        
        sugar_file_put_contents("themes/$themename/css/style.css","h2 { display: inline; }");
        sugar_file_put_contents("themes/$themename/css/yui.css",".yui { display: inline; }");
        sugar_file_put_contents("themes/$themename/js/style.js",'var dog = "cat";');
        sugar_touch("themes/$themename/images/Accounts.gif");
        sugar_touch("themes/$themename/images/fonts.big.icon.gif");
        sugar_touch("themes/$themename/tpls/header.tpl");
        
        $themedef = "<?php\n";
        $themedef .= "\$themedef = array(\n";
        $themedef .= "'name'  => '$themename',";
        $themedef .= "'dirName'  => '$themename',";
        $themedef .= "'description' => '$themename',";
        $themedef .= "'directionality' => 'rtl',";
        $themedef .= "'version' => array('regex_matches' => array('.*')),";
        $themedef .= ");";
        sugar_file_put_contents("themes/$themename/themedef.php",$themedef);
        
        self::$_createdThemes[] = $themename;
        
        SugarThemeRegistry::buildRegistry();        
        
        return $themename;
    }

    public static function removeAllCreatedAnonymousThemes() 
    {
        foreach (self::getCreatedThemeNames() as $name ) {
            if ( is_dir('themes/'.$name) )
                rmdir_recursive('themes/'.$name);
            if ( is_dir('custom/themes/'.$name) )
                rmdir_recursive('custom/themes/'.$name);
            if ( is_dir('cache/themes/'.$name) )
                rmdir_recursive('cache/themes/'.$name);
        }
        
        SugarThemeRegistry::buildRegistry();
    }
    
    public static function getCreatedThemeNames() 
    {
        return self::$_createdThemes;
    }
}

