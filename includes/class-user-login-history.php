<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://
 * @since      1.0.0
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 */
class User_Login_History {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.4.1
     * @access   protected
     * @var      User_Login_History_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.4.1
     * @access   protected
     * @var      string    $User_Login_History    The string used to uniquely identify this plugin.
     */
    protected $User_Login_History;

    /**
     * The current version of the plugin.
     *
     * @since   1.4.1
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The option prefix of this plugin.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $version   To be used with options like user meta key, key for option table etc. for the plugin.
     */
    private $option_prefix;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    1.4.1
     */
    public function __construct() {

        $this->User_Login_History = ULH_PLUGIN_NAME;
        $this->option_prefix = ULH_PLUGIN_OPTION_PREFIX;
        $this->version = ULH_PLUGIN_VERSION;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - User_Login_History_Loader. Orchestrates the hooks of the plugin.
     * - User_Login_History_i18n. Defines internationalization functionality.
     * - User_Login_History_Admin. Defines all hooks for the dashboard.
     * - User_Login_History_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.4.1
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-paginator-helper.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-user-login-history-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-user-login-history-public-list-table-helper.php';
        /**
         * The class responsible for tracking user login.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-user-tracker.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-date-time-helper.php';

        $this->loader = new User_Login_History_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the User_Login_History_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.4.1
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new User_Login_History_i18n();
        $plugin_i18n->set_domain($this->get_User_Login_History());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    1.4.1
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new User_Login_History_Admin($this->get_User_Login_History(), $this->get_version(), $this->get_option_prefix());

        $user_tracker = new User_Login_History_User_Tracker($this->get_User_Login_History(), $this->get_version(), $this->get_option_prefix());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'settings_api_init');
        $this->loader->add_action('admin_init', $plugin_admin, 'check_update_version');
        $this->loader->add_action('init', $plugin_admin, 'do_ob_start');
        $this->loader->add_action('admin_init', $plugin_admin, 'init_csv_export');
        $this->loader->add_action('admin_notices', $plugin_admin, 'show_admin_notice');
        $this->loader->add_action('admin_init', $plugin_admin, 'delete_all_records');
        $this->loader->add_action('admin_init', $plugin_admin, 'update_user_timezone');

        $this->loader->add_action('wp_login', $user_tracker, 'save_user_login', 10, 2);
        $this->loader->add_action('wp_logout', $user_tracker, 'save_user_logout');
        $this->loader->add_action('init', $user_tracker, 'do_session_start');
        $this->loader->add_action('after_setup_theme', $user_tracker, 'update_time_last_seen'); // auto update last seen time, sometime it does not work
        $this->loader->add_action('init', $user_tracker, 'update_time_last_seen'); // update last seen time only after redirection
        $this->loader->add_action('plugins_loaded', $user_tracker, 'create_table'); // Create listing table on admin panel.
    }

    /**
     * Register all of the hooks related to the frontend functionality
     * of the plugin.
     *
     * @since    1.4.1
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_user = new User_Login_History_Public($this->get_User_Login_History(), $this->get_version());

        $this->loader->add_shortcode('user-login-history', $plugin_user, 'shortcode_user_table');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_user, 'enqueue_scripts');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_user, 'enqueue_styles');
        $this->loader->add_action('wp_ajax_ulh_public_select_timezone', $plugin_user, 'ulh_public_select_timezone');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.4.1
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.4.1
     * @return    string    The name of the plugin.
     */
    public function get_User_Login_History() {
        return $this->User_Login_History;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since    1.4.1
     * @return    User_Login_History_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.4.1
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Retrieve the option prefix of the plugin.
     *
     * @since     1.4.1
     * @return    string    The option prefix of the plugin.
     */
    public function get_option_prefix() {
        return $this->option_prefix;
    }

}
