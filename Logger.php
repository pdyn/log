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

namespace pdyn\log;

/**
 * A PSR-3 compliant logging class.
 */
class Logger implements \Psr\Log\LoggerInterface {
	/** @var string The location of the log file to write to */
	public $logfile = '';

	/**
	 * Constructor.
	 *
	 * @param string $logfile The filename of the file to write logs to.
	 */
	public function __construct($logfile = '') {
		if (!empty($logfile)) {
			$this->set_logfile($logfile);
		}
	}

	/**
	 * Sets the output file.
	 *
	 * @param string $logfile The filename of the file to write logs to.
	 */
	public function set_logfile($logfile) {
		$this->logfile = $logfile;
		if (!file_exists($this->logfile)) {
			try {
				@touch($this->logfile);
			} catch (\Exception $e) {

			}
		}
	}

	/**
	 * Gets the output file.
	 *
	 * @return string $logfile The filename of the file to write logs to.
	 */
	public function get_logfile() {
		return $this->logfile;
	}

	/**
	 * System is unusable.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function emergency($message, array $context = array()) {
		$this->log('emergency', $message, $context);
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function alert($message, array $context = array()) {
		$this->log('alert', $message, $context);
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function critical($message, array $context = array()) {
		$this->log('critical', $message, $context);
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function error($message, array $context = array()) {
		$this->log('error', $message, $context);
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function warning($message, array $context = array()) {
		$this->log('warning', $message, $context);
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function notice($message, array $context = array()) {
		$this->log('notice', $message, $context);
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function info($message, array $context = array()) {
		$this->log('info', $message, $context);
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function debug($message, array $context = array()) {
		$this->log('debug', $message, $context);
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param string $level
	 * @param string $message
	 * @param array $context
	 */
	public function log($level, $message, array $context = array()) {
		$timestr = date('d-M-Y H:i:s', time());
		if (!is_scalar($message)) {
			$message = print_r($message, true);
		} else {
			if (is_int($message)) {
				$message = '(int)'.$message;
			} elseif (is_float($message)) {
				$message = '(float)'.$message;
			} elseif (is_bool($message)) {
				$message = '(bool)'.(int)$message;
			}
		}
		$message = '['.$timestr.'] '.mb_strtoupper($level).' :: '.$message."\n";
		if (file_exists($this->logfile)) {
			file_put_contents($this->logfile, $message, FILE_APPEND);
		}
	}
}
