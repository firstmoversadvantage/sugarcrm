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

$viewdefs = array (
  'Contacts' => 
  array (
    'DetailView' => 
    array (
      'templateMeta' => 
      array (
        'preForm' => '<form name="vcard" action="index.php"><input type="hidden" name="entryPoint" value="vCard"><input type="hidden" name="contact_id" value="{$fields.id.value}"><input type="hidden" name="module" value="Contacts"></form>',
        'form' => 
        array (
          'buttons' => 
          array (
            0 => 'EDIT',
            1 => 'DUPLICATE',
            2 => 'DELETE',
            3 => 'FIND_DUPLICATES',
            4 => 
            array (
              'customCode' => '<input title="{$APP.LBL_MANAGE_SUBSCRIPTIONS}" class="button" onclick="this.form.return_module.value=\'Contacts\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'Subscriptions\'; this.form.module.value=\'Campaigns\';" type="submit" name="Manage Subscriptions" value="{$APP.LBL_MANAGE_SUBSCRIPTIONS}">',
            ),
          ),
        ),
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
        'includes' => 
        array (
          0 => 
          array (
            'file' => 'modules/Leads/Lead.js',
          ),
        ),
      ),
      'panels' => 
      array (
        'default' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'name' => 'full_name',
              'customCode' => '{$fields.full_name.value}&nbsp;&nbsp;<input type="button" class="button" name="vCardButton" value="{$MOD.LBL_VCARD}" onClick="document.vcard.submit();">',
              'label' => 'LBL_NAME',
              'displayParams' => 
              array (
              ),
            ),
            1 => 
            array (
              'name' => 'phone_work',
              'label' => 'LBL_OFFICE_PHONE',
            ),
          ),
          1 => 
          array (
            0 => 'score_c',
            1 => 
            array (
              'name' => 'phone_mobile',
              'label' => 'LBL_MOBILE_PHONE',
            ),
          ),
          2 => 
          array (
            0 => 
            array (
              'name' => 'account_name',
              'label' => 'LBL_ACCOUNT_NAME',
            ),
            1 => 
            array (
              'name' => 'phone_home',
              'label' => 'LBL_HOME_PHONE',
            ),
          ),
          3 => 
          array (
            0 => 
            array (
              'name' => 'lead_source',
              'label' => 'LBL_LEAD_SOURCE',
            ),
            1 => 
            array (
              'name' => 'phone_other',
              'label' => 'LBL_OTHER_PHONE',
            ),
          ),
          4 => 
          array (
            0 => 
            array (
              'name' => 'title',
              'label' => 'LBL_TITLE',
            ),
            1 => 
            array (
              'name' => 'phone_fax',
              'label' => 'LBL_FAX_PHONE',
            ),
          ),
          5 => 
          array (
            0 => 
            array (
              'name' => 'department',
              'label' => 'LBL_DEPARTMENT',
            ),
            1 => 
            array (
              'name' => 'email1',
              'label' => 'LBL_EMAIL_ADDRESS',
            ),
          ),
          6 => 
          array (
            0 => 
            array (
              'name' => 'birthdate',
              'label' => 'LBL_BIRTHDATE',
            ),
            1 => NULL,
          ),
          7 => 
          array (
            0 => 
            array (
              'name' => 'report_to_name',
              'label' => 'LBL_REPORTS_TO',
            ),
            1 => 
            array (
              'name' => 'assistant',
              'label' => 'LBL_ASSISTANT',
            ),
          ),
          8 => 
          array (
            0 => 
            array (
              'name' => 'technical_proficiency_',
              'label' => 'LBL_TECHNICAL_PROFICIENCY_',
            ),
            1 => 
            array (
              'name' => 'assistant_phone',
              'label' => 'LBL_ASSISTANT_PHONE',
            ),
          ),
          9 => 
          array (
            0 => 
            array (
              'name' => 'do_not_call',
              'label' => 'LBL_DO_NOT_CALL',
            ),
            1 => NULL,
          ),
          10 => 
          array (
            0 => 
            array (
              'name' => 'sync_contact',
              'label' => 'LBL_SYNC_CONTACT',
            ),
            1 => NULL,
          ),
          11 => 
          array (
            0 => NULL,
            1 => 
            array (
              'name' => 'primary_business_c',
              'label' => 'Primary_Business_Contact__c',
            ),
          ),
          12 => 
          array (
            0 => NULL,
            1 => 
            array (
              'name' => 'support_authorized_c',
              'label' => 'Support_Authorized_Contact__c',
            ),
          ),
          13 => 
          array (
            0 => NULL,
            1 => 
            array (
              'name' => 'university_enabled_c',
              'label' => 'LBL_UNIVERSITY_ENABLED',
            ),
          ),
          14 => 
          array (
            0 => NULL,
            1 => 
            array (
              'name' => 'billing_contact_c',
              'label' => 'Billing_Contact__c',
            ),
          ),
          15 => 
          array (
            0 => NULL,
            1 => 
            array (
              'name' => 'oppq_active_c',
              'label' => 'LBL_OPPQ_ACTIVE_C',
            ),
          ),
          16 => 
          array (
            0 => 
            array (
              'name' => 'team_name',
              'label' => 'LBL_TEAM',
            ),
            1 => 
            array (
              'name' => 'date_modified',
              'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
              'label' => 'LBL_DATE_MODIFIED',
            ),
          ),
          17 => 
          array (
            0 => 
            array (
              'name' => 'assigned_user_name',
              'label' => 'LBL_ASSIGNED_TO_NAME',
            ),
            1 => 
            array (
              'name' => 'date_entered',
              'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
              'label' => 'LBL_DATE_ENTERED',
            ),
          ),
          18 => 
          array (
            0 => 
            array (
              'name' => 'primary_address_street',
              'label' => 'LBL_PRIMARY_ADDRESS',
              'type' => 'address',
              'displayParams' => 
              array (
                'key' => 'primary',
              ),
            ),
            1 => 
            array (
              'name' => 'alt_address_street',
              'label' => 'LBL_ALTERNATE_ADDRESS',
              'type' => 'address',
              'displayParams' => 
              array (
                'key' => 'alt',
              ),
            ),
          ),
          19 => 
          array (
            0 => 
            array (
              'name' => 'portal_name',
              'customCode' => '{if $PORTAL_ENABLED}{$fields.portal_name.value}{/if}',
              'customLabel' => '{if $PORTAL_ENABLED}{sugar_translate label="LBL_PORTAL_NAME" module="Contacts"}{/if}',
              'label' => 'LBL_PORTAL_NAME',
            ),
            1 => 
            array (
              'name' => 'portal_active',
              'customCode' => '{if $PORTAL_ENABLED}
	          		         {if strval($fields.portal_active.value) == "1" || strval($fields.portal_active.value) == "yes" || strval($fields.portal_active.value) == "on"}
	          		         {assign var="checked" value="CHECKED"}
                             {else}
                             {assign var="checked" value=""}
                             {/if}
                             <input type="checkbox" class="checkbox" name="{$fields.portal_active.name}" size="{$displayParams.size}" disabled="true" {$checked}>
                             {/if}',
              'customLabel' => '{if $PORTAL_ENABLED}{sugar_translate label="LBL_PORTAL_ACTIVE" module="Contacts"}{/if}',
              'label' => 'LBL_PORTAL_ACTIVE',
            ),
          ),
          20 => 
          array (
            0 => 
            array (
              'name' => 'description',
              'label' => 'LBL_DESCRIPTION',
            ),
          ),
        ),
        'lbl_panel1' => 
        array (
          0 => 
          array (
            0 => 
            array (
              'name' => 'dce_user_name_c',
              'label' => 'LBL_DCE_USER_NAME',
            ),
            1 => 
            array (
              'name' => 'licensing_rights_c',
              'label' => 'LBL_LICENSING_RIGHTS',
            ),
          ),
        ),
      ),
    ),
  ),
);
?>
