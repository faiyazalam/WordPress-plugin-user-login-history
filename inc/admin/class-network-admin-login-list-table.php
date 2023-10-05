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

use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;
use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Helpers\Tool as Tool_Helper;
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Common\Abstracts\List_Table as List_Table_Abstract;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;
use User_Login_History\Inc\Common\Interfaces\Admin_List_Table as Admin_List_Table_Interface;

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

		$where_query = parent::prepare_where_query();

		if ( ! empty( $_GET['is_super_admin'] ) && in_array( $_GET['is_super_admin'], array( 'yes', 'no' ) ) ) {
			$where_query .= " AND `FaUserLogin`.`is_super_admin` = '" . absint( 'yes' == $_GET['is_super_admin'] ) . "'";
		}

		return $where_query;
	}

	/**
	 * Get array of blog ids to be used in where sql query.
	 *
	 * @return array
	 */
	private function get_blog_ids_for_where_clause() {
		return ! empty( $_GET['blog_id'] ) && $_GET['blog_id'] > 0 ? array( $_GET['blog_id'] ) : Db_Helper::get_blog_ids_by_site_id();
	}

	/**
	 * Prepares main and count sql queries.
	 *
	 * @global type $wpdb
	 */
	private function prepare_sql_queries() {
		global $wpdb;
		$where_query = $this->prepare_where_query();

		$i        = 0;
		$blog_ids = $this->get_blog_ids_for_where_clause();
		foreach ( $blog_ids as $blog_id ) {
			$blog_prefix = $wpdb->get_blog_prefix( $blog_id );
			$table       = $blog_prefix . $this->table;

			if ( ! $this->is_plugin_active_for_network && ! Db_Helper::is_table_exist( $table ) ) {
				continue;
			}

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

			if ( $where_query ) {
				$this->rows_sql  .= $where_query;
				$this->count_sql .= $where_query;
			}

			$i++;
		}

		$this->rows_sql  = "SELECT * FROM ({$this->rows_sql}) AS FaUserLoginAllRows";
		$this->count_sql = "SELECT SUM(count) as total FROM ({$this->count_sql}) AS FaUserLoginCount";

	}

	/**
	 * Get the columns.
	 */
	public function get_columns() {
		$columns = array_merge(
			parent::get_columns(),
			array(
				'is_super_admin' => esc_html__( 'Super Admin', 'faulh' ),
			)
		);
		$columns = Tool_Helper::array_insert_after( $columns, 'user_id', array( 'blog_id' => esc_html__( 'Blog ID', 'faulh' ) ) );
		return apply_filters( $this->plugin_name . '_network_admin_login_list_get_columns', $columns );
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

		return apply_filters( $this->plugin_name . '_network_admin_login_list_get_sortable_columns', $columns );
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
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$direction            = ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : ' ASC';
			$sanitize_sql_orderby = sanitize_sql_orderby( $_REQUEST['orderby'] . ' ' . $direction );
			if ( $sanitize_sql_orderby ) {
				$this->rows_sql .= ' ORDER BY ' . $sanitize_sql_orderby;
			}
		} else {
			$this->rows_sql .= ' ORDER BY time_login DESC';
		}

		if ( $per_page > 0 ) {
			$this->rows_sql .= " LIMIT $per_page";
			$this->rows_sql .= ' OFFSET   ' . ( $page_number - 1 ) * $per_page;
		}

		return Db_Helper::get_results( $this->rows_sql );
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public function record_count() {
		return Db_Helper::get_var( $this->count_sql );
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
			'delete' => sprintf( '<a href="?page=%s&action=%s&blog_id=%s&record_id=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), $this->delete_action, absint( $item['blog_id'] ), absint( $item['id'] ), $delete_nonce, esc_html__( 'Delete', 'faulh' ) ),
		);

		return $title . $this->row_actions( $actions );
	}

	/**
	 * Validate the request for bulk action
	 */
	private function is_valid_request_to_process_bulk_action() {
		$nonce = '_wpnonce';
		return isset( $_POST[ $this->get_bulk_action_form() ] ) && ! empty( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], $this->get_bulk_action_nonce() ) && current_user_can( 'administrator' );
	}

	/**
	 * Validate the request for single action
	 */
	private function is_valid_request_to_process_single_action() {
		$nonce = '_wpnonce';
		return ! empty( $_GET['record_id'] ) && $_GET['record_id'] > 0 && ! empty( $_GET['blog_id'] ) && $_GET['blog_id'] > 0 && ! empty( $_GET[ $nonce ] ) && wp_verify_nonce( $_GET[ $nonce ], $this->get_delete_action_nonce() ) && current_user_can( 'administrator' );
	}

	/**
	 * Process the bulk action
	 */
	public function process_bulk_action() {
		if ( ! $this->is_valid_request_to_process_bulk_action() ) {
			return;
		}

		$message = esc_html__( 'Please try again.', 'faulh' );
		$status  = false;
		switch ( $this->current_action() ) {
			case 'bulk-delete':
				if ( ! empty( $_POST['bulk-delete-ids'] ) ) {
					$ids = $_POST['bulk-delete-ids'];

					foreach ( $ids as $blog_id => $record_ids ) {
						switch_to_blog( $blog_id );
						$status = Db_Helper::delete_rows_by_table_and_ids( $this->table, $record_ids );
						restore_current_blog();
					}

					if ( $status ) {
						$message = esc_html__( 'Selected record(s) deleted.', 'faulh' );
					}
				}

				break;
			case 'bulk-delete-all-admin':
				Db_Helper::query( 'START TRANSACTION' );
				$blog_ids = Db_Helper::get_blog_ids_by_site_id();
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$status = Db_Helper::truncate_table( $this->table );
					restore_current_blog();
					if ( ! $status ) {
						break;
					}
				}

				if ( $status ) {
					$message = esc_html__( 'All records deleted.', 'faulh' );
					Db_Helper::query( 'COMMIT' );
				} else {
					Db_Helper::query( 'ROLLBACK' );
				}

				break;
		}

		$this->admin_notice->add_notice( $message, $status ? 'success' : 'error' );
		wp_safe_redirect( esc_url( 'admin.php?page=' . $_GET['page'] ) );
		exit;
	}

	/**
	 * Process the single action
	 */
	public function process_single_action() {
		if ( ! $this->is_valid_request_to_process_single_action() ) {
			return;
		}

		$id      = absint( $_GET['record_id'] );
		$blog_id = absint( $_GET['blog_id'] );

		if ( ! Db_Helper::is_blog_exist( $blog_id ) ) {
			return;
		}

		$message = esc_html__( 'Please try again.', 'faulh' );
		$status  = false;

		switch ( $this->current_action() ) {
			case $this->delete_action:
				switch_to_blog( $blog_id );
				$status = Db_Helper::delete_rows_by_table_and_ids( $this->table, array( $id ) );
				restore_current_blog();
				if ( $status ) {
					$message = esc_html__( 'Selected record deleted.', 'faulh' );
				}

				break;
		}

		$this->admin_notice->add_notice( $message, $status ? 'success' : 'error' );
		wp_safe_redirect( esc_url( 'admin.php?page=' . $_GET['page'] ) );
		exit;
	}

}
