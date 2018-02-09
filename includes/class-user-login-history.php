<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
class User_Login_History {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @access   protected
     * @var      User_Login_History_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
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
        /**
         * Include all the common helpers.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-error-handler.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-date-time-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-session-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-db-helper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-template-helper.php';
        /**
         * Include all the common abstract or base classes.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-abstract-list-table.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-abstract-list-page.php';

        if (is_network_admin()) {
            //required files for network admin only
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-network-admin-list-table.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-network-admin-list-page.php';
        }

        if (is_admin() && !is_network_admin()) {
            //required files for admin only
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-admin-list-table.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-admin-list-page.php';
        }

        //required files for admin as well as public
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-network-admin-setting.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-user-tracker.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-user-profile.php';

        if (!is_admin()) {
            //required files for public only
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-user-login-history-public-list-table.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-user-login-history-public.php';
        }

        $this->loader = new User_Login_History_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the User_Login_History_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
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
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new User_Login_History_Admin($this->get_plugin_name(), $this->get_version());

        if (is_admin()) {
            //hooks for admin and network admin
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

            $User_Profile = new User_Login_History_User_Profile($this->get_plugin_name(), USER_LOGIN_HISTORY_USERMETA_PREFIX);
            $this->loader->add_action('show_user_profile', $User_Profile, 'show_extra_profile_fields');
            $this->loader->add_action('edit_user_profile', $User_Profile, 'show_extra_profile_fields');
            $this->loader->add_action('user_profile_update_errors', $User_Profile, 'user_profile_update_errors', 10, 3);
            $this->loader->add_action('personal_options_update', $User_Profile, 'update_profile_fields');
            $this->loader->add_action('edit_user_profile_update', $User_Profile, 'update_profile_fields');
            $this->loader->add_action('admin_init', $plugin_admin, 'admin_init');
            $this->loader->add_action('network_admin_menu', $plugin_admin, 'update_network_setting');
        }

        if (is_network_admin()) {
            //hooks for network admin only
            $Network_Admin_List_Page = new User_Login_History_Network_Admin_List_Page($this->get_plugin_name());
            $this->loader->add_filter('set-screen-option', $Network_Admin_List_Page, 'set_screen', 10, 3);
            $this->loader->add_action('network_admin_menu', $Network_Admin_List_Page, 'plugin_menu');
            $this->loader->add_action('network_admin_notices', $plugin_admin, 'show_admin_notice');

            $Network_Admin_Setting = new User_Login_History_Network_Admin_Setting($this->plugin_name);
            $this->loader->add_action('network_admin_menu', $Network_Admin_Setting, 'add_setting_menu');
        }

        if (is_admin() && !is_network_admin()) {
            //hooks for admin only

            $this->loader->add_action('admin_notices', $plugin_admin, 'show_admin_notice');
            $Admin_List_Page = new User_Login_History_Admin_List_Page($this->get_plugin_name());
            $this->loader->add_filter('set-screen-option', $Admin_List_Page, 'set_screen', 10, 3);
            $this->loader->add_action('admin_menu', $Admin_List_Page, 'plugin_menu');
        }

//hooks for admin, network and public
        $this->loader->add_action('init', $plugin_admin, 'session_start', 0);
        $User_Tracker = new User_Login_History_User_Tracker($this->get_plugin_name());
        $this->loader->add_action('init', $User_Tracker, 'update_time_last_seen');
        $this->loader->add_action('set_logged_in_cookie', $User_Tracker, 'set_session_token', 10, 6);
        $this->loader->add_action('wp_login', $User_Tracker, 'user_login', 10, 2);
        $this->loader->add_action('wp_logout', $User_Tracker, 'user_logout');
        $this->loader->add_action('wp_login_failed', $User_Tracker, 'user_login_failed');
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
        /**
         * The shortcode "[user-login-history]" is deprecated since version 1.7.0.
         * It is here only for backward compatibility.
         */
        $this->loader->add_shortcode('user-login-history', $plugin_public, 'shortcode_user_table'); //old-shortcode
        $this->loader->add_shortcode('user_login_history', $plugin_public, 'shortcode_user_table'); //new shortcode
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        
             $User_Profile = new User_Login_History_User_Profile($this->get_plugin_name(), USER_LOGIN_HISTORY_USERMETA_PREFIX);
            $this->loader->add_action('init', $User_Profile, 'update_user_timezone');
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
     *
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     * 
     * @return    User_Login_History_Loader    Orchestrates the hooks of the plugin.
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

}
