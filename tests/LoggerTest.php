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
 * Test Logger class.
 *
 * @group pdyn
 * @group pdyn_log
 */
class LoggerTest extends \PHPUnit_Framework_TestCase {
	protected $logfile = null;

	/**
	 * PHPUnit teardown - delete temp log file.
	 */
	protected function tearDown() {
        if (!empty($this->logfile)) {
			unlink($this->logfile);
		}
    }

	/**
	 * Assert the log contains a string.
	 *
	 * @param string $expected The expected string.
	 */
	public function assertLog($expected) {
		$log = file_get_contents($this->logfile);
		$log = trim(mb_substr($log, 23));
		$this->assertEquals($expected, $log);
	}

	/**
	 * Get the file we should write logs to.
	 *
	 * @return string The absolute path to the test log file.
	 */
	public function init_logfile() {
		if (!empty($this->logfile)) {
			unlink($this->logfile);
		}
		$this->logfile = tempnam(sys_get_temp_dir(), 'pdyn_log_loggertest');
	}

	/**
	 * Dataprovider for test_log
	 *
	 * @return array Array of test parameters.
	 */
	public function dataprovider_log() {
		return [
			[
				'emergency',
				'This is only a test emergency',
				'EMERGENCY :: This is only a test emergency'
			],
			[
				'alert',
				'This is only a test alert',
				'ALERT :: This is only a test alert'
			],
			[
				'critical',
				'This is only a test critical',
				'CRITICAL :: This is only a test critical'
			],
			[
				'error',
				'This is only a test error',
				'ERROR :: This is only a test error'
			],
			[
				'warning',
				'This is only a test warning',
				'WARNING :: This is only a test warning'
			],
			[
				'notice',
				'This is only a test notice',
				'NOTICE :: This is only a test notice'
			],
			[
				'info',
				'This is only a test info',
				'INFO :: This is only a test info'
			],
			[
				'debug',
				'This is only a test debug',
				'DEBUG :: This is only a test debug'
			],
			[
				'randomlevel',
				'This is only a test log for a random level',
				'RANDOMLEVEL :: This is only a test log for a random level'
			],
		];
	}

	/**
	 * Test logging.
	 *
	 * @dataProvider dataprovider_log
	 * @param string $level The level to log at.
	 * @param string $message The message to log.
	 * @param string $expected The expected log mesage.
	 */
	public function test_log($level, $message, $expected) {
		$this->init_logfile();
		$log = new \pdyn\log\Logger($this->logfile);

		$standardlevels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
		if (in_array($level, $standardlevels) === true) {
			$log->$level($message);
		} else {
			$log->log($level, $message);
		}
		$this->assertLog($expected);
	}
}
