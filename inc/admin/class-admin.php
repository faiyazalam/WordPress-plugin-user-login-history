<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History\Inc\Admin\LoginListTable;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 * @since      1.0.0
 *
 * @author    Er Faiyaz Alam
 */
class Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The text domain of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_text_domain    The text domain of this plugin.
     */
    private $plugin_text_domain;
    private $admin_notice_transient;

    /**
     * Initialize the class and set its properties.
     *
     * @since       1.0.0
     * @param       string $plugin_name        The name of this plugin.
     * @param       string $version            The version of this plugin.
     * @param       string $plugin_text_domain The text domain of this plugin.
     */
    public function __construct($plugin_name, $version, $plugin_text_domain) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;
        $this->admin_notice_transient = $this->plugin_name . '_admin_notice_transient';
    }

    private function is_current_page_by_file_name($file = 'admin') {
        global $pagenow;
        return $file . '.php' == $pagenow;
    }

    private function is_plugin_login_list_page() {
        if (!$this->is_current_page_by_file_name()) {
            return FALSE;
        }

        if (!empty($_GET['page']) && $this->get_plugin_login_list_page_slug() == $_GET['page']) {
            return TRUE;
        }

        return FALSE;
    }

    private function get_plugin_login_list_page_slug() {
        return $this->plugin_name . "-login-listing";
    }

    private function enqueue_scripts_for_plugin_login_list_page() {
        if (!$this->is_plugin_login_list_page()) {
            return FALSE;
        }

        wp_enqueue_script($this->plugin_name . '-admin-jquery-ui.min.js', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array(), $this->version, 'all');
        wp_enqueue_script($this->plugin_name . '-admin-custom.js', plugin_dir_url(__FILE__) . 'js/custom.js', array(), $this->version, 'all');
        wp_localize_script($this->plugin_name . '-admin-custom.js', 'admin_custom_object', array(
            'delete_confirm_message' => esc_html__('Are your sure?', 'faulh'),
            'invalid_date_range_message' => esc_html__('Please provide a valid date range.', 'faulh'),
            'admin_url' => admin_url(),
            'plugin_name' => $this->plugin_name,
            'show_advanced_filters' => esc_html__('Show Advanced Filters', 'faulh'),
            'hide_advanced_filters' => esc_html__('Hide Advanced Filters', 'faulh'),
        ));
    }

    private function enqueue_styles_for_user_profile() {
        if ($this->is_current_page_by_file_name('profile') || $this->is_current_page_by_file_name('user-edit')) {
            wp_enqueue_style($this->plugin_name . '-user-profile.css', plugin_dir_url(__FILE__) . 'css/user-profile.css', array(), $this->version, 'all');
        }
    }

    private function enqueue_styles_for_plugin_login_list_page() {
        if (!$this->is_plugin_login_list_page()) {
            return FALSE;
        }
        wp_enqueue_style($this->plugin_name . '-admin-jquery-ui.min.css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-admin.css', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        $this->enqueue_styles_for_plugin_login_list_page();
        $this->enqueue_styles_for_user_profile();
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        $this->enqueue_scripts_for_plugin_login_list_page();
    }

    public function admin_menu() {
        $menu_slug = $this->get_plugin_login_list_page_slug();
        $hook = add_menu_page(
                esc_html__('Login List', 'faulh'), 'User Login History', 'manage_options', $menu_slug, array($this, 'render_login_list'), plugin_dir_url(__FILE__) . 'images/icon.png', 30
        );
        add_submenu_page($menu_slug, esc_html__('Login List', $this->plugin_text_domain), esc_html__('Login List', 'faulh'), 'manage_options', $menu_slug, array($this, 'render_login_list'));
        add_action("load-$hook", array($this, 'screen_option'));
    }

    public function render_login_list() {
        require plugin_dir_path(dirname(__FILE__)) . 'admin/views/login-list-table.php';
    }

    /**
     * Callback function for the filter - set-screen-option
     */
    public function set_screen($status, $option, $value) {
        return $value;
    }

    /**
     * Callback function for the action - load-$hook
     */
    public function screen_option() {
        $option = 'per_page';
        $args = array(
            'label' => __('Show Records Per Page', $this->plugin_text_domain),
            'default' => 20,
            'option' => $this->plugin_name . '_rows_per_page'
        );

        add_screen_option($option, $args);

        $this->list_table = new LoginListTable($this->plugin_name, $this->version, $this->plugin_text_domain);
        $status = $this->list_table->process_action();
        if (!is_null($status)) {
            $this->add_admin_notice($this->list_table->get_message(), $status ? 'success' : 'error');
            wp_safe_redirect(esc_url("admin.php?page=" . $_GET['page']));
            exit;
        }

        $this->list_table->prepare_items();
    }

    /**
     * Add admin notices
     * @access public
     */
    public function add_admin_notice($message, $type = 'success') {
        $notices = get_transient($this->admin_notice_transient);
        if ($notices === false) {
            $new_notices[] = array($message, $type);
            set_transient($this->admin_notice_transient, $new_notices, 120);
        } else {
            $notices[] = array($message, $type);
            set_transient($this->admin_notice_transient, $notices, 120);
        }
    }

    /**
     * Show admin notices
     * 
     * @access public
     */
    public function show_admin_notice() {
        $notices = get_transient($this->admin_notice_transient);

        if ($notices !== false) {
            foreach ($notices as $notice) {
                echo '<div class="notice notice-' . $notice[1] . ' is-dismissible"><p>' . $notice[0] . '</p></div>';
            }
            delete_transient($this->admin_notice_transient);
        }
    }

}
