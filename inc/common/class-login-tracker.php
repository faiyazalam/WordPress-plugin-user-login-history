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

namespace User_Login_History\Inc\Common;

use User_Login_History\Inc\Common\Helpers\Browser as Browser_Helper;
use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Helpers\Geo as Geo_Helper;
use User_Login_History\Inc\Common\Helpers\Error_Log as Error_Log_Helper;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;
use User_Login_History\Inc\Admin\Network_Admin_Settings;
use User_Login_History\Inc\Admin\Settings as Admin_Settings;

/**
 * The admin-specific functionality of the plugin.
 */
class Login_Tracker {

	/**
	 * Login Status - Login.
	 */
	const LOGIN_STATUS_LOGIN = 'login';

	/**
	 * Login Status - Fail.
	 */
	const LOGIN_STATUS_FAIL = 'fail';

	/**
	 * Login Status - Logout.
	 */
	const LOGIN_STATUS_LOGOUT = 'logout';

	/**
	 * LOGIN_STATUS_BLOCK
	 * This will be saved in db if user is not allowed to login on another blog.
	 * This is for network enabled mode only.
	 */
	const LOGIN_STATUS_BLOCK = 'block';

	/**
	 * Holds the key for the current blog id on which user gets logged in.
	 */
	const LOGIN_BLOG_ID_KEY = 'login_blog_id';

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Stores user session token.
	 *
	 * @var string $session_token The session token of user.
	 */
	private $session_token = '';

	/**
	 * Stores the status of login.
	 *
	 * @var string|bool $login_status The login status of user.
	 */
	private $login_status = false;

	/**
	 * Stores the blog id on which user is just logged in.
	 *
	 * @var int
	 */
	private $current_loggedin_blog_id;

	/**
	 * The table name.
	 *
	 * @var string
	 */
	private $table;

	/**
	 * The geo tracker setting value.
	 *
	 * @var bool
	 */
	private $is_geo_tracker_enabled = false;

	/**
	 * The cross blog login setting value.
	 *
	 * @var bool
	 */
	private $is_cross_blog_login_blocked = false;

	/**
	 * The cross blog login setting message value.
	 *
	 * @var string
	 */
	private $message_for_cross_blog_login = 'No cross blog login is allowed. Please contact administrator.';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @param string $table The table name.
	 */
	public function __construct( $plugin_name, $version, $table ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->table       = $table;
	}

	/**
	 * Initialize.
	 * Hooked with init action.
	 */
	public function init() {
		$this->set_current_loggedin_blog_id();
		$this->update_time_last_seen();
	}

	/**
	 * Set geo setting.
	 *
	 * @param bool $status The value.
	 * @return $this
	 */
	public function set_is_geo_tracker_enabled( $status ) {
		$this->is_geo_tracker_enabled = $status;
		return $this;
	}

	/**
	 * Check if geo setting enabled.
	 *
	 * @return bool
	 */
	public function get_is_geo_tracker_enabled() {
		return $this->is_geo_tracker_enabled;
	}

	/**
	 * Gets the blog id from the session.
	 *
	 * @return int The blog id from which user gets logged in.
	 */
	public function get_current_login_blog_id_from_session() {
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		$wp_session_tokens = \WP_Session_Tokens::get_instance( $user_id );
		$session           = $wp_session_tokens->get( wp_get_session_token() );
		return ! empty( $session[ $this->plugin_name ][ self::LOGIN_BLOG_ID_KEY ] ) ? (int) $session[ $this->plugin_name ][ self::LOGIN_BLOG_ID_KEY ] : false;
	}

	/**
	 * Attach session info during login.
	 *
	 * @param array $array The array data.
	 * @param int   $user_id The user id.
	 * @return array
	 */
	public function attach_session_information( $array, $user_id ) {
		$array[ $this->plugin_name ][ self::LOGIN_BLOG_ID_KEY ] = get_current_blog_id();
		return $array;
	}

	/**
	 * Set the blog id on which user is just logged in.
	 */
	private function set_current_loggedin_blog_id() {
		$this->current_loggedin_blog_id = $this->get_current_login_blog_id_from_session();
	}

	/**
	 * Set setttings for cross blog login.
	 *
	 * @param bool $value The value.
	 * @return $this
	 */
	public function set_is_cross_blog_login_blocked( $value ) {
		$this->is_cross_blog_login_blocked = (bool) $value;
		return $this;
	}

	/**
	 * Get setttings for cross blog login.
	 *
	 * @return bool
	 */
	public function get_is_cross_blog_login_blocked() {
		return $this->is_cross_blog_login_blocked;
	}

	/**
	 * Set message for cross blog login.
	 *
	 * @param string $message The message.
	 * @return $this
	 */
	public function set_message_for_cross_blog_login( $message ) {
		$this->message_for_cross_blog_login = $message;
		return $this;
	}

	/**
	 * Get message for cross blog login.
	 *
	 * @return string
	 */
	public function get_message_for_cross_blog_login() {
		return $this->message_for_cross_blog_login;
	}

	/**
	 * Blocks user if the user is not allowed to
	 * login on another blog on the network.
	 *
	 * @param int $user_id The user id.
	 */
	private function is_blocked_user_on_current_blog( $user_id ) {
		if ( ! is_multisite() || is_super_admin( $user_id ) || is_user_member_of_blog( $user_id ) || ! $this->get_is_cross_blog_login_blocked() ) {
			return;
		}

		$this->login_status             = self::LOGIN_STATUS_BLOCK;
		$this->current_loggedin_blog_id = get_current_blog_id();
		wp_logout();
		wp_die( $this->get_message_for_cross_blog_login() );
	}

	/**
	 * Saves user login details.
	 *
	 * @access private
	 * @param string $user_login username.
	 * @param object $user WP_User object.
	 * @param string $status login, logout, block etc.
	 */
	private function save_login( $user_login, $user, $status = '' ) {
		if ( empty( $user_login ) ) {
			return false;
		}

		$unknown        = 'unknown';
		$current_date   = Date_Time_Helper::get_current_date_time();
		$user_id        = ! empty( $user->ID ) ? $user->ID : false;
		$browser_helper = new Browser_Helper();

		$data = array(
			'user_id'          => $user_id,
			'session_token'    => $this->get_session_token(),
			'username'         => $user_login,
			'time_login'       => $current_date,
			'ip_address'       => Geo_Helper::get_ip(),
			'time_last_seen'   => $current_date,
			'browser'          => $browser_helper->getBrowser(),
			'browser_version'  => $browser_helper->getVersion(),
			'operating_system' => $browser_helper->getPlatform(),
			'old_role'         => ! empty( $user->roles ) ? implode( ',', $user->roles ) : '',
			'user_agent'       => isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : $unknown,
			'login_status'     => $status,
			'is_super_admin'   => is_multisite() ? is_super_admin( $user_id ) : false,
		);

		if ( $this->get_is_geo_tracker_enabled() ) {
			$geo_location = Geo_Helper::get_geo_location();
			$geo_fields   = array( 'country_name', 'country_code', 'timezone' );
			foreach ( $geo_fields as $geo_field ) {
				$data[ $geo_field ] = ! empty( $geo_location[ $geo_field ] ) ? $geo_location[ $geo_field ] : $unknown;
			}
		}
		// this is used to modify data before saving in db.
		$filtered_data = apply_filters( 'faulh_before_save_login', $data );

		if ( is_array( $filtered_data ) && ! empty( $filtered_data ) ) {
			$data = array_merge( $data, $filtered_data );
		}

		if ( ! Db_Helper::insert( $this->table, $data ) ) {
			return;
		}

		do_action( 'faulh_after_save_login', $data );

		if ( self::LOGIN_STATUS_FAIL == $status ) {
			return;
		}

		$this->is_blocked_user_on_current_blog( $user_id );
	}

	/**
	 * Update last seen time for the current user.
	 *
	 * @access  public
	 * @global object $wpdb
	 * @return bool|int The number of records updated.
	 */
	public function update_time_last_seen() {

		$current_user = wp_get_current_user();
		if ( ! $current_user->ID ) {
			return;
		}

		global $wpdb;
		$table         = $wpdb->get_blog_prefix( $this->current_loggedin_blog_id ) . $this->table;
		$current_date  = Date_Time_Helper::get_current_date_time();
		$session_token = wp_get_session_token();

		$sql = "update $table set time_last_seen='$current_date' where session_token = '$session_token' and user_id = '{$current_user->ID}' ";

		Db_Helper::query( $sql );

		$data = array(
			'time_last_seen' => $current_date,
			'session_token'  => $session_token,
			'user_id'        => $current_user->ID,
		);
		do_action( 'faulh_update_time_last_seen', $data );
	}

	/**
	 * Fires if login failed.
	 *
	 * @param string $user_login The username.
	 */
	public function on_login_failed( $user_login ) {
		$this->save_login( $user_login, null, self::LOGIN_STATUS_FAIL );
	}

	/**
	 * Fires on logout.
	 * Saves logout time of current user.
	 */
	public function on_logout() {
		$session_token = wp_get_session_token();
		if ( ! $session_token ) {
			return;
		}

		global $wpdb;
		$time_logout  = Date_Time_Helper::get_current_date_time();
		$login_status = $this->login_status ? $this->login_status : self::LOGIN_STATUS_LOGOUT;
		$table        = $wpdb->get_blog_prefix( $this->current_loggedin_blog_id ) . $this->table;
		$sql          = "update $table  set time_logout='$time_logout', time_last_seen='$time_logout', login_status = '" . $login_status . "' where session_token = '" . $session_token . "' ";

		Db_Helper::query( $sql );

		$data = array(
			'time_logout'    => $time_logout,
			'time_last_seen' => $time_logout,
			'session_token'  => $session_token,
		);
		do_action( 'faulh_logout', $data );
	}

	/**
	 * Sets session token.
	 *
	 * @param string $token The session token.
	 */
	private function set_session_token( $token = '' ) {
		$this->session_token = $token;
	}

	/**
	 * Callback function for the action hook - set_logged_in_cookie.
	 *
	 * @param type $logged_in_cookie See the hook.
	 * @param type $expire See the hook.
	 * @param type $expiration See the hook.
	 * @param type $user_id See the hook.
	 * @param type $logged_in_text See the hook.
	 * @param type $token See the hook.
	 */
	public function set_logged_in_cookie( $logged_in_cookie, $expire, $expiration, $user_id, $logged_in_text, $token ) {
		$this->set_session_token( $token );
		$user = get_user_by( 'id', $user_id );
		$this->save_login( $user->user_login, $user, self::LOGIN_STATUS_LOGIN );
	}

	/**
	 * Gets session token.
	 *
	 * @return string The session token.
	 */
	public function get_session_token() {
		return $this->session_token;
	}

}
