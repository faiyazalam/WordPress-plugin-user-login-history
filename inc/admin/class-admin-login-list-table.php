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
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Common\Abstracts\List_Table as List_Table_Abstract;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;
use User_Login_History\Inc\Common\Interfaces\Admin_List_Table as Admin_List_Table_Interface;

/**
 * Render the login listing page.
 */
final class Admin_Login_List_Table extends Login_List_Table implements Admin_Csv_Interface, Admin_List_Table_Interface {

	/**
	 * Retrieves the records.
	 *
	 * @global object $wpdb The object.
	 * @param int $per_page The limit.
	 * @param int $page_number The page number.
	 * @return mixed
	 */
	public function get_rows( $per_page = 20, $page_number = 1 ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table;
		$sql   = ' SELECT'
				. ' FaUserLogin.*, '
				. ' TIMESTAMPDIFF(SECOND,FaUserLogin.time_login,FaUserLogin.time_last_seen) as duration'
				. ' FROM ' . $table . '  AS FaUserLogin'
				. ' WHERE 1 ';

		$where_query = $this->prepare_where_query();

		if ( $where_query ) {
			$sql .= $where_query;
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$direction            = ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : ' ASC';
			$sanitize_sql_orderby = sanitize_sql_orderby( $_REQUEST['orderby'] . ' ' . $direction );
			if ( $sanitize_sql_orderby ) {
				$sql .= ' ORDER BY ' . $sanitize_sql_orderby;
			}
		} else {
			$sql .= ' ORDER BY id DESC';
		}

		if ( $per_page > 0 ) {
			$sql .= " LIMIT $per_page";
			$sql .= ' OFFSET   ' . ( $page_number - 1 ) * $per_page;
		}

		return Db_Helper::get_results( $sql );
	}

	/**
	 * Returns the count of the records.
	 *
	 * @global object $wpdb The object.
	 * @return mixed
	 */
	public function record_count() {
		global $wpdb;
		$table = $wpdb->prefix . $this->table;
		$sql   = ' SELECT'
				. ' COUNT(FaUserLogin.id) AS total'
				. ' FROM ' . $table . ' AS FaUserLogin'
				. ' WHERE 1 ';
		$where_query = $this->prepare_where_query();

		if ( $where_query ) {
			$sql .= $where_query;
		}

		return Db_Helper::get_var( $sql );
	}

	/**
	 * Get username column value.
	 *
	 * @overridden
	 *
	 * @param array $item The record.
	 * @return string
	 */
	public function column_username( $item ) {
		$username = $this->is_empty( $item['username'] ) ? $this->unknown_symbol : esc_html( $item['username'] );
		if ( $this->is_empty( $item['user_id'] ) ) {
			$title = $username;
		} else {
			$edit_link = get_edit_user_link( $item['user_id'] );
			$title     = ! empty( $edit_link ) ? "<a href='" . $edit_link . "'>" . $username . '</a>' : '<strong>' . $username . '</strong>';
		}

		$delete_nonce = wp_create_nonce( $this->delete_action_nonce );
		$actions      = array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&record_id=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), $this->delete_action, absint( $item['id'] ), $delete_nonce, esc_html__( 'Delete', 'faulh' ) ),
		);
		return $title . $this->row_actions( $actions );
	}

	/**
	 * Get the checkbox column.
	 *
	 *  @overridden
	 *
	 * @param array $item The record.
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="bulk-action-ids[]" value="%s" />', $item['id'] );
	}

	/**
	 * Handles the bulk actions.
	 */
	public function process_bulk_action() {
		$nonce = '_wpnonce';

		if ( ! isset( $_POST[ $this->get_bulk_action_form() ] ) || empty( $_POST[ $nonce ] ) || ! wp_verify_nonce( $_POST[ $nonce ], $this->get_bulk_action_nonce() ) || ! current_user_can( 'administrator' ) ) {
			return;
		}

		$message = esc_html__( 'Please try again.', 'faulh' );
		$status  = false;

		switch ( $this->current_action() ) {

			case 'bulk-delete':
				if ( ! empty( $_POST['bulk-action-ids'] ) ) {
					$status = Db_Helper::delete_rows_by_table_and_ids( $this->table, $_POST['bulk-action-ids'] );
					if ( $status ) {
						$message = esc_html__( 'Selected record(s) deleted.', 'faulh' );
					}
				}

				break;

			case 'bulk-delete-all-admin':
				$status = Db_Helper::truncate_table( $this->table );
				if ( $status ) {
					$message = esc_html__( 'All record(s) deleted.', 'faulh' );
				}
				break;
		}

		$this->admin_notice->add_notice( $message, $status ? 'success' : 'error' );
		wp_safe_redirect( esc_url( 'admin.php?page=' . $_GET['page'] ) );
		exit;
	}

	/**
	 * Handles the single action.
	 */
	public function process_single_action() {
		$nonce = '_wpnonce';

		if ( empty( $_GET['record_id'] ) || empty( $_GET[ $nonce ] ) || ! wp_verify_nonce( $_GET[ $nonce ], $this->get_delete_action_nonce() ) || ! current_user_can( 'administrator' ) ) {
			return;
		}

		$id      = absint( $_GET['record_id'] );
		$status  = false;
		$message = esc_html__( 'Please try again.', 'faulh' );
		switch ( $this->current_action() ) {
			case $this->delete_action:
				$status = Db_Helper::delete_rows_by_table_and_ids( $this->table, array( $id ) );
				if ( $status ) {
					$message = esc_html__( 'Record deleted.', 'faulh' );
				}
				break;
		}

		$this->admin_notice->add_notice( $message, $status ? 'success' : 'error' );
		wp_safe_redirect( esc_url( 'admin.php?page=' . $_GET['page'] ) );
		exit;
	}

	/**
	 * Get the columns.
	 *
	 * @overridden
	 */
	public function get_columns() {
		return apply_filters( $this->plugin_name . '_admin_login_list_get_columns', parent::get_columns() );
	}

	/**
	 * Get the sortable columns.
	 *
	 * @overridden
	 */
	public function get_sortable_columns() {
		return apply_filters( $this->plugin_name . '_admin_login_list_get_columns', parent::get_sortable_columns() );
	}

}
