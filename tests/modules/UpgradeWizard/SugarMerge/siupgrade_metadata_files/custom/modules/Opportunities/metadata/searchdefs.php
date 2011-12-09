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

$searchdefs ['Opportunities'] = 
array (
  'layout' => 
  array (
    'basic_search' => 
    array (
      0 => 'name',
      1 => 'opportunity_type',
      2 => 'account_name',
      3 => 
      array (
        'name' => 'current_user_only',
        'label' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool',
      ),
    ),
    'advanced_search' => 
    array (
      'name' => 
      array (
        'name' => 'name',
        'label' => 'LBL_OPPORTUNITY_NAME',
        'default' => true,
        'width' => '10%',
      ),
      'amount' => 
      array (
        'name' => 'amount',
        'label' => 'LBL_AMOUNT',
        'default' => true,
        'currency_format' => true,
        'width' => '10%',
      ),
      'account_name' => 
      array (
        'name' => 'account_name',
        'label' => 'LBL_ACCOUNT_NAME',
        'default' => true,
        'width' => '10%',
      ),
      'date_closed' => 
      array (
        'name' => 'date_closed',
        'label' => 'LBL_DATE_CLOSED',
        'default' => true,
        'width' => '10%',
      ),
      'opportunity_type' => 
      array (
        'width' => '10%',
        'label' => 'LBL_TYPE',
        'sortable' => false,
        'default' => true,
        'name' => 'opportunity_type',
      ),
      'lead_source' => 
      array (
        'name' => 'lead_source',
        'label' => 'LBL_LEAD_SOURCE',
        'default' => true,
        'sortable' => false,
        'width' => '10%',
      ),
      'next_step' => 
      array (
        'name' => 'next_step',
        'label' => 'LBL_NEXT_STEP',
        'default' => true,
        'width' => '10%',
      ),
      'sales_stage' => 
      array (
        'name' => 'sales_stage',
        'label' => 'LBL_SALES_STAGE',
        'default' => true,
        'sortable' => false,
        'width' => '10%',
      ),
      'probability' => 
      array (
        'name' => 'probability',
        'label' => 'LBL_PROBABILITY',
        'default' => true,
        'width' => '10%',
      ),
      'assigned_user_id' => 
      array (
        'name' => 'assigned_user_id',
        'type' => 'enum',
        'label' => 'LBL_ASSIGNED_TO',
        'function' => 
        array (
          'name' => 'get_user_array',
          'params' => 
          array (
            0 => false,
          ),
        ),
        'default' => true,
        'sortable' => false,
        'width' => '10%',
      ),
      'partner_assigned_to_c' => 
      array (
        'width' => '10%',
        'label' => 'Partner_Assigned_To_c',
        'default' => true,
        'name' => 'partner_assigned_to_c',
      ),
      'order_number' => 
      array (
        'width' => '10%',
        'label' => 'LBL_ORDER_NUMBER',
        'default' => true,
        'name' => 'order_number',
      ),
    ),
  ),
  'templateMeta' => 
  array (
    'maxColumns' => '3',
    'widths' => 
    array (
      'label' => '10',
      'field' => '30',
    ),
  ),
);
?>
