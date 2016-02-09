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
 * Parses log files written by \pdyn\log\Logger.
 */
class LogParser {

	/** @var string The logfile we're parsing. */
	protected $logfile;

	/**
	 * Sets the logfile to use.
	 *
	 * @param string $logfile The full filename of a logfile.
	 */
	public function set_file($logfile) {
		if (file_exists($logfile)) {
			$this->logfile = $logfile;
		}
	}

	/**
	 * Get an array of log entries.
	 *
	 * @param bool $reversechronological Whether to order the entries in reverse-chronological order (if false, orders in chronological order)
	 * @return array An array of log entries. Indexes are timestamps, values are arrays of logs that occurred in that timestamp.
	 */
	public function get_array($reversechronological = false) {
		if (empty($this->logfile)) {
			return [];
		}

		$log = file_get_contents($this->logfile);
		$log = trim($log);
		$log = preg_split('#^\[([0-9]{2}\-[a-z]{3}\-[0-9]{4}\s*.+)\]#imuU', $log, null, PREG_SPLIT_DELIM_CAPTURE);
		$log0 = array_shift($log);

		$log_organized = [];
		if (!empty($log0)) {
			$log_organized[] = [$log0];
		}
		$logcount = count($log);
		for ($i = 0; $i < $logcount; $i += 2) {
			$log_organized[$log[$i]][] = trim($log[$i + 1]);
			unset($log[$i], $log[$i + 1]);
		}

		if ($reversechronological === true) {
			foreach ($log_organized as $time => $logs) {
				$log_organized[$time] = array_reverse($logs);
			}
			$log_organized = array_reverse($log_organized);
			return $log_organized;
		} else {
			return $log_organized;
		}
	}

	/**
	 * Get an HTML representation of the log.
	 *
	 * @param bool $reversechronological Whether to order the entries in reverse-chronological order (if false, orders in chronological order)
	 * @return string Returns the log as a string, with log entries organized into <li> tags.
	 */
	public function get_html($reversechronological = false) {
		if (empty($this->logfile)) {
			return '';
		}
		$logarray = $this->get_array($reversechronological);
		$output = '';
		foreach ($logarray as $time => $logs) {
			try {
				$timeobj = new \pdyn\datatype\Time($time);
				$relativetime = $timeobj->get_relative_time();
			} catch (\Exception $e) {
				$relativetime = 0;
			}

			$message = '<pre>';
			foreach ($logs as $log) {
				$message .= trim($log)."\n";
			}
			$message .= '</pre>';
			$output .= '<li><span class="date">'.$relativetime.'</span><span class="message">'.$message.'</span></li>';
			unset($logarray[$time]);
		}
		return $output;
	}
}
