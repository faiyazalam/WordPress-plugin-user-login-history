<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
class User_Login_History_Admin_Setting_Helper {

    private $settings_api;
    private $plugin_name;
        static private $instance;
    
        /** Singleton instance */
    public static function get_instance($plugin_name) {
        if (!isset(self::$instance)) {
            self::$instance = new self($plugin_name);
        }

        return self::$instance;
    }

    function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        $this->settings_api = new User_Login_History_Settings_API();

        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page('User Login History', 'User Login History', 'manage_options', $this->plugin_name . '-admin-settings', array($this, 'plugin_page'));
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => $this->plugin_name . '-basics',
                'title' => __('Basic Settings', 'wedevs')
            ),
            array(
                'id' => $this->plugin_name . '-advanced',
                'title' => __('Advanced Settings', 'wedevs')
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            $this->plugin_name . '-basics' => array(
                array(
                    'name' => 'frontend_limit',
                    'label' => __('Show Records Per Page', 'wedevs'),
                    'desc' => __('Number field with validation callback `floatval`', 'wedevs'),
                    'placeholder' => __('10', 'wedevs'),
                    'min' => 0,
                    'max' => 100,
                    'step' => '1',
                    'type' => 'number',
                    'default' => 'Title',
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name' => 'frontend_show_all_records',
                    'label' => 'Show All Records',
                    'desc' => __('Show records of all the users on the frontend listing table. By default it shows records of current user.', 'wedevs'),
                    'type' => 'checkbox',
                    
                ),
                array(
                    'name' => 'frontend_columns',
                    'label' => __('Display Columns', 'wedevs'),
                    'desc' => __('Choose the columns to be displayed on frontend listing table.', 'wedevs'),
                    'type' => 'multicheck',
                    'default' => $this->get_all_default_frontend_columns(),
                    'options' => $this->get_all_frontend_columns()
                ),
            ),
            $this->plugin_name . '-advanced' => array(
                array(
                    'name' => 'login_status_login_color',
                    'label' => __('Status Color', 'wedevs'),
                    'desc' => __('Select color for login.', 'wedevs'),
                    'type' => 'color',
                    'default' => '#b5d6a4'
                ),
                array(
                    'name' => 'login_status_logout_color',
                    'desc' => __('Select color for logout.', 'wedevs'),
                    'type' => 'color',
                    'default' => '#d3bee2'
                ),
                array(
                    'name' => 'login_status_fail_color',
                    'desc' => __('Select color for fail.', 'wedevs'),
                    'type' => 'color',
                    'default' => '#dd9d9d'
                ),
            ),
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }
    
    private function get_all_frontend_columns() {
        $columns = array(
                       'user_id'    => __( 'User ID', 'user-login-history' ),
			'username'    => __( 'Username', 'user-login-history' ),
                        'role' => __( 'Current Role', 'user-login-history' ),
                        'old_role' => __( 'Old Role', 'user-login-history' ),
			'ip_address'    => __( 'IP Address', 'user-login-history' ),
			'browser'    => __( 'Browser', 'user-login-history' ),
			'operating_system' => __( 'Platform', 'user-login-history' ),
			'country_name'    => __( 'Country Name', 'user-login-history' ),
			'timezone'    => __( 'Timezone', 'user-login-history' ),
			'user_agent'    => __( 'User Agent', 'user-login-history' ),
			'time_login'    => __( 'Login Date-Time', 'user-login-history' ),
			'time_last_seen'    => __('Last Seen', 'user-login-history' ),
			'time_logout'    => __( 'Logout Date-Time', 'user-login-history' ),
			'login_status'    => __( 'Login Status', 'user-login-history' ),
                    );
                    return $columns;
    }
    
     private function get_all_default_frontend_columns() {
        $columns = array(
			'ip_address'    => 'ip_address',
			'browser'    => 'browser',
			'operating_system' => 'operating_system',
			'country_name'    => 'country_name',
			'time_login'    => 'time_login',
			'time_last_seen'    =>'time_last_seen',
			'time_logout'    => 'time_logout',
			'login_status'    => 'login_status',
                    );
                    return $columns;
    }

}
