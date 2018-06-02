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
if (!class_exists('Faulh')) {

    class Faulh {

        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @access   protected
         * @var      Faulh_Loader    $loader    Maintains and registers all hooks for the plugin.
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
            $this->version = defined('FAULH_VERSION') ? FAULH_VERSION : '1.0.0';
            $this->plugin_name = defined('FAULH_PLUGIN_NAME') ? FAULH_PLUGIN_NAME : 'faulh';

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
         * - Faulh_Loader. Orchestrates the hooks of the plugin.
         * - Faulh_i18n. Defines internationalization functionality.
         * - Faulh_Admin. Defines all hooks for the admin area.
         * - Faulh_Public. Defines all hooks for the public side of the site.
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
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-loader.php';

            /**
             * The class responsible for defining internationalization functionality
             * of the plugin.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-i18n.php';
            /**
             * Include all the common helpers.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-error-handler.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-date-time-helper.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-session-helper.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-db-helper.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-template-helper.php';
            /**
             * Include all the common abstract or base classes.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-abstract-list-table.php';

            if (is_network_admin()) {
                //required files for network admin only
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-faulh-network-admin-list-table.php';
            }

            if (is_admin() && !is_network_admin()) {
                //required files for admin only
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-faulh-admin-setting.php';
                require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-faulh-admin-list-table.php';
            }

            //required files for admin as well as public
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-faulh-network-admin-setting.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-faulh-admin.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-user-tracker.php';
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-faulh-user-profile.php';

            if (!is_admin()) {
                //required files for public only
                require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-faulh-public-list-table.php';
                require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-faulh-public.php';
            }

            $this->loader = new Faulh_Loader();
        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the Faulh_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @access   private
         */
        private function set_locale() {

            $plugin_i18n = new Faulh_i18n();

            $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
        }

        /**
         * Register all of the hooks related to the admin area functionality
         * of the plugin.
         *
         * @access   private
         */
        private function define_admin_hooks() {
            $plugin_admin = new Faulh_Admin($this->get_plugin_name(), $this->get_version());

            if (is_admin()) {
                //hooks for admin and network admin
                $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
                $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

                $User_Profile = new Faulh_User_Profile($this->get_plugin_name(), $this->get_version());
                $this->loader->add_action('show_user_profile', $User_Profile, 'show_extra_profile_fields');
                $this->loader->add_action('edit_user_profile', $User_Profile, 'show_extra_profile_fields');
                $this->loader->add_action('user_profile_update_errors', $User_Profile, 'user_profile_update_errors', 10, 3);
                $this->loader->add_action('personal_options_update', $User_Profile, 'update_profile_fields');
                $this->loader->add_action('edit_user_profile_update', $User_Profile, 'update_profile_fields');
                $this->loader->add_action('admin_init', $plugin_admin, 'admin_init');
                $this->loader->add_action('network_admin_menu', $plugin_admin, 'update_network_setting');
                $this->loader->add_action('admin_menu', $plugin_admin, 'plugin_menu');
                $this->loader->add_filter('set-screen-option', $plugin_admin, 'set_screen', 10, 3);
            }

            if (is_network_admin()) {
                //hooks for network admin only
                $this->loader->add_action('network_admin_menu', $plugin_admin, 'plugin_menu');
                $this->loader->add_action('network_admin_notices', $plugin_admin, 'show_admin_notice');
                $Network_Admin_Setting = new Faulh_Network_Admin_Setting($this->plugin_name);
                $this->loader->add_action('network_admin_menu', $Network_Admin_Setting, 'add_setting_menu');
            }

            if (is_admin() && !is_network_admin()) {
                //hooks for admin only
                $this->loader->add_action('admin_notices', $plugin_admin, 'show_admin_notice');
                $Admin_Setting = new Faulh_Admin_Setting($this->get_plugin_name(), $this->get_version());
                $this->loader->add_action('admin_init', $Admin_Setting, 'admin_init');
                $this->loader->add_action('admin_menu', $Admin_Setting, 'admin_menu');
            }
//hooks for admin, network and public
            $Session_Helper = new Faulh_Session_Helper($this->get_plugin_name());

            $User_Tracker = new Faulh_User_Tracker($this->get_plugin_name(), $this->get_version());
            $this->loader->add_action('init', $plugin_admin, 'check_update_version');
            $this->loader->add_action('init', $User_Tracker, 'set_current_loggedin_blog_id');
            $this->loader->add_action('init', $User_Tracker, 'update_time_last_seen');
            $this->loader->add_action('set_logged_in_cookie', $User_Tracker, 'set_logged_in_cookie', 10, 6);
            $this->loader->add_action('wp_login', $User_Tracker, 'user_login', 0, 2);
            $this->loader->add_action('wp_logout', $User_Tracker, 'user_logout');
            $this->loader->add_action('wp_login_failed', $User_Tracker, 'user_login_failed');
            $this->loader->add_filter('attach_session_information', $Session_Helper, 'attach_session_information', 10, 2);
        }

        /**
         * Register all of the hooks related to the public-facing functionality
         * of the plugin.
         *
         * @since    1.0.0
         * @access   private
         */
        private function define_public_hooks() {
            $plugin_public = new Faulh_Public($this->get_plugin_name(), $this->get_version());
            /**
             * The shortcode "[user-login-history]" is deprecated since version 1.7.0.
             * It is here only for backward compatibility.
             */
            $this->loader->add_shortcode('user-login-history', $plugin_public, 'shortcode_user_table'); //old-shortcode
            $this->loader->add_shortcode('user_login_history', $plugin_public, 'shortcode_user_table'); //new shortcode
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

            $User_Profile = new Faulh_User_Profile($this->get_plugin_name(), $this->get_version());
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
         * @return    Faulh_Loader    Orchestrates the hooks of the plugin.
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

}

