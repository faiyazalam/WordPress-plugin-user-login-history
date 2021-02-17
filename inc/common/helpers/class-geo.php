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
class Geo {

	/**
	 * URL of the thirst party service.
	 */
	const GEO_API_URL = 'https://tools.keycdn.com/geo.json?host=';

	/**
	 * Retrieve IP Address of user.
	 *
	 * @return string The IP address of user.
	 */
	public static function get_ip() {
		$https = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		);

		$https = apply_filters( 'faulh_https_list', $https );

		foreach ( $https as $http ) {
			if ( ! empty( $_SERVER[ $http ] ) ) {
				return $_SERVER[ $http ];
			}
		}
	}

	/**
	 * Retrieve the geo location.
	 *
	 * @return string The response from geo API.
	 */
	public static function get_geo_location() {
		$defaults = array( 'timeout' => 5, "headers"=>array("User-Agent"=>"keycdn-tools:". get_site_url()));
		$args     = apply_filters( 'faulh_remote_get_args', $defaults );
		$r        = wp_parse_args( $args, $defaults );
		$response = wp_remote_get( self::GEO_API_URL . self::get_ip(), $r );
		if ( is_wp_error( $response ) ) {
			Error_Log::error_log( 'error while calling wp_remote_get method:' . $response->get_error_message(), __LINE__, __FILE__ );
			return false;
		}

		if ( is_array( $response ) ) {
			$decoded = json_decode( $response['body'] );
			if ( isset( $decoded->data->geo ) ) {
				return (array) $decoded->data->geo;
			}
		}
		return false;
	}

}
