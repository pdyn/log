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
 * Handles log rotation.
 */
class LogRotate {

	/**
	 * Rotates logs on a daily basis.
	 *
	 * @param string $logdir The directory logs are stored in.
	 */
	public function rotate_daily($logdir) {
		if (!file_exists($logdir.'/archive/')) {
			mkdir($logdir.'/archive/');
		}

		$datestamp = date('Ymd', strtotime('midnight yesterday'));

		$logfiles = glob($logdir.'/*.log');
		clearstatcache();
		foreach ($logfiles as $file) {
			$archivefile = $logdir.'/archive/'.$datestamp.'_'.basename($file);
			if (!file_exists($archivefile)) {
				// Move + recreate the log file.
				rename($file, $archivefile);
				touch($file);

				// Compress the archived file.
				if (extension_loaded('zlib') && function_exists('gzopen')) {
					$this->compress_with_zlib($archivefile);
				} elseif (extension_loaded('zip') && class_exists('\ZipArchive')) {
					$this->compress_with_ziparchive($archivefile);
				}

			}
		}
	}

	/**
	 * Compress the log file using zlib.
	 *
	 * @param string $file The name of the file to compress.
	 * @return bool Success/Failure.
	 */
	protected function compress_with_zlib($file) {
		$gzarchivefile = $file.'.gz';
		if (!file_exists($gzarchivefile)) {
			$fh = gzopen($gzarchivefile, 'ab9');
			gzwrite($fh, file_get_contents($file));
			gzclose($fh);
			unlink($file);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Compress the log file using zip.
	 *
	 * @param string $file The name of the file to compress.
	 * @return bool Success/Failure.
	 */
	protected function compress_with_ziparchive($file) {
		$zipfile = $file.'.zip';
		if (!file_exists($zipfile)) {
			$zip = new \ZipArchive;
			$res = $zip->open($zipfile, \ZipArchive::CREATE);
			if ($res === true) {
				$zip->addFile($file);
				$zip->close();
				unlink($file);
				return true;
			}
		}
		return false;
	}
}
