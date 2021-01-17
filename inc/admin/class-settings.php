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

use User_Login_History\Inc\Admin\Settings_Api;
use User_Login_History as NS;

/**
 * Admin specific settings.
 */
class Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Holds the instance of Settings_Api.
	 *
	 * @var Settings_Api
	 */
	private $settings_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string       $plugin_name   The name of this plugin.
	 * @param string       $version       The version of this plugin.
	 * @param Settings_Api $settings_api  The settings object.
	 */
	public function __construct( $plugin_name, $version, Settings_Api $settings_api ) {
		$this->version      = $version;
		$this->plugin_name  = $plugin_name;
		$this->settings_api = $settings_api;
	}

	/**
	 * Hooked with admin_init action.
	 */
	public function admin_init() {
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );
		$this->settings_api->admin_init();
	}

	/**
	 * Hooked with admin_menu action.
	 */
	public function admin_menu() {
		add_options_page( NS\PLUGIN_NAME, NS\PLUGIN_NAME, 'administrator', $this->plugin_name . '-settings', array( $this, 'plugin_page' ) );
	}

	/**
	 * Get the setting sections.
	 *
	 * @return array
	 */
	public function get_settings_sections() {
		$sections = array(
			array(
				'id'    => $this->plugin_name . '_basics',
				'title' => esc_html__( 'Basic Settings', 'faulh' ),
			),
			array(
				'id'    => $this->plugin_name . '_advanced',
				'title' => esc_html__( 'Advanced Settings', 'faulh' ),
			),
		);
		return $sections;
	}

	/**
	 * Returns all the settings fields.
	 *
	 * @return array
	 */
	public function get_settings_fields() {
		$settings_fields = array(
			$this->plugin_name . '_basics'   => array(
				array(
					'name'              => 'is_status_online',
					'label'             => esc_html__( 'Online', 'faulh' ),
					'desc'              => esc_html__( 'Maximum number of minutes for online users. Default is', 'faulh' ) . ' ' . NS\DEFAULT_IS_STATUS_ONLINE_MIN,
					'min'               => 1,
					'step'              => '1',
					'type'              => 'number',
					'default'           => NS\DEFAULT_IS_STATUS_ONLINE_MIN,
					'sanitize_callback' => 'absint',
				),
				array(
					'name'              => 'is_status_idle',
					'label'             => esc_html__( 'Idle', 'faulh' ),
					'desc'              => esc_html__( 'Maximum number of minutes for idle users. This should be greater than that of online users. Default is', 'faulh' ) . ' ' . NS\DEFAULT_IS_STATUS_IDLE_MIN,
					'min'               => 1,
					'step'              => '1',
					'type'              => 'number',
					'default'           => NS\DEFAULT_IS_STATUS_IDLE_MIN,
					'sanitize_callback' => 'absint',
				),
			),
			$this->plugin_name . '_advanced' => array(
				array(
					'name'    => 'is_geo_tracker_enabled',
					'label'   => esc_html__( 'Geo Tracker', 'faulh' ) . '<br>' . esc_html__( '(Not Recommended)', 'faulh' ),
					'desc'    => esc_html__( 'Enable tracking of country and timezone.', 'faulh' ) . '<br>' . esc_html__( 'This functionality is dependent on a free third-party API service, hence not recommended.', 'faulh' ),
					'type'    => 'checkbox',
					'default' => false,
				),
			),
		);

		return $settings_fields;
	}

	/**
	 * Get all the basics settings.
	 *
	 * @return mixed
	 */
	private function get_basic_settings() {
		return get_option( $this->plugin_name . '_basics' );
	}

	/**
	 * Get all the advanced settings.
	 *
	 * @return mixed
	 */
	private function get_advanced_settings() {
		return get_option( $this->plugin_name . '_advanced' );
	}

	/**
	 * Get duration for online and idle statuses.
	 *
	 * @return array
	 */
	public function get_online_duration() {
		$settings      = $this->get_basic_settings();
		$minute_online = ! empty( $settings['is_status_online'] ) ? absint( $settings['is_status_online'] ) : NS\DEFAULT_IS_STATUS_ONLINE_MIN;
		$minute_idle   = ! empty( $settings['is_status_idle'] ) ? absint( $settings['is_status_idle'] ) : NS\DEFAULT_IS_STATUS_IDLE_MIN;
		return array(
			'online' => $minute_online,
			'idle'   => $minute_idle,
		);
	}

	/**
	 * Check if the geo tracker setting is enabled.
	 *
	 * @return bool
	 */
	public function is_geo_tracker_enabled() {
		$options = $this->get_advanced_settings();
		return ( ! empty( $options['is_geo_tracker_enabled'] ) && 'on' == $options['is_geo_tracker_enabled'] );
	}

	/**
	 * Render the admin settings page.
	 */
	public function plugin_page() {
		echo '<div class="wrap">';
		\User_Login_History\Inc\Common\Helpers\Template::head( esc_html__( 'Settings', 'faulh' ) );
		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();
		echo '</div>';
	}

}
