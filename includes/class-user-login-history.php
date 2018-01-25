<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.0.0
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam <support@userloginhistory.com>
 */
class User_Login_History {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      User_Login_History_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if (defined('USER_LOGIN_HISTORY_VERSION')) {
            $this->version = USER_LOGIN_HISTORY_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = USER_LOGIN_HISTORY_NAME;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();

        if (!is_admin()) {
            $this->define_public_hooks();
        }
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - User_Login_History_Loader. Orchestrates the hooks of the plugin.
     * - User_Login_History_i18n. Defines internationalization functionality.
     * - User_Login_History_Admin. Defines all hooks for the admin area.
     * - User_Login_History_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-error-handler.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-date-time-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-session-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-db-helper.php';
       
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

       

        if (is_admin()) {
            //required files for admin only
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-abstract-list-table.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-admin-list-table.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-singleton-admin-list-table.php';
       
            require plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-settings-api.php';
    require plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-admin-setting-helper.php';
            
        }
        else{
            //required files for public only
        }

        //required files for admin as well as public
         require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-user-login-history-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-user-tracker.php';

        $this->loader = new User_Login_History_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the User_Login_History_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new User_Login_History_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new User_Login_History_Admin($this->get_plugin_name(), $this->get_version());


        if (is_admin()) {
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

            $this->loader->add_action('plugins_loaded', $plugin_admin, 'plugins_loaded');
            $this->loader->add_action('admin_init', $plugin_admin, 'process_bulk_action');
                    $this->loader->add_action('admin_notices', $plugin_admin, 'show_admin_notice');
                   // $this->loader->add_action('plugins_loaded', $plugin_admin, 'create_admin_settings');
        }

//hooks for admin as well as public
        $this->loader->add_action('wp_login', $plugin_admin, 'user_login', 10, 2);
        $this->loader->add_action('wp_logout', $plugin_admin, 'user_logout');
        $this->loader->add_action('wp_login_failed', $plugin_admin, 'user_login_failed');
        $this->loader->add_action('init', $plugin_admin, 'update_user_time_last_seen');
        $this->loader->add_action('init', $plugin_admin, 'session_start', 0);

        $this->loader->add_action('set_logged_in_cookie', $plugin_admin, 'set_user_session_token', 10, 6);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new User_Login_History_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    User_Login_History_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
