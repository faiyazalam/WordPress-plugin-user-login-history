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
class Tool {

	/**
	 * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
	 * to the end of the array.
	 *
	 * @param array  $array The array.
	 * @param string $key The key.
	 * @param array  $new The new array.
	 *
	 * @return array
	 */
	public static function array_insert_after( array $array, $key, array $new ) {
		$keys  = array_keys( $array );
		$index = array_search( $key, $keys );
		$pos   = false === $index ? count( $array ) : $index + 1;
		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
	}

	/**
	 * Get role names by keys.
	 *
	 * @param type $keys The role keys.
	 * @return boolean
	 */
	public static function get_role_names_by_keys( $keys = array() ) {
		if ( empty( $keys ) ) {
			return false;
		}

		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		if ( is_string( $keys ) ) {
			$keys = explode( ',', $keys );
		}

		$names          = array();
		$editable_roles = array_reverse( get_editable_roles() );

		foreach ( $keys as $key ) {
			$editable_role = $editable_roles[ trim( $key ) ];
			$names[]       = translate_user_role( $editable_role['name'] );
		}

		return implode( ', ', $names );
	}

}
