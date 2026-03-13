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

namespace User_Login_History\Inc\Admin;

use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;
use User_Login_History\Inc\Common\Helpers\Tool as Tool_Helper;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;
use User_Login_History\Inc\Common\Interfaces\Admin_List_Table as Admin_List_Table_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The login listing table for network admin.
 */
final class Network_Admin_Login_List_Table extends Login_List_Table implements Admin_Csv_Interface, Admin_List_Table_Interface {

	/**
	 * Holds the main sql query.
	 *
	 * @var string
	 */
	private $rows_sql = '';

	/**
	 * Holds the count sql query.
	 *
	 * @var string
	 */
	private $count_sql = '';

	/**
	 * Holds the where clause values.
	 *
	 * @var array
	 */
	private array $where_query_values = array();

	/**
	 * Holds the where clause.
	 *
	 * @var string
	 */
	private string $where_query = '';

	/**
	 * Initialize.
	 */
	public function init() {
		parent::init();
		$this->prepare_sql_queries();
	}

	/**
	 * Prepares the where clause.
	 */
	public function prepare_where_query() {
		$where                    = parent::prepare_where_query();
		$this->where_query        = $where['where_query'] ?? '';
		$this->where_query_values = $where['where_query_values'] ?? array();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- no need of nonce to fetch the data.
		$the_get = $_GET;
		if ( ! empty( $the_get['is_super_admin'] ) && in_array( $the_get['is_super_admin'], array( 'yes', 'no' ), true ) ) {
			$this->where_query         .= ' AND `FaUserLogin`.`is_super_admin` = %d';
			$this->where_query_values[] = absint( 'yes' == $the_get['is_super_admin'] );
		}
	}

	/**
	 * Prepares main and count sql queries.
	 *
	 * @global type $wpdb
	 */
	private function prepare_sql_queries() {
		$this->prepare_where_query();
		$where_query_values = array();
		global $wpdb;

		$i = 0;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- no need of nonce to fetch the data.
		$the_get = $_GET;

		if ( empty( $the_get['blog_id'] ) ) {
			$blog_ids = get_sites( array( 'fields' => 'ids' ) );
		} else {

			if ( ! is_numeric( $the_get['blog_id'] ) ) {
				return;
			}

			if ( $the_get['blog_id'] <= 0 ) {
				return;
			}

			$blog_id_to_filter = absint( $the_get['blog_id'] );

			if ( ! get_site( $blog_id_to_filter ) ) {
				return;
			}

			$blog_ids = array( $blog_id_to_filter );
		}

		foreach ( $blog_ids as $blog_id ) {
			$blog_prefix = $wpdb->get_blog_prefix( $blog_id );
			$table       = $blog_prefix . $this->table;

			$table = '`' . str_replace( '`', '``', $table ) . '`';

			if ( 0 < $i ) {
				$this->rows_sql  .= ' UNION ALL';
				$this->count_sql .= ' UNION ALL';
			}

			$this->rows_sql .= ' SELECT'
					. ' FaUserLogin.id, '
					. ' FaUserLogin.user_id,'
					. ' FaUserLogin.username,'
					. ' FaUserLogin.time_login,'
					. ' FaUserLogin.time_logout,'
					. ' FaUserLogin.time_last_seen,'
					. ' FaUserLogin.ip_address,'
					. ' FaUserLogin.operating_system,'
					. ' FaUserLogin.browser,'
					. ' FaUserLogin.browser_version,'
					. ' FaUserLogin.country_name,'
					. ' FaUserLogin.country_code,'
					. ' FaUserLogin.timezone,'
					. ' FaUserLogin.old_role,'
					. ' FaUserLogin.user_agent,'
					. ' FaUserLogin.login_status,'
					. ' FaUserLogin.is_super_admin,'
					. ' TIMESTAMPDIFF(SECOND,FaUserLogin.time_login,FaUserLogin.time_last_seen) as duration,'
					. " $blog_id as blog_id"
					. " FROM $table  AS FaUserLogin"
					. ' WHERE 1 ';

			$this->count_sql .= ' SELECT'
					. ' COUNT(FaUserLogin.id) AS count'
					. " FROM $table  AS FaUserLogin"
					. ' WHERE 1 ';

			if ( $this->where_query ) {
				$this->rows_sql    .= $this->where_query;
				$this->count_sql   .= $this->where_query;
				$where_query_values = array_merge( $where_query_values, $this->where_query_values );
			}

			++$i;
		}

		$this->where_query_values = $where_query_values;
		$this->rows_sql           = "SELECT * FROM ({$this->rows_sql}) AS FaUserLoginAllRows";
		$this->count_sql          = "SELECT SUM(count) as total FROM ({$this->count_sql}) AS FaUserLoginCount";
	}

	/**
	 * Get the columns.
	 */
	public function get_columns() {
		$columns = array_merge(
			parent::get_columns(),
			array(
				'is_super_admin' => esc_html__( 'Super Admin', 'user-login-history' ),
			)
		);
		$columns = Tool_Helper::array_insert_after( $columns, 'user_id', array( 'blog_id' => esc_html__( 'Blog ID', 'user-login-history' ) ) );
		return apply_filters( 'faulh_network_admin_login_list_get_columns', $columns );
	}

	/**
	 * Get the sortable columns.
	 */
	public function get_sortable_columns() {
		$columns = array_merge(
			parent::get_sortable_columns(),
			array(
				'is_super_admin' => array( 'is_super_admin', false ),
				'blog_id'        => array( 'blog_id', false ),
			)
		);

		return apply_filters( 'faulh_network_admin_login_list_get_sortable_columns', $columns );
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item The record.
	 * @return string
	 */
	public function column_cb( $item ) {
		$blog_id = ! empty( $item['blog_id'] ) ? absint( $item['blog_id'] ) : 0;
		return sprintf(
			'<input type="checkbox" name="bulk-delete-ids[%s][]" value="%s" />',
			$blog_id,
			$item['id']
		);
	}

	/**
	 * Retrieves the rows.
	 *
	 * @param int $per_page The limit.
	 * @param int $page_number The page number.
	 * @return mixed
	 */
	public function get_rows( $per_page = 20, $page_number = 1 ) {
		// phpcs:ignore	WordPress.Security.NonceVerification.Recommended -- no need of nonce to fetch the data.
		$the_request          = $_REQUEST;
		$sanitize_sql_orderby = sanitize_sql_orderby( 'time_login DESC' );
		$rows_sql             = $this->rows_sql;

		if ( ! empty( $the_request['orderby'] ) ) {
			$direction            = ! empty( $the_request['order'] ) ? strtoupper( $the_request['order'] ) : 'ASC';
			$direction            = in_array( $direction, array( 'ASC', 'DESC' ), true ) ? $direction : 'ASC';
			$sanitize_sql_orderby = sanitize_sql_orderby( $the_request['orderby'] . ' ' . $direction );
		}

		if ( $sanitize_sql_orderby ) {
			$rows_sql .= ' ORDER BY ' . $sanitize_sql_orderby;
		}

		if ( $per_page > 0 ) {
			$rows_sql .= ' LIMIT ' . absint( $per_page );
			$rows_sql .= ' OFFSET ' . absint( ( $page_number - 1 ) * $per_page );
		}

		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- already scaped.
		return $wpdb->get_results( $wpdb->prepare( $rows_sql, $this->where_query_values ), ARRAY_A );
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public function record_count() {
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- already scaped.
		return $wpdb->get_var( $wpdb->prepare( $this->count_sql, $this->where_query_values ) );
	}

	/**
	 * Get the username column value.
	 *
	 * @param array $item The record.
	 *
	 * @return string
	 */
	public function column_username( $item ) {

		if ( empty( $item['user_id'] ) ) {
			$title = esc_html( $item['username'] );
		} else {
			$edit_link = get_edit_user_link( $item['user_id'] );

			$title = ! empty( $edit_link ) ? "<a href='" . $edit_link . "'>" . esc_html( $item['username'] ) . '</a>' : '<strong>' . esc_html( $item['username'] ) . '</strong>';

			if ( empty( $item['blog_id'] ) ) {
				return $title;
			}
		}

		$delete_nonce = wp_create_nonce( $this->delete_action_nonce );
		$actions      = array(
			'delete' => sprintf(
				'<a href="?page=%s&action=%s&blog_id=%s&record_id=%s&_wpnonce=%s">%s</a>',
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is not required here to generate delete button.
				esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['page'] ?? '' ) ) ),
				$this->delete_action,
				absint( $item['blog_id'] ),
				absint( $item['id'] ),
				$delete_nonce,
				esc_html__( 'Delete', 'user-login-history' )
			),
		);

		return $title . $this->row_actions( $actions );
	}

	/**
	 * Process the bulk action
	 */
	public function process_bulk_action() {

		$nonce = '_wpnonce';

		if ( ! isset( $_POST[ $this->get_bulk_action_form() ] ) ) {
			return;
		}

		if ( empty( $_POST[ $nonce ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce ] ) ), $this->get_bulk_action_nonce() ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_network_users' ) ) {
			return;
		}

		$message = esc_html__( 'Please try again.', 'user-login-history' );
		$status  = false;
		switch ( $this->current_action() ) {
			case 'bulk-delete':
				if ( ! empty( $_POST['bulk-delete-ids'] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized with absint.
					$ids = wp_unslash( $_POST['bulk-delete-ids'] );

					foreach ( $ids as $blog_id => $record_ids ) {
						$record_ids = array_map( 'absint', $record_ids );
						$blog_id    = absint( $blog_id );
						switch_to_blog( $blog_id );
						$status = Db_Helper::delete_rows_by_table_and_ids( $this->table, $record_ids );
						restore_current_blog();
					}

					if ( $status ) {
						$message = esc_html__( 'Selected record(s) deleted.', 'user-login-history' );
					}
				}

				break;
			case 'bulk-delete-all-admin':
				global $wpdb;
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Transaction control statement.
				$wpdb->query( 'START TRANSACTION' );
				$blog_ids = get_sites( array( 'fields' => 'ids' ) );
				if ( is_array( $blog_ids ) && ! empty( $blog_ids ) ) {
					foreach ( $blog_ids as $blog_id ) {
						switch_to_blog( $blog_id );
						$status = $wpdb->query( $wpdb->prepare( 'TRUNCATE TABLE %i', $wpdb->prefix . $this->table ) );
						restore_current_blog();
						if ( ! $status ) {
							break;
						}
					}
				}

				if ( $status ) {
					$message = esc_html__( 'All records deleted.', 'user-login-history' );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Transaction control statement.
					$wpdb->query( 'COMMIT' );
				} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Transaction control statement.
					$wpdb->query( 'ROLLBACK' );
				}

				break;
		}

		$this->admin_notice->add_notice( $message, $status ? 'success' : 'error' );
		wp_safe_redirect( esc_url( 'admin.php?page=' . sanitize_text_field( wp_unslash( $_GET['page'] ?? '' ) ) ) );
		exit;
	}

	/**
	 * Process the single action
	 */
	public function process_single_action() {

		if ( ! wp_verify_nonce( sanitize_file_name( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), $this->get_delete_action_nonce() ) ) {
			return;
		}

		$the_get = $_GET;

		if ( empty( $the_get['record_id'] ) || empty( $the_get['blog_id'] ) ) {
			return;
		}

		if ( ! is_numeric( $the_get['record_id'] ) || $the_get['record_id'] <= 0 ) {
			return;
		}

		if ( ! is_numeric( $the_get['blog_id'] ) || $the_get['blog_id'] <= 0 ) {
			return;
		}

		if ( ! current_user_can( 'manage_network_users' ) ) {
			return;
		}

		$id      = absint( $_GET['record_id'] ?? 0 );
		$blog_id = absint( $_GET['blog_id'] ?? 0 );

		$blog_ids = get_sites(
			array(
				'site__in' => array( $blog_id ),
			)
		);

		if ( empty( $blog_ids ) ) {
			return;
		}

		$message = esc_html__( 'Please try again.', 'user-login-history' );
		$status  = false;

		switch ( $this->current_action() ) {
			case $this->delete_action:
				switch_to_blog( $blog_id );
				$status = Db_Helper::delete_rows_by_table_and_ids( $this->table, array( $id ) );
				restore_current_blog();
				if ( $status ) {
					$message = esc_html__( 'Selected record deleted.', 'user-login-history' );
				}

				break;
		}

		$this->admin_notice->add_notice( $message, $status ? 'success' : 'error' );
		wp_safe_redirect( esc_url( 'admin.php?page=' . sanitize_text_field( wp_unslash( $_GET['page'] ?? '' ) ) ) );
		exit;
	}
}
