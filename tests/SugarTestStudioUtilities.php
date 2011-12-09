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

 
require_once('include/MVC/Controller/SugarController.php');
require_once('modules/ModuleBuilder/controller.php');
require_once('modules/ModuleBuilder/parsers/ParserFactory.php');

class SugarTestStudioUtilities
{
    private static $_fieldsAdded = array();

    private function __construct() {}
    
    /*
     * $module_name should be the module name (Contacts, Leads, etc)
     * $view should be the layout (editview, detailview, etc)
     * $field_name should be the name of the field being added
     */
    public static function addFieldToLayout($module_name, $view, $field_name) 
    {
        $parser = ParserFactory::getParser($view, $module_name);
        $parser->addField(array('name' => $field_name));
        //$parser->writeWorkingFile();
        $parser->handleSave(false);
        unset($parser);
        
        self::$_fieldsAdded[$module_name][$view][$field_name] = $field_name;
    }
    
    public static function removeAllCreatedFields()
    {
        foreach(self::$_fieldsAdded as $module_name => $views)
        {
            foreach($views as $view => $fields)
            {
                $parser = ParserFactory::getParser($view, $module_name);
                foreach($fields as $field_name)
                {
                    $parser->removeField($field_name);
                }
                $parser->handleSave(false);
                unset($parser);
            }
        }
    }

}
?>