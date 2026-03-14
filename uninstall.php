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
		$plugin_name = 'faulh';
		$user_meta_keys = array(
			$plugin_name . '_timezone',
			$plugin_name . '_rows_per_page',
			$plugin_name . '_last_seen_time',
			'managetoplevel_page_' . $plugin_name . '-login-listingcolumnshidden',
			'managetoplevel_page_' . $plugin_name . '-login-listing-networkcolumnshidden',
		);

		foreach ( $user_meta_keys as $user_meta_key ) {
			delete_metadata( 'user', 0, $user_meta_key, '', true );
		}
	}
}

if ( ! function_exists( 'faulh_drop_tables' ) ) {

	/**
	 * Drop the tables.
	 */
	function faulh_drop_tables() {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange	-- droping the database table on uninstallation of the plugin, catching is not required here.
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $wpdb->prefix . 'fa_user_logins' ) );
	}
}

if ( ! function_exists( 'faulh_uninstall_plugin' ) ) {

	/**
	 * Do stuff during plugin uninstallation.
	 */
	function faulh_uninstall_plugin() {
		faulh_delete_user_metadata();
		delete_site_option('faulh_network_settings');

		if ( is_multisite() ) {
			$blog_ids = get_sites(
				array(
					'fields' => 'ids',
				)
			);
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
