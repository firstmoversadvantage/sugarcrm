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


/**
 * Created: Sep 12, 2011
 */
include_once('include/Dashlets/DashletRssFeedTitle.php');

class Bug46217Test extends Sugar_PHPUnit_Framework_TestCase {

	public $rssFeedClass;
	
	public function setUp() {
		$this->rssFeedClass = new DashletRssFeedTitle("");
	}
	
	public function tearDown() {
		unset($this->rssFeedClass);
	}
	
	public function dataProviderCorrectParse() {
		return array(
			array('<?xml version="1.0" encoding="UTF-8"?>
				<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel>
				<title>France Info</title>
				<link>http://www.france-info.com</link>
				<description>France Info - A la Une</description>
				<image>
				<url>http://www.france-info.com/IMG/siteon0.gif</url>
				<title>France Info</title>
				<link>http://www.france-info.com</link>
				</image>', 
				
				'France Info'
			),
			array('<?xml version="1.0" encoding="UTF-8" ?>
				<rss version="2.0">
				<channel>
				<title><![CDATA[RSS Title]]></title>
				<description>This is an example of an RSS feed</description>
				<link>http://www.someexamplerssdomain.com/main.html</link>
				<lastBuildDate>Mon, 06 Sep 2010 00:01:00 +0000 </lastBuildDate>
				<pubDate>Mon, 06 Sep 2009 16:45:00 +0000 </pubDate>',
				
				'RSS Title'
			),
		);
	}
	
	/**
	 * @dataProvider dataProviderCorrectParse
	 */
	public function testCorrectTitleParse($rssFeed, $expectedTitle) {
		$this->rssFeedClass->contents = $rssFeed;
		$this->rssFeedClass->getTitle();
		$this->assertEquals($expectedTitle, $this->rssFeedClass->title);
		$this->rssFeedClass->convertEncoding();
		$this->assertEquals($expectedTitle, $this->rssFeedClass->title);
	}
}