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

$viewdefs ['Quotes'] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'closeFormBeforeCustomButtons' => true,
        'links' => 
        array (
          0 => '{$MOD.PDF_FORMAT} <select name="layout" id="layout">{$LAYOUT_OPTIONS}</select></form>',
        ),
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 
          array (
            'customCode' => '<form action="index.php" method="POST" name="Quote2Opp" id="form"><input type="hidden" name="module" value="Quotes"><input type="hidden" name="record" value="{$fields.id.value}"><input type="hidden" name="user_id" value="{$current_user->id}"><input type="hidden" name="team_id" value="{$fields.team_id.value}"><input type="hidden" name="user_name" value="{$current_user->user_name}"><input type="hidden" name="action" value="QuoteToOpportunity"><input type="hidden" name="opportunity_subject" value="{$fields.name.value}"><input type="hidden" name="opportunity_name" value="{$fields.name.value}"><input type="hidden" name="opportunity_id" value="{$fields.billing_account_id.value}"><input type="hidden" name="amount" value="{$fields.total.value}"><input type="hidden" name="valid_until" value="{$fields.date_quote_expected_closed.value}"><input type="hidden" name="currency_id" value="{$fields.currency_id.value}"><input title="{$APP.LBL_QUOTE_TO_OPPORTUNITY_TITLE}" accessKey="{$APP.LBL_QUOTE_TO_OPPORTUNITY_KEY}" class="button" type="submit" name="opp_to_quote_button" value="{$APP.LBL_QUOTE_TO_OPPORTUNITY_LABEL}"></form>',
          ),
          4 => 
          array (
            'customCode' => '<form action="index.php" method="{$PDFMETHOD}" name="ViewPDF" id="form"><input type="hidden" name="module" value="Quotes"><input type="hidden" name="record" value="{$fields.id.value}"><input type="hidden" name="action" value="Layouts"><input type="hidden" name="entryPoint" value="pdf"><input type="hidden" name="email_action"><input title="{$APP.LBL_EMAIL_PDF_BUTTON_TITLE}" accessKey="{$APP.LBL_EMAIL_PDF_BUTTON_KEY}" class="button" type="submit" name="button" value="{$APP.LBL_EMAIL_PDF_BUTTON_LABEL}" onclick="this.form.email_action.value=\'EmailLayout\';"> <input title="{$APP.LBL_VIEW_PDF_BUTTON_TITLE}" accessKey="{$APP.LBL_VIEW_PDF_BUTTON_KEY}" class="button" type="submit" name="button" value="{$APP.LBL_VIEW_PDF_BUTTON_LABEL}">',
          ),
        ),
        'footerTpl' => 'modules/Quotes/tpls/DetailViewFooter.tpl',
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
    ),
    'panels' => 
    array (
      'default' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'name',
            'label' => 'LBL_QUOTE_NAME',
          ),
          1 => 
          array (
            'name' => 'opportunity_name',
            'label' => 'LBL_OPPORTUNITY_NAME',
          ),
        ),
        1 => 
        array (
          0 => 
          array (
            'name' => 'quote_num',
            'label' => 'LBL_QUOTE_NUM',
          ),
          1 => 
          array (
            'name' => 'quote_stage',
            'label' => 'LBL_QUOTE_STAGE',
          ),
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'purchase_order_num',
            'label' => 'LBL_PURCHASE_ORDER_NUM',
          ),
          1 => 
          array (
            'name' => 'date_quote_expected_closed',
            'label' => 'LBL_DATE_QUOTE_EXPECTED_CLOSED',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'payment_terms',
            'label' => 'LBL_PAYMENT_TERMS',
          ),
          1 => 
          array (
            'name' => 'original_po_date',
            'label' => 'LBL_ORIGINAL_PO_DATE',
          ),
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'team_name',
            'label' => 'LBL_TEAM',
          ),
          1 => 
          array (
            'name' => 'date_entered',
            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
            'label' => 'LBL_DATE_ENTERED',
          ),
        ),
        5 => 
        array (
          0 => 
          array (
            'name' => 'assigned_user_name',
            'label' => 'LBL_ASSIGNED_TO_NAME',
          ),
          1 => 
          array (
            'name' => 'date_modified',
            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
            'label' => 'LBL_DATE_MODIFIED',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'billing_account_name',
            'label' => 'LBL_BILLING_ACCOUNT_NAME',
          ),
          1 => 
          array (
            'name' => 'shipping_account_name',
            'label' => 'LBL_SHIPPING_ACCOUNT_NAME',
          ),
        ),
        7 => 
        array (
          0 => 
          array (
            'name' => 'billing_contact_name',
            'label' => 'LBL_BILLING_CONTACT_NAME',
          ),
          1 => 
          array (
            'name' => 'shipping_contact_name',
            'label' => 'LBL_SHIPPING_CONTACT_NAME',
          ),
        ),
        8 => 
        array (
          0 => 
          array (
            'name' => 'billing_address_street',
            'label' => 'LBL_BILL_TO',
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'billing',
            ),
          ),
          1 => 
          array (
            'name' => 'shipping_address_street',
            'label' => 'LBL_SHIP_TO',
            'type' => 'address',
            'displayParams' => 
            array (
              'key' => 'shipping',
            ),
          ),
        ),
        9 => 
        array (
          0 => 
          array (
            'name' => 'description',
            'label' => 'LBL_DESCRIPTION',
          ),
          1 => 
          array (
            'name' => 'contacts_quotes_name',
            'label' => 'LBL_CONTACTS_QUOTES_FROM_CONTACTS_TITLE',
          ),
        ),
        10 => 
        array (
          0 => 
          array (
            'name' => 'contacts_quotes_1_name',
            'label' => 'LBL_CONTACTS_QUOTES_1_FROM_CONTACTS_TITLE',
          ),
          1 => 
          array (
            'name' => 'contacts_quotes_2_name',
          ),
        ),
      ),
    ),
  ),
);
?>
