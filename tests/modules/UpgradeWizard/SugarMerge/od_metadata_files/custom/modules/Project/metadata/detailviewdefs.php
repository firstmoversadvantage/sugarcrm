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

$viewdefs['Project']['DetailView'] = array (
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
    'includes' => 
    array (
      0 => 
      array (
        'file' => 'modules/Project/Project.js',
      ),
    ),
    'form' => 
    array (
      'buttons' => 
      array (
        0 => 
        array (
          'customCode' => '<input title="{$APP.LBL_EDIT_BUTTON_TITLE}" accessKey="{$APP.LBL_EDIT_BUTTON_KEY}" class="button" type="submit" name="Edit" id="edit_button" value="{$APP.LBL_EDIT_BUTTON_LABEL}"{if $IS_TEMPLATE}onclick="this.form.return_module.value=\'Project\'; this.form.return_action.value=\'ProjectTemplatesDetailView\'; this.form.return_id.value=\'{$id}\'; this.form.action.value=\'ProjectTemplatesEditView\';"{else}onclick="this.form.return_module.value=\'Project\'; this.form.return_action.value=\'DetailView\'; this.form.return_id.value=\'{$id}\'; this.form.action.value=\'EditView\';" {/if}"/>',
        ),
        1 => 
        array (
          'customCode' => '<input title="{$APP.LBL_DELETE_BUTTON_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="button" type="submit" name="Delete" id="delete_button" value="{$APP.LBL_DELETE_BUTTON_LABEL}"{if $IS_TEMPLATE}onclick="this.form.return_module.value=\'Project\'; this.form.return_action.value=\'ProjectTemplatesListView\'; this.form.action.value=\'Delete\'; return confirm(\'{$APP.NTC_DELETE_CONFIRMATION}\');"{else}onclick="this.form.return_module.value=\'Project\'; this.form.return_action.value=\'ListView\'; this.form.action.value=\'Delete\'; return confirm(\'{$APP.NTC_DELETE_CONFIRMATION}\');" {/if}"/>',
        ),
        2 => 
        array (
          'customCode' => '{if $EDIT_RIGHTS_ONLY}<input title="{$MOD.LBL_VIEW_GANTT_TITLE}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="button" type="submit" name="EditProjectTasks" value="  {$MOD.LBL_VIEW_GANTT_TITLE}  " onclick="prep_edit_project_tasks(this.form);" />{/if}',
        ),
        3 => 
        array (
          'customCode' => '<input title="{$SAVE_AS}" accessKey="{$APP.LBL_DELETE_BUTTON_KEY}" class="button" type="submit" name="SaveAsTemplate" value="{$SAVE_AS}"{if $IS_TEMPLATE}onclick="prep_save_as_project(this.form)"{else}onclick="prep_save_as_template(this.form) {/if}"/>',
        ),
        4 => 
        array (
          'customCode' => '<input title="{$MOD.LBL_EXPORT_TO_MS_PROJECT}" class="button" type="submit" name="ExportToProject" value="  {$MOD.LBL_EXPORT_TO_MS_PROJECT}  " onclick="prep_export_to_project(this.form);"/>',
        ),
      ),
    ),
  ),
  'panels' => 
  array (
    'lbl_panel_1' => 
    array (
      0 => 
      array (
        0 => 
        array (
          'name' => 'name',
          'label' => 'LBL_NAME',
        ),
        1 => 
        array (
          'name' => 'status',
          'label' => 'LBL_STATUS',
        ),
      ),
      1 => 
      array (
        0 => 
        array (
          'name' => 'estimated_start_date',
          'label' => 'LBL_DATE_START',
        ),
        1 => 
        array (
          'name' => 'estimated_end_date',
          'label' => 'LBL_DATE_END',
        ),
      ),
      2 => 
      array (
        0 => 
        array (
          'name' => 'assigned_user_name',
          'label' => 'LBL_ASSIGNED_USER_ID',
        ),
        1 => 
        array (
          'name' => 'team_name',
        ),
      ),
      3 => 
      array (
        0 => 
        array (
          'name' => 'priority',
          'label' => 'LBL_PRIORITY',
        ),
      ),
      4 => 
      array (
        0 => 
        array (
          'name' => 'description',
          'label' => 'LBL_DESCRIPTION',
        ),
        1 => NULL,
      ),
      5 => 
      array (
        0 => 
        array (
          'name' => 'kiosk_kiosk_project_name',
        ),
      ),
    ),
  ),
);
?>
