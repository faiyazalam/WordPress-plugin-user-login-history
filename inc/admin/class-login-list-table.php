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
use User_Login_History\Inc\Common\Login_Tracker;
use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
use User_Login_History\Inc\Common\Helpers;

/**
 * Base class to handle admin and network admin login listing functionality.
 */
abstract class Login_List_Table extends List_Table_Abstract {

	/**
	 * Holds the interval for online status
	 *
	 * @var int
	 */
	protected $online_duration;

	/**
	 * Holds the interval for idle status
	 *
	 * @var int
	 */
	protected $idle_duration;

	/**
	 * Holds the instance of Admin_Notice.
	 *
	 * @var Admin_Notice
	 */
	protected $admin_notice;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string       $plugin_name  The name of this plugin.
	 * @param string       $version      The version of this plugin.
	 * @param Admin_Notice $admin_notice The admin notice object.
	 */
	public function __construct( $plugin_name, $version, Admin_Notice $admin_notice ) {
		$args = array(
			'singular' => $plugin_name . '_user_login',
			'plural'   => $plugin_name . '_user_logins',
		);
		parent::__construct( $plugin_name, $version, $args );
		$this->admin_notice = $admin_notice;
	}

	/**
	 * Set interval for online status.
	 *
	 * @param int $duration The duration.
	 * @return $this
	 */
	public function set_online_duration( $duration ) {
		$this->online_duration = $duration;
		return $this;
	}

	/**
	 * Get interval for online status.
	 *
	 * @return int
	 */
	public function get_online_duration() {
		return $this->online_duration;
	}

	/**
	 * Set interval for idle status.
	 *
	 * @param int $duration The duration.
	 * @return $this
	 */
	public function set_idle_duration( $duration ) {
		$this->idle_duration = $duration;
		return $this;
	}

	/**
	 * Get interval for idle status.
	 *
	 * @return int
	 */
	public function get_idle_duration() {
		return $this->idle_duration;
	}

	/**
	 * Overrides and initializes class properties
	 */
	public function init() {
		parent::init();
		$this->table         = NS\PLUGIN_TABLE_FA_USER_LOGINS;
		$this->delete_action = $this->_args['singular'] . '_delete';
	}

	/**
	 * Prepares the where query.
	 *
	 * @return string
	 */
	public function prepare_where_query() {

		$where_query = '';

		$fields = array(
			'user_id',
			'username',
			'browser',
			'operating_system',
			'ip_address',
			'timezone',
			'country_name',
			'old_role',
		);

		foreach ( $fields as $field ) {
			if ( ! empty( $_GET[ $field ] ) ) {
				$where_query .= " AND `FaUserLogin`.`$field` = '" . esc_sql( trim( $_GET[ $field ] ) ) . "'";
			}
		}

		if ( ! empty( $_GET['date_type'] ) ) {
			$user_profile   = new User_Profile( $this->plugin_name, $this->version );
			$input_timezone = $user_profile->get_user_timezone();
			$date_type      = $_GET['date_type'];

			if ( in_array( $date_type, array_keys( Template_Helper::time_field_types() ) ) ) {

				$key_date_from = 'date_from';
				$key_date_to   = 'date_to';

				if ( ! empty( $_GET[ $key_date_from ] ) && ! empty( $_GET[ $key_date_to ] ) ) {
					$date_type = esc_sql( $date_type );
					$date_from = Date_Time_Helper::convert_timezone( $_GET[ $key_date_from ] . ' 00:00:00', $input_timezone );
					$date_to   = Date_Time_Helper::convert_timezone( $_GET[ $key_date_to ] . ' 23:59:59', $input_timezone );

					if ( $date_from && $date_to ) {
						$where_query .= " AND `FaUserLogin`.`time_$date_type` >= '" . esc_sql( $date_from ) . "'";
						$where_query .= " AND `FaUserLogin`.`time_$date_type` <= '" . esc_sql( $date_to ) . "'";
					}
				} else {
					unset( $_GET[ $key_date_from ] );
					unset( $_GET[ $key_date_to ] );
				}
			}
		}

		if ( ! empty( $_GET['login_status'] ) ) {
			$login_status       = $_GET['login_status'];
			$login_status_value = strtolower( $login_status ) == 'unknown' ? '' : esc_sql( $login_status );
			$where_query       .= " AND `FaUserLogin`.`login_status` = '" . $login_status_value . "'";
		}

		$where_query = apply_filters( 'faulh_admin_prepare_where_query', $where_query );
		return $where_query;
	}

	/**
	 * Returns an associative array containing the bulk action.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete'           => esc_html__( 'Delete Selected Records', 'faulh' ),
			'bulk-delete-all-admin' => esc_html__( 'Delete All Records', 'faulh' ),
		);

		return $actions;
	}

	/**
	 * Get the columns.
	 */
	public function get_columns() {
		return array(
			'cb'               => '<input type="checkbox" />',
			'user_id'          => esc_html__( 'User ID', 'faulh' ),
			'username'         => esc_html__( 'Username', 'faulh' ),
			'role'             => esc_html__( 'Role', 'faulh' ),
			'old_role'         => esc_html__( 'Old Role', 'faulh' ),
			'browser'          => esc_html__( 'Browser', 'faulh' ),
			'operating_system' => esc_html__( 'Operating System', 'faulh' ),
			'ip_address'       => esc_html__( 'IP Address', 'faulh' ),
			'timezone'         => esc_html__( 'Timezone', 'faulh' ),
			'country_name'     => esc_html__( 'Country', 'faulh' ),
			'user_agent'       => esc_html__( 'User Agent', 'faulh' ),
			'duration'         => esc_html__( 'Duration', 'faulh' ),
			'time_last_seen'   => esc_html__( 'Last Seen', 'faulh' ),
			'time_login'       => esc_html__( 'Login', 'faulh' ),
			'time_logout'      => esc_html__( 'Logout', 'faulh' ),
			'login_status'     => esc_html__( 'Login Status', 'faulh' ),
		);
	}

	/**
	 * Overrides
	 */
	public function get_sortable_columns() {
		return array(
			'user_id'          => array( 'user_id', true ),
			'username'         => array( 'username', true ),
			'old_role'         => array( 'old_role', true ),
			'time_login'       => array( 'time_login', false ),
			'time_logout'      => array( 'time_logout', false ),
			'browser'          => array( 'browser', false ),
			'operating_system' => array( 'operating_system', false ),
			'country_name'     => array( 'country_name', false ),
			'time_last_seen'   => array( 'time_last_seen', false ),
			'timezone'         => array( 'timezone', false ),
			'user_agent'       => array( 'user_agent', false ),
			'login_status'     => array( 'login_status', false ),
			'duration'         => array( 'duration', false ),
		);

	}

	/**
	 * Get the value.
	 *
	 * @param array $item The record.
	 * @return string
	 */
	public function column_time_last_seen( $item ) {
		$column_name = 'time_last_seen';

		$time_last_seen_unix = strtotime( $item[ $column_name ] );

		if ( $this->is_empty( $item['user_id'] ) || ! ( $time_last_seen_unix > 0 ) ) {
			return $this->get_unknown_symbol();
		}

		$timezone       = $this->get_timezone();
		$time_last_seen = Date_Time_Helper::convert_format( Date_Time_Helper::convert_timezone( $item[ $column_name ], '', $timezone ) );

		if ( ! $time_last_seen ) {
			return $this->get_unknown_symbol();
		}

		$human_time_diff = human_time_diff( $time_last_seen_unix );
		$is_online_str   = $this->get_online_status( $time_last_seen_unix, $item['login_status'] );
		return "<div class='is_status_$is_online_str' title = '$time_last_seen'>" . $human_time_diff . ' ' . esc_html__( 'ago', 'faulh' ) . '</div>';
	}

	/**
	 *
	 * Get string for login status.
	 *
	 * TODO::Remove parameters - low priority
	 *
	 * @param int    $time_last_seen_unix The timestamp.
	 * @param string $login_status The login status.
	 * @return boolean|string offline, online or idle
	 */
	private function get_online_status( $time_last_seen_unix, $login_status ) {

		$time_last_seen_unix = absint( $time_last_seen_unix );

		if ( ! is_string( $login_status ) || empty( trim( $login_status ) ) || $time_last_seen_unix <= 0 ) {
			return false;
		}

		$online_status = 'offline';

		if ( Login_Tracker::LOGIN_STATUS_LOGIN == $login_status ) {
			$minutes = ( ( time() - $time_last_seen_unix ) / 60 );

			if ( $minutes <= $this->get_online_duration() ) {
				$online_status = 'online';
			} elseif ( $minutes <= $this->get_idle_duration() ) {
				$online_status = 'idle';
			}
		}
		return $online_status;
	}

	/**
	 * Get the value for all the columns.
	 *
	 * @param type $item The record.
	 * @param type $column_name The column name.
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		$timezone = $this->get_timezone();

		$new_column_data = apply_filters( 'manage_faulh_admin_custom_column', '', $item, $column_name );
		if ( $new_column_data ) {
			return $new_column_data;
		}
		$country_code = in_array( strtolower( $item['country_code'] ), array( '', $this->get_unknown_symbol() ) ) ? $this->get_unknown_symbol() : $item['country_code'];

		switch ( $column_name ) {

			case 'user_id':
				return $this->is_empty( $item[ $column_name ] ) ? $this->get_unknown_symbol() : absint( $item[ $column_name ] );

			case 'username':
				return $this->is_empty( $item['username'] ) ? $this->get_unknown_symbol() : esc_html( $item['username'] );

			case 'role':
				if ( $this->is_empty( $item['user_id'] ) ) {
					return $this->get_unknown_symbol();
				}

				if ( is_network_admin() ) {
					switch_to_blog( $item['blog_id'] );
					$user_data = get_userdata( $item['user_id'] );
					restore_current_blog();
				} else {
					$user_data = get_userdata( $item['user_id'] );
				}

				return $this->is_empty( $user_data->roles ) ? $this->get_unknown_symbol() : Helpers\Tool::get_role_names_by_keys( $user_data->roles );

			case 'old_role':
				return $this->is_empty( $item[ $column_name ] ) ? $this->get_unknown_symbol() : Helpers\Tool::get_role_names_by_keys( $item[ $column_name ] );

			case 'browser':
				if ( $this->is_empty( $item[ $column_name ] ) ) {
					return $this->get_unknown_symbol();
				}

				if ( empty( $item['browser_version'] ) ) {
					return esc_html( $item[ $column_name ] );
				}

				return esc_html( $item[ $column_name ] . ' (' . $item['browser_version'] . ')' );

			case 'ip_address':
				return $this->is_empty( $item[ $column_name ] ) ? $this->get_unknown_symbol() : esc_html( $item[ $column_name ] );

			case 'timezone':
				return $this->is_empty( $item[ $column_name ] ) ? $this->get_unknown_symbol() : esc_html( $item[ $column_name ] );

			case 'country_name':
				if ( $this->is_empty( $item[ $column_name ] ) ) {
					return $this->get_unknown_symbol();
				}

				if ( empty( $item['country_code'] ) ) {
					return esc_html( $item[ $column_name ] );
				}

				return esc_html( $item[ $column_name ] . ' (' . $item['country_code'] . ')' );

			case 'country_code':
				return $this->is_empty( $item[ $column_name ] ) ? $this->get_unknown_symbol() : esc_html( $item[ $column_name ] );

			case 'operating_system':
				return $this->is_empty( $item[ $column_name ] ) ? $this->get_unknown_symbol() : esc_html( $item[ $column_name ] );

			case 'time_login':
				if ( ! ( strtotime( $item[ $column_name ] ) > 0 ) ) {
					return $this->get_unknown_symbol();
				}
				$time_login = Date_Time_Helper::convert_format( Date_Time_Helper::convert_timezone( $item[ $column_name ], '', $timezone ) );
				return $time_login ? $time_login : $this->get_unknown_symbol();

			case 'time_logout':
				if ( $this->is_empty( $item['user_id'] ) || ! ( strtotime( (string)$item[ $column_name ] ) > 0 ) ) {
					return $this->get_unknown_symbol();
				}
				$time_logout = Date_Time_Helper::convert_format( Date_Time_Helper::convert_timezone( $item[ $column_name ], '', $timezone ) );
				return $time_logout ? $time_logout : $this->get_unknown_symbol();

			case 'time_last_seen':
				$time_last_seen_unix = strtotime( $item[ $column_name ] );
				if ( $this->is_empty( $item['user_id'] ) || ! ( $time_last_seen_unix > 0 ) ) {
					return $this->get_unknown_symbol();
				}
				$time_last_seen = Date_Time_Helper::convert_format( Date_Time_Helper::convert_timezone( $item[ $column_name ], '', $timezone ) );

				if ( ! $time_last_seen ) {
					return $this->get_unknown_symbol();
				}

				return human_time_diff( $time_last_seen_unix ) . ' ' . esc_html__( 'ago', 'faulh' ) . " ($time_last_seen)";

			case 'user_agent':
				return $this->is_empty( $item[ $column_name ] ) ? $this->get_unknown_symbol() : esc_html( $item[ $column_name ] );

			case 'duration':
				if ( $this->is_empty( $item['time_login'] ) || ! ( strtotime( $item['time_login'] ) > 0 ) ) {
					return $this->get_unknown_symbol();
				}

				if ( $this->is_empty( $item['time_last_seen'] ) || ! ( strtotime( $item['time_login'] ) > 0 ) ) {
					return $this->get_unknown_symbol();
				}
				return human_time_diff( strtotime( $item['time_login'] ), strtotime( $item['time_last_seen'] ) );

			case 'login_status':
				$login_statuses = Template_Helper::login_statuses();
				return ! empty( $login_statuses[ $item[ $column_name ] ] ) ? $login_statuses[ $item[ $column_name ] ] : $this->get_unknown_symbol();

			case 'blog_id':
				return ! empty( $item[ $column_name ] ) ? (int) $item[ $column_name ] : $this->get_unknown_symbol();

			case 'is_super_admin':
				$super_admin_statuses = Template_Helper::super_admin_statuses();
				return $super_admin_statuses[ $item[ $column_name ] ? 'yes' : 'no' ];

			default:
				return print_r( $item, true );
		}
	}

}
