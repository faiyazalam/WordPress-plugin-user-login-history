<?php
/**
 * Backend Functionality
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
class Validation {

	/**
	 * Just for backward compatibility.
	 *
	 * @param mixed $value The value.
	 * @return bool
	 */
	public static function is_empty( $value = '' ) {
		if ( is_string( $value ) ) {
			$value = trim( $value );
			return ( empty( $value ) || 'unknown' == strtolower( $value ) );
		}

		return empty( $value );
	}

}
