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

namespace User_Login_History\Inc\Common\Abstracts;

use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;
use User_Login_History\Inc\Common\Helpers\Validation as Validation_Helper;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Backend Functionality.
 */
abstract class List_Table extends \WP_List_Table {

	const DEFAULT_TIMEZONE = 'UTC';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * Holds timezone.
	 *
	 * @var string
	 */
	protected $timezone;

	/**
	 * Holds symbol to display for unknown value.
	 *
	 * @var string|HTML
	 */
	protected $unknown_symbol = '<span aria-hidden="true">—</span>';

	/**
	 * The table name.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Some properties to be used in HTML form and its processing actions
	 */

	/**
	 * Form name for bulk action
	 *
	 * @var string
	 */
	protected $bulk_action_form;

	/**
	 * Delete action name
	 *
	 * @var string
	 */
	protected $delete_action;

	/**
	 * Delete action nonce
	 *
	 * @var string
	 */
	protected $delete_action_nonce;

	/**
	 * Bulk action nonce
	 *
	 * @var string
	 */
	protected $bulk_action_nonce;

	/**
	 * CSV field name
	 *
	 * @var string
	 */
	protected $csv_field_name = 'csv';

	/**
	 * CSV field nonce
	 *
	 * @var string
	 */
	protected $csv_nonce_name = 'csv_nonce';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @param array  $args The arguments.
	 */
	public function __construct( $plugin_name, $version, $args = array() ) {
		parent::__construct( $args );
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->init();
	}

	/**
	 * Hooked with init action.
	 */
	public function init() {
		$this->set_bulk_action_form( $this->_args['singular'] . '_form' );
		$this->set_delete_action_nonce( $this->_args['singular'] . '_delete_none' );
		$this->set_bulk_action_nonce( 'bulk-' . $this->_args['plural'] );
	}

	/**
	 * Set unknown symbol.
	 *
	 * @param type $unknown_symbol The symbol.
	 * @return $this
	 */
	public function set_unknown_symbol( $unknown_symbol ) {
		$this->unknown_symbol = $unknown_symbol;
		return $this;
	}

	/**
	 * Get unknown symbol.
	 *
	 * @return string
	 */
	public function get_unknown_symbol() {
		return $this->unknown_symbol;
	}

	/**
	 * Sets the timezone to be used for table listing.
	 *
	 * @param string $timezone The timezone.
	 */
	public function set_timezone( $timezone = '' ) {
		$this->timezone = ! empty( $timezone ) ? $timezone : self::DEFAULT_TIMEZONE;
	}

	/**
	 * Gets the timezone to be used for table listing.
	 *
	 * @return string The timezone.
	 */
	public function get_timezone() {
		return $this->timezone;
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		$per_page              = $this->get_items_per_page( $this->plugin_name . '_rows_per_page' );

		$this->set_pagination_args(
			array(
				'total_items' => $this->record_count(),
				'per_page'    => $per_page,
			)
		);

		$this->items = $this->get_rows( $per_page, $this->get_pagenum() );
	}

	/**
	 * Check if value is empty.
	 *
	 * @param mixed $value The value.
	 * @return bool
	 */
	protected function is_empty( $value = '' ) {
		return Validation_Helper::is_empty( $value );
	}

	/**
	 * Time-zone edit link.
	 *
	 * @return string
	 */
	public function timezone_edit_link() {
		return esc_html__( 'This table is showing time in the timezone', 'faulh' ) . ' - <strong>' . $this->get_timezone() . "</strong>&nbsp;<a class='edit-link' href='" . get_edit_user_link() . '#' . $this->plugin_name . "'>" . esc_html__( 'Edit', 'faulh' ) . '</a>';
	}

	/**
	 * Get all the rows.
	 *
	 * @return array
	 */
	public function get_all_rows() {
		return $this->get_rows( 0 );
	}

	/**
	 * Setter.
	 *
	 * @param string $value The value.
	 * @return $this
	 */
	protected function set_csv_field_name( $value ) {
		$this->csv_field_name = $value;
		return $this;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_csv_field_name() {
		return $this->csv_field_name;
	}

	/**
	 * Setter.
	 *
	 * @param string $value The value.
	 * @return $this
	 */
	protected function set_csv_nonce_name( $value ) {
		$this->csv_nonce_name = $value;
		return $this;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_csv_nonce_name() {
		return $this->csv_nonce_name;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_bulk_action_form() {
		return $this->bulk_action_form;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_bulk_action_nonce() {
		return $this->bulk_action_nonce;
	}

	/**
	 * Getter.
	 *
	 * @return string
	 */
	public function get_delete_action_nonce() {
		return $this->delete_action_nonce;
	}

	/**
	 * Setter.
	 *
	 * @param string $value The value.
	 * @return $this
	 */
	public function set_bulk_action_form( $value ) {
		$this->bulk_action_form = $value;
		return $this;
	}

	/**
	 * Setter.
	 *
	 * @param string $value The value.
	 * @return $this
	 */
	public function set_bulk_action_nonce( $value ) {
		$this->bulk_action_nonce = $value;
		return $this;
	}

	/**
	 * Setter.
	 *
	 * @param string $value The value.
	 * @return $this
	 */
	public function set_delete_action_nonce( $value ) {
		$this->delete_action_nonce = $value;
		return $this;
	}

}
