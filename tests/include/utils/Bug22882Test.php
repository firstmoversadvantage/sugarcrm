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

 
require_once 'modules/Calls/metadata/additionalDetails.php';

/**
 * @ticket 22882
 */
class Bug22882Test extends Sugar_PHPUnit_Framework_TestCase
{
    private $file = '';
    private $file_fr_tmp = '';
    private $file_en_tmp = '';
    
    public function setUp()
    {
        if ( !is_dir('custom/include/language') )
            @mkdir('custom/include/language', 0777, true);
        sugar_cache_clear('app_list_strings.en_us');
        sugar_cache_clear('app_list_strings.fr_test');
    }
    
    public function testMultiLanguagesDeletedValue()
    {
        $this->loadFilesDeletedValue();
        $resultfr = return_app_list_strings_language('fr_test');
        $resulten = return_app_list_strings_language('en_us');
        $resultfr = array_keys($resultfr['account_type_dom']);
        $resulten = array_keys($resulten['account_type_dom']);
        if($this->isSameSize($resultfr, $resulten)){
            $this->isEqual($resultfr, $resulten);
        }
        $this->cleanupFiles();
    }
    
    public function testMultiLanguagesDeletedValueFrOnly()
    {
        $this->loadFilesDeletedValueFrOnly();
        $resultfr = return_app_list_strings_language('fr_test');
        $resulten = return_app_list_strings_language('en_us');
        $resultfr = array_keys($resultfr['account_type_dom']);
        $resulten = array_keys($resulten['account_type_dom']);
        $this->assertNotEquals(count($resultfr), count($resulten), 'The 2 drop down list have the same size.');
        $this->cleanupFiles();
    }
    
    public function testMultiLanguagesDeletedValueEnOnly()
    {
        $this->loadFilesDeletedValueEnOnly();
        $resultfr = return_app_list_strings_language('fr_test');
        $resulten = return_app_list_strings_language('en_us');
        $resultfr = array_keys($resultfr['account_type_dom']);
        $resulten = array_keys($resulten['account_type_dom']);
        $this->assertNotEquals(count($resultfr),count($resulten));
        $this->assertFalse(in_array('Customer',$resulten));
        $this->assertTrue(in_array('Customer',$resultfr));
        $this->cleanupFiles();
    }
    
    public function testMultiLanguagesAddedValue()
    {
        $this->loadFilesAddedValueEn();
        $resultfr = return_app_list_strings_language('fr_test');
        $resulten = return_app_list_strings_language('en_us');
        $resultfr = array_keys($resultfr['account_type_dom']);
        $resulten = array_keys($resulten['account_type_dom']);
        $this->assertNotEquals(count($resultfr), count($resulten), 'The 2 drop down list have the same size.');
        $this->cleanupFiles();
    }
    
    public function loadFilesDeletedValue(){
            $file_fr = <<<FRFR
<?php
\$app_list_strings['account_type_dom']=array ( 
  //'Analyst' => 'Analyste', Line deleted
  'Competitor' => 'Concurrent',
  'Customer' => 'Client',
  'Integrator' => 'Intégrateur',
  'Investor' => 'Investisseur',
  'Partner' => 'Partenaire',
  'Press' => 'Presse',
  'Prospect' => 'Prospect',
  'Other' => 'Autre',
  '' => '',
);
FRFR;
        $file_en = <<<ENEN
<?php
\$app_list_strings['account_type_dom']=array ( 
  //'Analyst' => 'Analyst', Line deleted
  'Competitor' => 'Competitor',
  'Customer' => 'Customer',
  'Integrator' => 'Integrator',
  'Investor' => 'Investor',
  'Partner' => 'Partner',
  'Press' => 'Press',
  'Prospect' => 'Prospect',
  'Other' => 'Other',
  '' => '',
);
ENEN;
        if(!file_exists('include/language/fr_test.lang.php')){
            $this->file = file_get_contents('include/language/en_us.lang.php');
            file_put_contents('include/language/fr_test.lang.php', $this->file);
        }
        if(!file_exists('custom/include/language/fr_test.lang.php')){
            file_put_contents('custom/include/language/fr_test.lang.php', $file_fr);
        }else{
            $this->file_fr_tmp = file_get_contents('custom/include/language/fr_test.lang.php');
            file_put_contents('custom/include/language/fr_test.lang.php', $file_fr);
        }
        if(!file_exists('custom/include/language/en_us.lang.php')){
            file_put_contents('custom/include/language/en_us.lang.php', $file_en);
        }else{
            $this->file_en_tmp = file_get_contents('custom/include/language/en_us.lang.php');
            file_put_contents('custom/include/language/en_us.lang.php', $file_en);
        }
    }
    
    public function loadFilesDeletedValueFrOnly(){
            $file_fr = <<<FRFR
<?php
\$app_list_strings['account_type_dom']=array ( 
  //'Analyst' => 'Analyste', Line deleted
  'Competitor' => 'Concurrent',
  'Customer' => 'Client',
  'Integrator' => 'Intégrateur',
  'Investor' => 'Investisseur',
  'Partner' => 'Partenaire',
  'Press' => 'Presse',
  'Prospect' => 'Prospect',
  'Other' => 'Autre',
  '' => '',
);
FRFR;
        $file_en = <<<ENEN
<?php
\$app_list_strings['account_type_dom']=array ( 
  'Analyst' => 'Analyst',
  'Competitor' => 'Competitor',
  'Customer' => 'Customer',
  'Integrator' => 'Integrator',
  'Investor' => 'Investor',
  'Partner' => 'Partner',
  'Press' => 'Press',
  'Prospect' => 'Prospect',
  'Other' => 'Other',
  '' => '',
);
ENEN;
        if(!file_exists('include/language/fr_test.lang.php')){
            $this->file = file_get_contents('include/language/en_us.lang.php');
            file_put_contents('include/language/fr_test.lang.php', $this->file);
        }
        if(!file_exists('custom/include/language/fr_test.lang.php')){
            file_put_contents('custom/include/language/fr_test.lang.php', $file_fr);
        }else{
            $this->file_fr_tmp = file_get_contents('custom/include/language/fr_test.lang.php');
            file_put_contents('custom/include/language/fr_test.lang.php', $file_fr);
        }
        if(!file_exists('custom/include/language/en_us.lang.php')){
            file_put_contents('custom/include/language/en_us.lang.php', $file_en);
        }else{
            $this->file_en_tmp = file_get_contents('custom/include/language/en_us.lang.php');
            file_put_contents('custom/include/language/en_us.lang.php', $file_en);
        }
    }
    
    public function loadFilesDeletedValueEnOnly(){
            $file_fr = <<<FRFR
<?php
\$app_list_strings['account_type_dom']=array ( 
  'Analyst' => 'Analyste',
  'Competitor' => 'Concurrent',
  'Customer' => 'Client',
  'Integrator' => 'Intégrateur',
  'Investor' => 'Investisseur',
  'Partner' => 'Partenaire',
  'Press' => 'Presse',
  'Prospect' => 'Prospect',
  'Other' => 'Autre',
  '' => '',
);
FRFR;
        $file_en = <<<ENEN
<?php
\$app_list_strings['account_type_dom']=array ( 
  'Analyst' => 'Analyst',
  'Competitor' => 'Competitor',
  //'Customer' => 'Customer',
  'Integrator' => 'Integrator',
  'Investor' => 'Investor',
  'Partner' => 'Partner',
  'Press' => 'Press',
  'Prospect' => 'Prospect',
  'Other' => 'Other',
  '' => '',
);
ENEN;
        if(!file_exists('include/language/fr_test.lang.php')){
            $this->file = file_get_contents('include/language/en_us.lang.php');
            file_put_contents('include/language/fr_test.lang.php', $this->file);
        }
        if(!file_exists('custom/include/language/fr_test.lang.php')){
            file_put_contents('custom/include/language/fr_test.lang.php', $file_fr);
        }else{
            $this->file_fr_tmp = file_get_contents('custom/include/language/fr_test.lang.php');
            file_put_contents('custom/include/language/fr_test.lang.php', $file_fr);
        }
        if(!file_exists('custom/include/language/en_us.lang.php')){
            file_put_contents('custom/include/language/en_us.lang.php', $file_en);
        }else{
            $this->file_en_tmp = file_get_contents('custom/include/language/en_us.lang.php');
            file_put_contents('custom/include/language/en_us.lang.php', $file_en);
        }
    }
    
    public function loadFilesAddedValueEn(){
            $file_fr = <<<FRFR
<?php
\$app_list_strings['account_type_dom']=array ( 
  'Analyst' => 'Analyste',
  'Competitor' => 'Concurrent',
  'Customer' => 'Client',
  'Integrator' => 'Intégrateur',
  'Investor' => 'Investisseur',
  'Partner' => 'Partenaire',
  'Press' => 'Presse',
  'Prospect' => 'Prospect',
  'Other' => 'Autre',
  '' => '',
);
FRFR;
        $file_en = <<<ENEN
<?php
\$app_list_strings['account_type_dom']=array ( 
  'Extra' => 'Extra',
  'Analyst' => 'Analyst',
  'Competitor' => 'Competitor',
  'Customer' => 'Customer',
  'Integrator' => 'Integrator',
  'Investor' => 'Investor',
  'Partner' => 'Partner',
  'Press' => 'Press',
  'Prospect' => 'Prospect',
  'Other' => 'Other',
  '' => '',
);
ENEN;
        if(!file_exists('include/language/fr_test.lang.php')){
            $this->file = file_get_contents('include/language/en_us.lang.php');
            file_put_contents('include/language/fr_test.lang.php', $this->file);
        }
        if(!file_exists('custom/include/language/fr_test.lang.php')){
            file_put_contents('custom/include/language/fr_test.lang.php', $file_fr);
        }else{
            $this->file_fr_tmp = file_get_contents('custom/include/language/fr_test.lang.php');
            file_put_contents('custom/include/language/fr_test.lang.php', $file_fr);
        }
        if(!file_exists('custom/include/language/en_us.lang.php')){
            file_put_contents('custom/include/language/en_us.lang.php', $file_en);
        }else{
            $this->file_en_tmp = file_get_contents('custom/include/language/en_us.lang.php');
            file_put_contents('custom/include/language/en_us.lang.php', $file_en);
        }
    }
    
    public function cleanupFiles(){
        if(!empty($this->file)){
            $this->file = '';
            unlink('include/language/fr_test.lang.php');
        }
        if(!empty($this->file_fr_tmp)){
            file_put_contents('custom/include/language/fr_test.lang.php', $this->file_fr_tmp);
            $this->file_fr_tmp = '';
        }else{
            unlink('custom/include/language/fr_test.lang.php');
        }
        if(!empty($this->file_en_tmp)){
            file_put_contents('custom/include/language/en_us.lang.php', $this->file_en_tmp);
            $this->file_en_tmp = '';
        }else{
            unlink('custom/include/language/en_us.lang.php');
        }
    }
    
    public function isSameSize($result1, $result2)
    {
        if(count($result1) != count($result2)){
            $this->assertTrue(false, 'The 2 drop down list didn\'t have the same size.');
            return false;
        }
        return true;
    }
    
    public function isEqual($result1, $result2)
    {
        foreach($result1 as $k=>$v){
            $this->assertTrue(in_array($v,$result2));
        }
        foreach($result2 as $k=>$v){
            $this->assertTrue(in_array($v,$result1));
        }
    }
}
