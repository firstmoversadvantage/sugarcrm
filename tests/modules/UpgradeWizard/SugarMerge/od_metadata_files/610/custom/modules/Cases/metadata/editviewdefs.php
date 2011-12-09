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


$viewdefs ['Cases'] = 
array (
  'EditView' => 
  array (
    'templateMeta' => 
    array (
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'form' => 
      array (
        'footerTpl' => 'custom/modules/Cases/tpls/EditViewFooter.tpl',
      ),
      'useTabs' => false,
    ),
    'panels' => 
    array (
      'lbl_case_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'case_number',
            'type' => 'readonly',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'priority',
            'comment' => 'The priority of the case',
            'label' => 'LBL_PRIORITY',
          ),
          1 => 
          array (
            'name' => 'ticket_due_date_c',
            'label' => 'LBL_TICKET_DUE_DATE',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'status',
            'comment' => 'The status of the case',
            'label' => 'LBL_STATUS',
          ),
          1 => 
          array (
            'name' => 'account_name',
            'comment' => 'The name of the account represented by the account_id field',
            'label' => 'LBL_ACCOUNT_NAME',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'type',
            'comment' => 'The type of issue (ex: issue, feature)',
            'label' => 'LBL_TYPE',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'displayParams' => 
            array (
              'size' => 75,
            ),
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'nl2br' => true,
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'resolution',
            'nl2br' => true,
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'portal_viewable',
            'customLabel' => '{if ($PORTAL_ENABLED)}{sugar_translate label="LBL_SHOW_IN_PORTAL" module="Cases"}{/if}',
            'customCode' => ' {if ($PORTAL_ENABLED)}
								{if $fields.portal_viewable.value == "1"}
								{assign var="checked" value="CHECKED"}
								{else}
								{assign var="checked" value=""}
									{/if}
								<input type="hidden" name="{$fields.portal_viewable.name}" value="0"> 
								<input type="checkbox" name="{$fields.portal_viewable.name}" value="1" tabindex="1" {$checked}>
		        		        {/if}',
          ),
          1 => 
          array (
            'name' => 'tick_email_on_close_c',
            'label' => 'LBL_TICK_EMAIL_ON_CLOSE',
          ),
        ),
      ),
      'LBL_PANEL_ASSIGNMENT' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ),
          1 => 
          array (
            'name' => 'team_name',
            'displayParams' => 
            array (
              'required' => true,
            ),
          ),
        ),
      ),
      'lbl_editview_panel1' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'contact_c',
            'studio' => 'visible',
            'label' => 'LBL_CONTACT_C',
            'displayParams' => 
            array (
              'call_back_function' => 'setAccountInfo',
            ),
          ),
          1 => '',
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'account_address_street',
            'hideLabel' => true,
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'account',
              'rows' => 2,
              'cols' => 30,
              'maxlength' => 150,
            ),
          ),
          1 => '',
        ),
      ),
    ),
  ),
);
?>