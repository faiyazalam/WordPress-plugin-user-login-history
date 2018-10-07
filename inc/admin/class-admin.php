<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History\Inc\Admin\Login_List_Csv;
use User_Login_History\Inc\Admin\Login_List_Table;
use User_Login_History\Inc\Admin\Network_Login_List_Table;
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Common\Helpers\Request as RequestHelper;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv;

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

    const INTERVAL_FOR_TRANSIENT = 120;

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
    private $User_Profile;
    private $Login_List_Csv;

    /**
     * Initialize the class and set its properties.
     *
     * @since       1.0.0
     * @param       string $plugin_name        The name of this plugin.
     * @param       string $version            The version of this plugin.
     * @param       string $plugin_text_domain The text domain of this plugin.
     */
    public function __construct($plugin_name, $version, $plugin_text_domain, User_Profile $User_Profile, Login_List_Csv $Login_List_Csv) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;
        $this->admin_notice_transient = $this->plugin_name . '_admin_notice_transient';
        $this->User_Profile = $User_Profile;
        $this->Login_List_Csv = $Login_List_Csv;
    }

    public function admin_init() {
        $this->init_csv_export();
        $this->update_network_settings();
    }

    private function init_csv_export() {
        if ($this->is_plugin_login_list_page() && current_user_can('administrator')) {
            $Login_List = is_network_admin() ? new Network_Login_List_Table($this->plugin_name, $this->version, $this->plugin_text_domain) : new Login_List_Table($this->plugin_name, $this->version, $this->plugin_text_domain);
            $this->Login_List_Csv->set_login_list_object($Login_List);
            
            if ($this->Login_List_Csv->is_request_for_csv()) {
                $this->User_Profile->set_user_id();
                $Login_List->set_timezone($this->User_Profile->get_user_timezone());
                $this->Login_List_Csv->init();
            }
        }
    }

    private function is_plugin_login_list_page() {
        if (!RequestHelper::is_current_page_by_file_name()) {
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

        wp_enqueue_script($this->plugin_name . '-admin-jquery-ui.min', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array(), $this->version, 'all');
        wp_enqueue_script($this->plugin_name . '-admin-custom', plugin_dir_url(__FILE__) . 'js/custom.js', array(), $this->version, 'all');
        wp_localize_script($this->plugin_name . '-admin-custom', 'admin_custom_object', array(
            'delete_confirm_message' => esc_html__('Are your sure?', 'faulh'),
            'invalid_date_range_message' => esc_html__('Please provide a valid date range.', 'faulh'),
            'admin_url' => admin_url(),
            'plugin_name' => $this->plugin_name,
            'show_advanced_filters' => esc_html__('Show Advanced Filters', 'faulh'),
            'hide_advanced_filters' => esc_html__('Hide Advanced Filters', 'faulh'),
        ));
    }

    private function enqueue_styles_for_user_profile() {
        if (RequestHelper::is_current_page_by_file_name('profile') || RequestHelper::is_current_page_by_file_name('user-edit')) {
            wp_enqueue_style($this->plugin_name . '-user-profile.css', plugin_dir_url(__FILE__) . 'css/user-profile.css', array(), $this->version, 'all');
        }
    }

    private function enqueue_styles_for_plugin_login_list_page() {
        if (!$this->is_plugin_login_list_page()) {
            return FALSE;
        }
        wp_enqueue_style($this->plugin_name . '-admin-jquery-ui.min', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
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

        $this->list_table = is_network_admin() ? new Network_Login_List_Table($this->plugin_name, $this->version, $this->plugin_text_domain) : new Login_List_Table($this->plugin_name, $this->version, $this->plugin_text_domain);
        $this->list_table->set_timezone($this->User_Profile->get_user_timezone());
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
            set_transient($this->admin_notice_transient, $new_notices, self::INTERVAL_FOR_TRANSIENT);
        } else {
            $notices[] = array($message, $type);
            set_transient($this->admin_notice_transient, $notices, self::INTERVAL_FOR_TRANSIENT);
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

    /**
     * Update the network settings.
     * 
     * @access public
     */
    private function update_network_settings() {
        if (!is_network_admin()) {
            return;
        }
        $obj = new Network_Admin_Settings($this->plugin_name);
        if ($obj->update()) {
            $this->add_admin_notice(esc_html__('Settings updated successfully.', 'faulh'));
            wp_safe_redirect(esc_url(network_admin_url("settings.php?page=" . $_GET['page'])));
            exit;
        }
    }

}
