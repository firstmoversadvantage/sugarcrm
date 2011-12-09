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


class Bug44831Test extends Sugar_PHPUnit_Framework_OutputTestCase
{
    public function setUp()
    {
        $GLOBALS['current_user'] = SugarTestUserUtilities::createAnonymousUser();
        $GLOBALS['app_list_strings'] = return_app_list_strings_language($GLOBALS['current_language']);
        $GLOBALS['app_strings'] = return_application_language($GLOBALS['current_language']);

        // Create a Custom editviewdefs.php
        sugar_mkdir("custom/modules/Leads/metadata/",null,true);

        if ( is_dir("cache/modules/Leads") )
            rmdir_recursive("cache/modules/Leads");

        if (file_exists("custom/modules/Leads/metadata/editviewdefs.php")) 
            unlink("custom/modules/Leads/metadata/editviewdefs.php");

        // Create a very simple custom EditView Layout
        if( $fh = @fopen("custom/modules/Leads/metadata/editviewdefs.php", 'w+') ) 
        {
$string = <<<EOQ
<?php
\$viewdefs['Leads']['EditView'] = array('templateMeta' => array (
                                                                 'form' => array('buttons' => array ('SAVE', 'CANCEL'),
                                                                                 'hidden' => array ('<a>HiddenPlaceHolder</a>',
                                                                                                   ),
                                                                                ),
                                                                 'maxColumns' => '2', 
                                                                 'useTabs' => true,
                                                                 'widths' => array( array ('label' => '10', 'field' => '30'),
                                                                                    array ('label' => '10', 'field' => '30'),
                                                                                  ),
                                                                 'javascript' => array( array ('file' => 'custom/modules/Leads/javascript/LeadJS1.js'),
                                                                                        array ('file' => 'custom/modules/Leads/javascript/LeadJS2.js'),
                                                                                      ),
                                                                ),
                                        'panels' => array ('default' => array (0 => array (0 => array ('name' => 'first_name',
                                                                                                      ),
                                                                                           1 => array ('name' => 'last_name',
                                                                                                      ),
                                                                                          ),
                                                                               1 => array (0 => array ('name' => 'unknown_field',
                                                                                                       'customCode' => '<a href="#">Unknown Field Link</a>',
                                                                                                      ),
                                                                                          ),
                                                                              ),
                                                          ),  
                                       );
?>
EOQ;
            fputs( $fh, $string);
            fclose( $fh );
        }


    }
    
    public function tearDown()
    {
        if ( is_dir("cache/modules/Leads") )
            rmdir_recursive("cache/modules/Leads");

        if (file_exists("custom/modules/Leads/metadata/editviewdefs.php")) 
            unlink("custom/modules/Leads/metadata/editviewdefs.php");

        SugarTestUserUtilities::removeAllCreatedAnonymousUsers();
        unset($GLOBALS['app_list_strings']);
        unset($GLOBALS['current_user']);
    }
    
    /**
    * @group bug44831
    */
    public function testJSInjection()
    {
    	$this->markTestSkipped('Marked as skipped for now... too problematic');
    	return;
        require_once('include/utils/layout_utils.php');
        $_SERVER['REQUEST_METHOD'] = "POST";

        $lead = SugarTestLeadUtilities::createLead();
        $lead->name = 'LeadName';
        $lead->save();
        
        $_REQUEST['module'] = 'Leads';
        $_REQUEST['action'] = 'EditView';
        $_REQUEST['record'] = $lead->id;
        
        require_once('include/MVC/Controller/ControllerFactory.php');
        require_once('include/MVC/View/ViewFactory.php');
        $GLOBALS['app']->controller = ControllerFactory::getController($_REQUEST['module']);
        //ob_start();
        $GLOBALS['app']->controller->execute();
        //$tStr = ob_get_clean();
        
        // First of all, need to be sure that I'm actually dealing with my new custom DetailView Layout
        $this->expectOutputRegex('/.*HiddenPlaceHolder.*/');
        // Then check inclusion of LeadJS1.js
        $this->expectOutputRegex('/.*<script src=\"custom\/modules\/Leads\/javascript\/LeadJS1\.js.*\"><\/script>.*/');
        // Then check inclusion of LeadJS2.js
        $this->expectOutputRegex('/.*<script src=\"custom\/modules\/Leads\/javascript\/LeadJS2\.js.*\"><\/script>.*/');
        
        //unset($GLOBALS['app']->controller);
        unset($_REQUEST['module']);
        unset($_REQUEST['action']);
        unset($_REQUEST['record']);
        SugarTestLeadUtilities::removeAllCreatedLeads();
    }
}
