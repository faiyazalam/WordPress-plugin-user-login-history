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
class Db {

	/**
	 * Inserts a record in the given table.
	 *
	 * @global type $wpdb The object.
	 * @param type $table The table name.
	 * @param type $data The data to be saved.
	 * @return boolean
	 */
	public static function insert( $table = '', $data = array() ) {
		if ( ! $table || ! $data ) {
			return false;
		}
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . $table, $data );

		if ( $wpdb->last_error || ! $wpdb->insert_id ) {
			Error_Log::error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query );
			return false;
		}
		return $wpdb->insert_id;
	}

	/**
	 * Run a given query.
	 *
	 * @param string $sql The sql query.
	 * @return boolean
	 */
	public static function query( $sql = '' ) {
		if ( empty( $sql ) ) {
			return false;
		}
		global $wpdb;

		$wpdb->query( $sql );

		if ( $wpdb->last_error ) {
			Error_Log::error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query );
			return false;
		}
		return ! empty( $wpdb->insert_id ) ? $wpdb->insert_id : true;
	}

	/**
	 * Get results.
	 *
	 * @param string $sql The sql query to be run.
	 * @param string $type The type of result to be returned.
	 * @return boolean
	 */
	public static function get_results( $sql = '', $type = 'ARRAY_A' ) {
		if ( ! $sql ) {
			return false;
		}
		global $wpdb;

		$results = $wpdb->get_results( $sql, $type );

		if ( $wpdb->last_error ) {
			Error_Log::error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query );
			return false;
		}

		return $results;
	}

	/**
	 * Get a single value after running a given sql.
	 *
	 * @param string $sql The sql query.
	 * @return boolean
	 */
	public static function get_var( $sql = '' ) {
		if ( ! $sql ) {
			return false;
		}
		global $wpdb;

		$result = $wpdb->get_var( $sql );

		if ( $wpdb->last_error ) {
			Error_Log::error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query );
			return false;
		}
		return $result;
	}

	/**
	 * Delete multiple records from a table.
	 *
	 * @param string $table The table name.
	 * @param [int]  $ids The record IDs.
	 */
	public static function delete_rows_by_table_and_ids( string $table = '', array $ids = [] ) {
		if ( empty( $table ) || empty( $ids ) ) {
			return false;
		}
		global $wpdb;
		$table = $wpdb->prefix . $table;

		$ids    = is_array( $ids ) ? implode( ',', array_map( 'absint', $ids ) ) : $ids;
		$status = $wpdb->query( "DELETE FROM $table WHERE id IN($ids)" );

		if ( $wpdb->last_error ) {
			Error_Log::error_log( $wpdb->last_error . ' ' . $wpdb->last_query );
			return false;
		}
		return $status;
	}

	/**
	 * Truncate a table.
	 *
	 * @param string $table The table name.
	 * @return boolean
	 */
	public static function truncate_table( $table = '' ) {

		if ( empty( $table ) ) {
			return false;
		}
		global $wpdb;
		return self::query( "TRUNCATE {$wpdb->prefix}$table" );
	}

	/**
	 * Drop table.
	 *
	 * @param string $table The table name.
	 * @return boolean
	 */
	public static function drop_table( $table = '' ) {
		if ( empty( $table ) ) {
			return false;
		}

		global $wpdb;
		return self::query( "DROP TABLE IF EXISTS {$wpdb->prefix}$table" );
	}

	/**
	 * Get a column value after running a given sql query.
	 *
	 * @param string $sql The sql query.
	 * @return boolean
	 */
	public static function get_col( $sql = '' ) {

		if ( empty( $sql ) ) {
			return;
		}

		global $wpdb;

		$result = $wpdb->get_col( $sql );

		if ( $wpdb->last_error ) {
			Error_Log::error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query, __LINE__, __FILE__ );
			return false;
		}

		return $result;
	}

	/**
	 * Get blog ids by site id.
	 *
	 * @param int $site_id The site id.
	 * @return boolean
	 */
	public static function get_blog_ids_by_site_id( $site_id = null ) {

		if ( is_null( $site_id ) ) {

			$site_id = get_current_network_id();
		}

		if ( ! is_numeric( $site_id ) || ! ( $site_id > 0 ) ) {
			return false;
		}
		global $wpdb;
		return self::get_col( "SELECT blog_id FROM $wpdb->blogs WHERE `site_id` = " . absint( $site_id ) . ' ORDER BY blog_id ASC' );
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
			wp_die( $wpdb->last_error );
		}
	}

	/**
	 * Check if table exists.
	 *
	 * @param string $table The table name.
	 * @return boolean
	 */
	public static function is_table_exist( $table = '' ) {
		if ( empty( $table ) || ! is_string( $table ) ) {

			return false;
		}
		global $wpdb;

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) );
		return $wpdb->get_var( $query ) == $table;
	}

	/**
	 * Check if blog exists.
	 *
	 * @param int $blog_id The blog id.
	 * @param int $site_id The site id.
	 * @return boolean
	 */
	public static function is_blog_exist( $blog_id = null, $site_id = null ) {
		if ( is_null( $blog_id ) || ! is_numeric( $blog_id ) || ! ( $blog_id > 0 ) ) {
			return false;
		}

		if ( is_null( $site_id ) ) {

			$site_id = get_current_network_id();
		}

		if ( ! is_numeric( $site_id ) || ! ( $site_id > 0 ) ) {
			return false;
		}
		global $wpdb;
		return self::get_var( "SELECT blog_id FROM $wpdb->blogs WHERE `blog_id` = " . absint( $blog_id ) . ' AND `site_id` = ' . absint( $site_id ) . ' ORDER BY blog_id ASC' );
	}

}
