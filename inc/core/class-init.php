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

namespace User_Login_History\Inc\Core;

use User_Login_History as NS;
use User_Login_History\Inc\Admin\Admin;
use User_Login_History\Inc\Admin\Admin_Notice;
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Frontend\User_Profile as FrontendUserProfile;
use User_Login_History\Inc\Admin\Settings as AdminSettings;
use User_Login_History\Inc\Admin\Network_Admin_Settings;
use User_Login_History\Inc\Common\Login_Tracker;
use User_Login_History\Inc\Admin\Network_Blog_Manager;
use User_Login_History\Inc\Admin\Settings_Api;
use User_Login_History\Inc\Admin\Listing_Table_Csv;
use User_Login_History\Inc\Frontend\Frontend;
use User_Login_History\Inc\Frontend\Frontend_Login_List_Table;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 */
class Init {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_base_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_basename;

	/**
	 * The current version of the plugin.
	 *
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Initialize and define the core functionality of the plugin.
	 */
	public function __construct() {

		$this->plugin_name        = NS\USER_LOGIN_HISTORY;
		$this->version            = NS\PLUGIN_VERSION;
		$this->plugin_basename    = NS\PLUGIN_BASENAME;
		$this->plugin_text_domain = NS\PLUGIN_TEXT_DOMAIN;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Loads the following required dependencies for this plugin.
	 *
	 * - Loader - Orchestrates the hooks of the plugin.
	 * - Internationalization_I18n - Defines internationalization functionality.
	 * - Admin - Defines all hooks for the admin area.
	 * - Frontend - Defines all hooks for the public side of the site.
	 */
	private function load_dependencies() {
		$this->loader = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Internationalization_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 */
	private function set_locale() {

		$plugin_i18n = new Internationalization_I18n( $this->plugin_text_domain );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks() {

		$admin_notice           = new Admin_Notice( $this->get_plugin_name() );
		$user_profile           = new User_Profile( $this->get_plugin_name(), $this->get_version() );
		$admin_setting          = new AdminSettings( $this->get_plugin_name(), $this->get_version(), new Settings_Api() );
		$admin                  = new Admin( $this->get_plugin_name(), $this->get_version(), $user_profile, new Listing_Table_Csv(), $admin_setting, $admin_notice );
		$network_admin_settings = new Network_Admin_Settings( $this->get_plugin_name(), $this->get_version(), $admin_notice );

		if ( is_network_admin() ) {
			$network_blog_manager = new Network_Blog_Manager();
			$this->loader->add_action( 'wpmu_new_blog', $network_blog_manager, 'on_create_blog', 10, 6 );
			$this->loader->add_action( 'deleted_blog', $network_blog_manager, 'deleted_blog', 10, 1 );
		}

		$login_tracker = new Login_Tracker( $this->get_plugin_name(), $this->get_version(), NS\PLUGIN_TABLE_FA_USER_LOGINS );
		$login_tracker->set_is_geo_tracker_enabled( $admin_setting->is_geo_tracker_enabled() );
		$login_tracker->set_is_cross_blog_login_blocked( $network_admin_settings->get_block_user() );
		$login_tracker->set_message_for_cross_blog_login( $network_admin_settings->get_block_user_message() );

		$this->loader->add_action( 'admin_init', $admin, 'admin_init' );
		$this->loader->add_action( 'admin_init', $admin, 'check_update_version' );
		$this->loader->add_action( 'plugin_action_links_' . (NS\PLUGIN_BASENAME), $admin, 'add_action_links' );

		if ( is_network_admin() ) {
			$this->loader->add_action( 'admin_init', $network_admin_settings, 'update' );
		}

		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $admin, 'admin_menu' );
		$this->loader->add_action( 'network_admin_menu', $admin, 'admin_menu' );
		$this->loader->add_filter( 'set-screen-option', $admin, 'set_screen', 10, 3 );

		$this->loader->add_action( 'admin_notices', $admin_notice, 'show_notice' );
		$this->loader->add_action( 'network_admin_notices', $admin_notice, 'show_notice' );

		$this->loader->add_action( 'set_logged_in_cookie', $login_tracker, 'set_logged_in_cookie', 10, 6 );
		$this->loader->add_action( 'wp_login_failed', $login_tracker, 'on_login_failed' );
		$this->loader->add_action( 'wp_logout', $login_tracker, 'on_logout' );
		$this->loader->add_action( 'init', $login_tracker, 'init' );
		$this->loader->add_action( 'attach_session_information', $login_tracker, 'attach_session_information', 10, 2 );

		$this->loader->add_action( 'admin_init', $admin_setting, 'admin_init' );
		$this->loader->add_action( 'admin_menu', $admin_setting, 'admin_menu' );

		$this->loader->add_action( 'init', $user_profile, 'init' );
		$this->loader->add_action( 'show_user_profile', $user_profile, 'show_extra_profile_fields' );
		$this->loader->add_action( 'edit_user_profile', $user_profile, 'show_extra_profile_fields' );
		$this->loader->add_action( 'personal_options_update', $user_profile, 'update_profile_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $user_profile, 'update_profile_fields' );

		$this->loader->add_action( 'network_admin_menu', $network_admin_settings, 'admin_menu' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {
		if ( is_admin() ) {
			return;
		}
		$list_table    = new Frontend_Login_List_Table( $this->get_plugin_name(), $this->get_version() );
		$user_profile  = new FrontendUserProfile( $this->get_plugin_name(), $this->get_version() );
		$plugin_public = new Frontend( $this->get_plugin_name(), $this->get_version(), $list_table, $user_profile );
		$this->loader->add_shortcode( 'user_login_history', $plugin_public, 'shortcode_user_table' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $user_profile, 'init' );
		$this->loader->add_action( 'init', $user_profile, 'update_user_timezone' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the text domain of the plugin.
	 *
	 * @return    string    The text domain of the plugin.
	 */
	public function get_plugin_text_domain() {
		return $this->plugin_text_domain;
	}

}
