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

 
class SugarTestLangPackCreator
{
    public function __construct()
    {
    }
    
    public function __destruct()
    {
        $this->clearLangCache();
    }
    
    /**
     * Set a string for the app_strings array
     *
     * @param $key   string
     * @param $value string
     */
    public function setAppString(
        $key,
        $value
        )
    {
        $this->_strings['app_strings'][$key] = $value;
    }
    
    /**
     * Set a string for the app_list_strings array
     *
     * @param $key   string
     * @param $value string
     */
    public function setAppListString(
        $key,
        $value
        )
    {
        $this->_strings['app_list_strings'][$key] = $value;
    }
    
    /**
     * Set a string for the mod_strings array
     *
     * @param $key    string
     * @param $value  string
     * @param $module string
     */
    public function setModString(
        $key,
        $value,
        $module
        )
    {
        $this->_strings['mod_strings'][$module][$key] = $value;
    }
    
    /**
     * Saves the created strings
     *
     * Here, we cheat the system by storing our string overrides in the sugar_cache where
     * we normally stored the cached language strings.
     */
    public function save()
    {
        $language = $GLOBALS['current_language'];
        if ( isset($this->_strings['app_strings']) ) {
            $cache_key = 'app_strings.'.$language;
            $app_strings = sugar_cache_retrieve($cache_key);
            if ( empty($app_strings) )
                $app_strings = return_application_language($language);
            foreach ( $this->_strings['app_strings'] as $key => $value )
                $app_strings[$key] = $value;
            sugar_cache_put($cache_key, $app_strings);
            $GLOBALS['app_strings'] = $app_strings;
        }
        
        if ( isset($this->_strings['app_list_strings']) ) {
            $cache_key = 'app_list_strings.'.$language;
            $app_list_strings = sugar_cache_retrieve($cache_key);
            if ( empty($app_list_strings) )
                $app_list_strings = return_app_list_strings_language($language);
            foreach ( $this->_strings['app_list_strings'] as $key => $value )
                $app_list_strings[$key] = $value;
            sugar_cache_put($cache_key, $app_list_strings);
            $GLOBALS['app_list_strings'] = $app_list_strings;
        }
        
        if ( isset($this->_strings['mod_strings']) ) {
            foreach ( $this->_strings['mod_strings'] as $module => $strings ) {
                $cache_key = LanguageManager::getLanguageCacheKey($module, $language);
                $mod_strings = sugar_cache_retrieve($cache_key);
                if ( empty($mod_strings) )
                    $mod_strings = return_module_language($language, $module);
                foreach ( $strings as $key => $value )
                    $mod_strings[$key] = $value;
                sugar_cache_put($cache_key, $mod_strings);
                $GLOBALS['mod_strings'] = $mod_strings;
            }
        }
    }
    
    /**
     * Clear the language string cache in sugar_cache, which will get rid of our
     * language file overrides.
     */
    protected function clearLangCache()
    {
        $language = $GLOBALS['current_language'];
        
        if ( isset($this->_strings['app_strings']) ) {
            $cache_key = 'app_strings.'.$language;
            sugar_cache_clear($cache_key);
        }
        
        if ( isset($this->_strings['app_list_strings']) ) {
            $cache_key = 'app_list_strings.'.$language;
            sugar_cache_clear($cache_key);
        }
        
        if ( isset($this->_strings['mod_strings']) ) {
            foreach ( $this->_strings['mod_strings'] as $module => $strings ) {
                $cache_key = LanguageManager::getLanguageCacheKey($module, $language);
                sugar_cache_clear($cache_key);
            }
        }
    }
}
