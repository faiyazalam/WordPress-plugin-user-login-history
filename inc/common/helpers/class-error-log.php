<?php
/**
 * Backend Functionality.
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Common\Helpers;

/**
 * Backend Functionality.
 */
class Error_Log {

	const ERROR_LOG_FILE = '/user-login-history';

	/**
	 * Log the error message.
	 *
	 * @param string $message The message.
	 * @param string $line The line number.
	 * @param string $file The filepath.
	 */
	public static function error_log( $message = '', $line = '', $file = '' ) {
		$log_file = WP_CONTENT_DIR . '/user-login-history-errors.log';
		$entry    = '[' . current_time( 'mysql' ) . '] ' . $message . PHP_EOL;
		file_put_contents( $log_file, $entry, FILE_APPEND | LOCK_EX );
	}
}
