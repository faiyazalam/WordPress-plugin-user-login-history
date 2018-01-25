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
                    'desc' => __('This is used for frontend listing table.', 'wedevs'),
                    'type' => 'select',
                    'options' => array(
                        'current_user' => 'Show records of the current loggedin user only.',
                        'all_users' => 'Show records of all the users.',
                    )
                ),
                array(
                    'name' => 'frontend_columns',
                    'label' => __('Display Columns', 'wedevs'),
                    'desc' => __('Choose the columns to be displayed on frontend listing table.', 'wedevs'),
                    'type' => 'multicheck',
                    'default' => array('one' => 'ones', 'four' => 'four'),
                    'options' => array(
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                        'four' => 'Four'
                    )
                ),
            ),
            $this->plugin_name . '-advanced' => array(
                array(
                    'name' => 'login_status_login_color',
                    'label' => __('Status Color', 'wedevs'),
                    'desc' => __('Select color for login.', 'wedevs'),
                    'type' => 'color',
                    'default' => ''
                ),
                array(
                    'name' => 'login_status_logout_color',
                    'desc' => __('Select color for logout.', 'wedevs'),
                    'type' => 'color',
                    'default' => ''
                ),
                array(
                    'name' => 'login_status_fail_color',
                    'desc' => __('Select color for fail.', 'wedevs'),
                    'type' => 'color',
                    'default' => ''
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

}
