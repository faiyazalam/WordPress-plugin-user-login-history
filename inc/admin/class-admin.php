<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History AS NS;
use User_Login_History\Inc\Admin\Listing_Table_Csv;
use User_Login_History\Inc\Admin\Admin_Login_List_Table;
use User_Login_History\Inc\Admin\Network_Admin_Login_List_Table;
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Common\Helpers\Request as RequestHelper;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv;
use User_Login_History\Inc\Common\Login_Tracker;
use User_Login_History\Inc\Admin\Settings as Admin_Settings;

/**
 * The admin-specific functionality of the plugin.
 *
 * @author    Er Faiyaz Alam
 */
class Admin {

    /**
     * The ID of this plugin.
     *
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Variables to store instance of other classes.
     */
    private $User_Profile;
    private $Listing_Table_Csv;
    private $Admin_Settings;
    private $Admin_Notice;

    /**
     * Initialize the class and set its properties.
     *
     * @param       string $plugin_name        The name of this plugin.
     * @param       string $version            The version of this plugin.
     */
    public function __construct(
    $plugin_name, $version, User_Profile $User_Profile, Listing_Table_Csv $Listing_Table_Csv, Admin_Settings $Admin_Settings, Admin_Notice $Admin_Notice
    ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->User_Profile = $User_Profile;
        $this->Listing_Table_Csv = $Listing_Table_Csv;
        $this->Admin_Notice = $Admin_Notice;
        $this->Admin_Settings = $Admin_Settings;
    }

    /**
     * Hooked with admin_init action
     */
    public function admin_init() {
        $this->csv_export_login_list();
    }

    /**
     * Exports the login list in csv format
     */
    private function csv_export_login_list() {
        if ($this->is_plugin_login_list_page() && current_user_can('administrator')) {
            $Login_List = $this->get_Login_List_Table();

            $this->Listing_Table_Csv->set_Listing_Table($Login_List);

            if (!$this->Listing_Table_Csv->is_request_for_csv()) {
                return;
            }

            $Login_List->set_timezone($this->User_Profile->get_user_timezone());
            $this->Listing_Table_Csv->init();
        }
    }

    /**
     * Checks if the current page is the login listing page or not. 
     * @return bool
     */
    private function is_plugin_login_list_page() {
        return RequestHelper::is_current_page_by_file_name() && !empty($_GET['page']) && $this->get_plugin_login_list_page_slug() == $_GET['page'];
    }

    /**
     * Gets the slug of the login listing page.
     * @return string
     */
    private function get_plugin_login_list_page_slug() {
        return $this->plugin_name . "-login-listing";
    }

    /**
     * Enqueue scripts for the login list page only.
     */
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

    /**
     * Enqueue styles for the user profile page only.
     */
    private function enqueue_styles_for_user_profile() {
        if (RequestHelper::is_current_page_by_file_name('profile') || RequestHelper::is_current_page_by_file_name('user-edit')) {
            wp_enqueue_style($this->plugin_name . '-user-profile.css', plugin_dir_url(__FILE__) . 'css/user-profile.css', array(), $this->version, 'all');
        }
    }

    /**
     * Enqueue styles for the login list page only.
     * @return null
     */
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
     */
    public function enqueue_styles() {
        $this->enqueue_styles_for_plugin_login_list_page();
        $this->enqueue_styles_for_user_profile();
    }

    /**
     * Register the JavaScript for the admin area.
     *
     */
    public function enqueue_scripts() {
        $this->enqueue_scripts_for_plugin_login_list_page();
    }

    /**
     * Hooked with admin_menu action
     */
    public function admin_menu() {

        $menu_slug = $this->get_plugin_login_list_page_slug();
        $hook = add_menu_page(
                esc_html__('Login List', 'faulh'), NS\PLUGIN_NAME, 'manage_options', $menu_slug, array($this, 'render_login_list'), plugin_dir_url(__FILE__) . 'images/icon.png', 30
        );
        add_submenu_page($menu_slug, esc_html__('Login List', 'faulh'), esc_html__('Login List', 'faulh'), 'manage_options', $menu_slug, array($this, 'render_login_list'));

        add_action("load-$hook", array($this, 'screen_option'));
    }

    /**
     * Render the login listing page
     */
    public function render_login_list() {
        require plugin_dir_path(dirname(__FILE__)) . 'admin/views/login-list-table.php';
    }

    /**
     * Hooked with set-screen-option filter
     */
    public function set_screen($status, $option, $value) {
        return $value;
    }

    /**
     * Hooked with load-$hook action
     */
    public function screen_option() {
        $option = 'per_page';
        $args = array(
            'label' => __('Show Records Per Page', 'faulh'),
            'default' => 20,
            'option' => $this->plugin_name . '_rows_per_page'
        );

        add_screen_option($option, $args);
        $this->Login_List_Table = $this->get_Login_List_Table();
        $this->Login_List_Table->process_bulk_action();
        $this->Login_List_Table->process_single_action();

        $online_duration = $this->Admin_Settings->get_online_duration();
        $this->Login_List_Table->set_online_duration($online_duration['online']);
        $this->Login_List_Table->set_idle_duration($online_duration['idle']);
        $this->Login_List_Table->set_timezone($this->User_Profile->get_user_timezone());
        $this->Login_List_Table->prepare_items();
    }

    /**
     * Get the instance of the login list table class.
     * @return Login_List_Table
     */
    private function get_Login_List_Table() {
        return is_network_admin() ? new Network_Admin_Login_List_Table($this->plugin_name, $this->version, $this->Admin_Notice) : new Admin_Login_List_Table($this->plugin_name, $this->version, $this->Admin_Notice);
    }

}
