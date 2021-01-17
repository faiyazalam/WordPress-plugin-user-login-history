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

/**
 * Backend Functionality.
 */
abstract class User_Profile {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The usermeta key.
	 *
	 * @var      string    $usermeta_key_timezone The timezone usermeta key.
	 */
	protected $usermeta_key_timezone;

	/**
	 * The user id.
	 *
	 * @var      int    $user_id The user id.
	 */
	protected $user_id;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Callback function for the hook - init.
	 */
	public function init() {
		$this->set_user_id();
		$this->set_usermeta_key_timezone();
	}

	/**
	 * Set user id.
	 *
	 * @global object $current_user The current user.
	 * @param int $user_id The user id.
	 * @return $this
	 */
	public function set_user_id( $user_id = null ) {

		if ( ! empty( $user_id ) ) {
			$this->user_id = absint( $user_id );
		} else {

			global $current_user;

			$this->user_id = ! empty( $current_user->ID ) ? $current_user->ID : false;
		}

		return $this;
	}

	/**
	 * Get user id.
	 *
	 * @return int
	 */
	public function get_user_id() {
		return $this->user_id;
	}

	/**
	 * Set usermeta key for timezone.
	 *
	 * @param string $key The key.
	 */
	public function set_usermeta_key_timezone( $key = null ) {
		if ( ! empty( $key ) && is_string( $key ) ) {
			$this->usermeta_key_timezone = $key;
		} else {
			$this->usermeta_key_timezone = $this->plugin_name . '_timezone';
		}
	}

	/**
	 * Get usermeta key for timezone.
	 *
	 * @return string
	 */
	public function get_usermeta_key_timezone() {
		return $this->usermeta_key_timezone;
	}

	/**
	 * Add timezone to the user profile.
	 *
	 * @param string $timezone The timezone.
	 * @return int|false Meta ID on success, false on failure.
	 */
	public function add_timezone( $timezone ) {
		return add_user_meta( $this->get_user_id(), $this->get_usermeta_key_timezone(), $timezone, true );
	}

	/**
	 * Get timezone of the current user.
	 *
	 * @return mixed
	 */
	public function get_user_timezone() {
		return get_user_meta( $this->get_user_id(), $this->get_usermeta_key_timezone(), true );
	}

	/**
	 * Delete old user meta data for old version.
	 */
	protected function delete_old_usermeta_key_timezone() {
		if ( empty( $this->get_user_id() ) ) {
			return;
		}
		if ( version_compare( $this->version, '1.7.0', '<=' ) ) {
			delete_user_meta( $this->get_user_id(), 'fa_userloginhostory_user_timezone' );
		}
	}

	/**
	 * Update user meta timezone.
	 */
	protected function update_usermeta_key_timezone() {

		if ( ! empty( $_POST[ $this->get_usermeta_key_timezone() ] ) ) {
			update_user_meta( $this->get_user_id(), $this->get_usermeta_key_timezone(), $_POST[ $this->get_usermeta_key_timezone() ] );
		} else {
			delete_user_meta( $this->get_user_id(), $this->get_usermeta_key_timezone() );
		}
	}

}
