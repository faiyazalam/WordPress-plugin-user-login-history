<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://userloginhistory.com
 *
 * @package    User_Login_History
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( defined( 'User_Login_History_Pro\NS' ) ) {
	return;
}

if ( ! function_exists( 'faulh_delete_options' ) ) {

	/**
	 * Delete the plugin options.
	 */
	function faulh_delete_options() {
		$prefix = 'faulh';

		$options = array(
			'fa_userloginhostory_version',
			$prefix . '_basics',
			$prefix . '_advanced',
		);

		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}
}

if ( ! function_exists( 'faulh_delete_user_metadata' ) ) {

	/**
	 * Delete the user metadata.
	 */
	function faulh_delete_user_metadata() {
		global $wpdb;
		$plugin_name = 'faulh';

		$user_meta_keys = array(
			$plugin_name . '_timezone',
			$plugin_name . '_rows_per_page',
			$plugin_name . '_last_seen_time',
			'managetoplevel_page_' . $plugin_name . '-login-listingcolumnshidden',
		);

		$sql_in = "'" . implode( "', '", $user_meta_keys ) . "'";

		$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key IN ($sql_in)" );

		if ( $wpdb->last_error ) {
			faulh_error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query );
		}
	}
}

if ( ! function_exists( 'faulh_drop_tables' ) ) {

	/**
	 * Drop the tables.
	 */
	function faulh_drop_tables() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'fa_user_logins' );

		if ( $wpdb->last_error ) {
			faulh_error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query );
		}
	}
}

if ( ! function_exists( 'faulh_get_blogs_of_current_network' ) ) {

	/**
	 * Get blogs of the current network.
	 *
	 * @return mixed
	 */
	function faulh_get_blogs_of_current_network() {
		global $wpdb;
		$sql    = "SELECT blog_id FROM $wpdb->blogs WHERE site_id = " . get_current_network_id() . '  ORDER BY blog_id ASC';
		$result = $wpdb->get_col( $sql );
		if ( $wpdb->last_error ) {
			faulh_error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query );
		}
		return $result;
	}
}

if ( ! function_exists( 'faulh_error_log' ) ) {

	/**
	 * Log the error message.
	 *
	 * @param string $message The message.
	 */
	function faulh_error_log( $message = '' ) {
		ini_set( 'error_log', WP_CONTENT_DIR . '/user-login-history.log' );
		error_log( 'Error While Uninstalling the plugin: ' . $message );
	}
}


if ( ! function_exists( 'faulh_uninstall_plugin' ) ) {

	/**
	 * Do stuff during plugin uninstallation.
	 */
	function faulh_uninstall_plugin() {
		faulh_delete_user_metadata();

		if ( is_multisite() ) {
			$blog_ids = faulh_get_blogs_of_current_network();
			foreach ( $blog_ids as $blog_id ) {
				switch_to_blog( $blog_id );
				faulh_delete_options();
				faulh_drop_tables();
			}
			restore_current_blog();
		} else {
			faulh_delete_options();
			faulh_drop_tables();
		}
	}
}

faulh_uninstall_plugin();
