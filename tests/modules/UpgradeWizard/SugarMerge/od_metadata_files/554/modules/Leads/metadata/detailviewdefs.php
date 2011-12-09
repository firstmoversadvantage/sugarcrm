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

$viewdefs ['Leads'] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 
          array (
            'customCode' => '<input title="{$MOD.LBL_CONVERTLEAD_TITLE}" accessKey="{$MOD.LBL_CONVERTLEAD_BUTTON_KEY}" type="button" class="button" onClick="document.location=\'index.php?module=Leads&action=ConvertLead&record={$fields.id.value}\'" name="convert" value="{$MOD.LBL_CONVERTLEAD}">',
          ),
          4 => 
          array (
            'customCode' => '<input title="{$APP.LBL_DUP_MERGE}" accessKey="M" class="button" onclick="this.form.return_module.value=\'Leads\'; this.form.return_action.value=\'DetailView\';this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'Step1\'; this.form.module.value=\'MergeRecords\';" type="submit" name="Merge" value="{$APP.LBL_DUP_MERGE}">',
          ),
          5 => 
          array (
            'customCode' => '<input title="{$APP.LBL_MANAGE_SUBSCRIPTIONS}" class="button" onclick="this.form.return_module.value=\'Leads\'; this.form.return_action.value=\'DetailView\';this.form.return_id.value=\'{$fields.id.value}\'; this.form.action.value=\'Subscriptions\'; this.form.module.value=\'Campaigns\'; this.form.module_tab.value=\'Leads\';" type="submit" name="Manage Subscriptions" value="{$APP.LBL_MANAGE_SUBSCRIPTIONS}">',
          ),
        ),
        'headerTpl' => 'modules/Leads/tpls/DetailViewHeader.tpl',
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
      0 => 
      array (
        0 => 'lead_source',
        1 => 'status',
      ),
      1 => 
      array (
        0 => 'lead_source_description',
        1 => 'status_description',
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'campaign_name',
          'label' => 'LBL_CAMPAIGN',
        ),
        1 => 'opportunity_amount',
      ),
      3 => 
      array (
        0 => 'refered_by',
        1 => 
        array (
          'name' => 'phone_work',
          'label' => 'LBL_OFFICE_PHONE',
          'customCode' => '{fonality_phone value=$fields.phone_work.value this_module=Leads this_id=$fields.id.value}',
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'full_name',
          'label' => 'LBL_NAME',
        ),
        1 => 
        array (
          'name' => 'phone_mobile',
          'label' => 'LBL_MOBILE_PHONE',
          'customCode' => '{fonality_phone value=$fields.phone_mobile.value this_module=Leads this_id=$fields.id.value}',
        ),
      ),
      5 => 
      array (
        0 => 'birthdate',
        1 => 
        array (
          'name' => 'phone_home',
          'label' => 'LBL_HOME_PHONE',
          'customCode' => '{fonality_phone value=$fields.phone_home.value this_module=Leads this_id=$fields.id.value}',
        ),
      ),
      6 => 
      array (
        0 => 'account_name',
        1 => 
        array (
          'name' => 'phone_other',
          'label' => 'LBL_OTHER_PHONE',
          'customCode' => '{fonality_phone value=$fields.phone_other.value this_module=Leads this_id=$fields.id.value}',
        ),
      ),
      7 => 
      array (
        0 => 'title',
        1 => 
        array (
          'name' => 'phone_fax',
          'label' => 'LBL_FAX_PHONE',
          'customCode' => '{fonality_phone value=$fields.phone_fax.value this_module=Leads this_id=$fields.id.value}',
        ),
      ),
      8 => 
      array (
        0 => 'department',
        1 => 'do_not_call',
      ),
      9 => 
      array (
        0 => 'team_name',
        1 => 
        array (
          'name' => 'date_modified',
          'label' => 'LBL_DATE_MODIFIED',
          'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
        ),
      ),
      10 => 
      array (
      ),
      11 => 
      array (
        0 => 
        array (
          'name' => 'assigned_user_name',
          'label' => 'LBL_ASSIGNED_TO',
        ),
        1 => 
        array (
          'name' => 'created_by',
          'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}&nbsp;',
          'label' => 'LBL_DATE_ENTERED',
        ),
      ),
      12 => 
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
      13 => 
      array (
        0 => 'description',
        1 => '',
      ),
      14 => 
      array (
        0 => 'email1',
      ),
    ),
  ),
);
?>
