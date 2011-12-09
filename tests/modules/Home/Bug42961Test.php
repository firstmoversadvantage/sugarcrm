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


require_once 'modules/Home/UnifiedSearchAdvanced.php';

/**
 * @brief Try to find force_unifedsearch fields
 * @ticket 42961
 */
class Bug42961Test extends Sugar_PHPUnit_Framework_TestCase
{

    /**
     * @brief generation of new cache file and search for force_unifiedsearch fields in it
     * @group 42961
     */
    public function testBuildCache()
    {
        $beanList = array();
        $beanFiles = array();
        require('include/modules.php');
        $GLOBALS['beanList'] = $beanList;
        $GLOBALS['beanFiles'] = $beanFiles;
        $unifiedSearchAdvanced = new UnifiedSearchAdvanced();
        $unifiedSearchAdvanced->buildCache();
        $this->assertFileExists($GLOBALS['sugar_config']['cache_dir'].'modules/unified_search_modules.php', 'Here should be cache file with data');
        include $GLOBALS['sugar_config']['cache_dir'].'modules/unified_search_modules.php';
        $force_unifiedsearch = 0;
        foreach ($unified_search_modules as $moduleName=>$moduleInformation)
        {
            foreach ($moduleInformation['fields'] as $fieldName=>$fieldInformation)
            {
                if (key_exists('force_unifiedsearch', $fieldInformation)) {
                    $force_unifiedsearch++;
                }
            }
        }
        $this->assertGreaterThan(0, $force_unifiedsearch, 'Here should be fields with force_unifiedsearch key');
    }
}