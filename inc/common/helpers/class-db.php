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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Backend Functionality.
 */
class Db {


	/**
	 * Delete multiple records from a table.
	 *
	 * @param string $table The table name.
	 * @param [int]  $ids The record IDs.
	 */
	public static function delete_rows_by_table_and_ids( string $table = '', array $ids = array() ) {
		if ( empty( $table ) || empty( $ids ) ) {
			return false;
		}

		global $wpdb;
		$ids = implode( ',', array_map( 'absint', $ids ) );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- already sanitized.
		$status = $wpdb->query( $wpdb->prepare( "DELETE FROM %i WHERE id IN ($ids)", $wpdb->prefix . $table ) );

		if ( $wpdb->last_error ) {
			Error_Log::error_log( $wpdb->last_error . ' ' . $wpdb->last_query );
			return false;
		}
		return $status;
	}

	/**
	 * Run db delta.
	 *
	 * @param string $sql The sql query.
	 * @return boolean
	 */
	public static function db_delta( $sql = '' ) {
		if ( empty( $sql ) ) {
			return false;
		}
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $sql );
		if ( ! empty( $wpdb->last_error ) ) {
			Error_Log::error_log( 'Error while creating or updatiing tables-' . $wpdb->last_error, __LINE__, __FILE__ );
			wp_die( esc_html( $wpdb->last_error ) );
		}
	}
}
