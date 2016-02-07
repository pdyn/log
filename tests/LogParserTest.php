<?php
/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * @copyright 2010 onwards James McQuillan (http://pdyn.net)
 * @author James McQuillan <james@pdyn.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace pdyn\log\tests;

/**
 * Mock LogParser implementation allowing inspection of all properties and running of all methods.
 */
class LogParserMock extends \pdyn\log\LogParser {
	use \pdyn\testing\AccessibleObjectTrait;
}

/**
 * Test Logger class.
 *
 * @group pdyn
 * @group pdyn_log
 * @codeCoverageIgnore
 */
class LogParserTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test set_file function.
	 */
	public function test_set_file() {
		$parser = new LogParserMock();
		$parser->set_file(__DIR__.'/fixtures/log.txt');
		$this->assertEquals(__DIR__.'/fixtures/log.txt', $parser->logfile);
	}

	/**
	 * Test get_array function.
	 */
	public function test_get_array() {
		$parser = new LogParserMock();
		$parser->set_file(__DIR__.'/fixtures/log.txt');

		$expected = array (
			'13-Apr-2014 05:29:08 America/Toronto' => array(
				0 => 'New Line 1',
			),
			'13-Apr-2014 05:29:09 America/Toronto' => array(
				0 => 'Connected line 1',
				1 => 'Connected line 2',
				2 => 'Connected line 3',
				3 => 'Connected line 4',
			),
			'13-Apr-2014 05:29:10 America/Toronto' => array(
				0 => 'New Line 2',
			),
		);
		$actual = $parser->get_array();
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Test get_html method.
	 */
	public function test_get_html() {
		$parser = new LogParserMock();
		$parser->set_file(__DIR__.'/fixtures/log.txt');

		$expected = '<li><span class="date">Apr 13 2014</span><span class="message"><pre>New Line 1
</pre></span></li><li><span class="date">Apr 13 2014</span><span class="message"><pre>Connected line 1
Connected line 2
Connected line 3
Connected line 4
</pre></span></li><li><span class="date">Apr 13 2014</span><span class="message"><pre>New Line 2
</pre></span></li>';
		$actual = $parser->get_html();
		$this->assertEquals($expected, $actual);
	}
}
