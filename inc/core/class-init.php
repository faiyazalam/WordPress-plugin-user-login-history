<?php

namespace User_Login_History\Inc\Core;

use User_Login_History as NS;
use User_Login_History\Inc\Admin\Admin;
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Admin\Settings as AdminSettings;
use User_Login_History\Inc\Admin\Network_Admin_Settings;
use User_Login_History\Inc\Admin\LoginListCsv;
use User_Login_History\Inc\Admin\LoginListTable;
use User_Login_History\Inc\Common\LoginTracker;
use User_Login_History\Inc\Frontend as Frontend;
use User_Login_History\Inc\Common\Settings;
use User_Login_History\Inc\Admin\NetworkBlogManager;

/**
 * The core plugin class.
 * Defines internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * @link       http://userloginhistory.com
 * @since      1.0.0
 *
 * @author     Er Faiyaz Alam
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
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_base_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_basename;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The text domain of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $plugin_text_domain;

    /**
     * Initialize and define the core functionality of the plugin.
     */
    public function __construct() {

        $this->plugin_name = NS\USER_LOGIN_HISTORY;
        $this->version = NS\PLUGIN_VERSION;
        $this->plugin_basename = NS\PLUGIN_BASENAME;
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
     *
     * @access    private
     */
    private function load_dependencies() {
        $this->loader = new Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Internationalization_I18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @access    private
     */
    private function set_locale() {

        $plugin_i18n = new Internationalization_I18n($this->plugin_text_domain);

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @access    private
     */
    private function define_admin_hooks() {
        if (is_multisite() && is_network_admin()) {
            $NetworkBlogManager = new NetworkBlogManager();
            $this->loader->add_action('wpmu_new_blog', $NetworkBlogManager, 'on_create_blog', 10, 6);
            $this->loader->add_action('deleted_blog', $NetworkBlogManager, 'deleted_blog', 10, 1);
        }

        $User_Profile = new User_Profile($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());
        $plugin_admin = new Admin($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain(), $User_Profile);


        $this->loader->add_action('admin_init', $plugin_admin, 'admin_init');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu');
        $this->loader->add_action('network_admin_menu', $plugin_admin, 'admin_menu');
        $this->loader->add_filter('set-screen-option', $plugin_admin, 'set_screen', 10, 3);
        
        $this->loader->add_action('admin_notices', $plugin_admin, 'show_admin_notice');
        $this->loader->add_action('network_admin_notices', $plugin_admin, 'show_admin_notice');

        $LoginTracker = new LoginTracker($this->get_plugin_name(), $this->get_version(), NS\PLUGIN_TABLE_FA_USER_LOGINS);
        $this->loader->add_action('set_logged_in_cookie', $LoginTracker, 'set_logged_in_cookie', 10, 6);
        $this->loader->add_action('wp_login_failed', $LoginTracker, 'on_login_failed');
        $this->loader->add_action('wp_logout', $LoginTracker, 'on_logout');
        $this->loader->add_action('init', $LoginTracker, 'init');
        $this->loader->add_action('attach_session_information', $LoginTracker, 'attach_session_information', 10, 2);


        $Admin_Setting = new AdminSettings($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain(), new Settings());
        $this->loader->add_action('admin_init', $Admin_Setting, 'admin_init');
        $this->loader->add_action('admin_menu', $Admin_Setting, 'admin_menu');



        $this->loader->add_action('admin_init', $User_Profile, 'admin_init');
        $this->loader->add_action('show_user_profile', $User_Profile, 'show_extra_profile_fields');
        $this->loader->add_action('edit_user_profile', $User_Profile, 'show_extra_profile_fields');
        $this->loader->add_action('user_profile_update_errors', $User_Profile, 'user_profile_update_errors', 10, 3);
        $this->loader->add_action('personal_options_update', $User_Profile, 'update_profile_fields');
        $this->loader->add_action('edit_user_profile_update', $User_Profile, 'update_profile_fields');
        
          $Network_Admin_Setting = new Network_Admin_Settings($this->plugin_name);
                $this->loader->add_action('network_admin_menu', $Network_Admin_Setting, 'add_setting_menu');


        /*
         * Additional Hooks go here
         *
         * e.g.
         *
         * //admin menu pages
         * $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
         *
         *  //plugin action links
         * $this->loader->add_filter( 'plugin_action_links_' . $this->plugin_basename, $plugin_admin, 'add_additional_action_link' );
         *
         */
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @access    private
     */
    private function define_public_hooks() {

        $plugin_public = new Frontend\Frontend($this->get_plugin_name(), $this->get_version(), $this->get_plugin_text_domain());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
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
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Retrieve the text domain of the plugin.
     *
     * @since     1.0.0
     * @return    string    The text domain of the plugin.
     */
    public function get_plugin_text_domain() {
        return $this->plugin_text_domain;
    }

}
