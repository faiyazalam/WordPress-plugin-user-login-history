<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History\Inc\Admin\Settings_Api;
use User_Login_History as NS;

class Settings {

    private $settings_api;
    private $plugin_name;
    private $plugin_text_domain;
    private $version;

    function __construct($plugin, $version, $plugin_text_domain, Settings_Api $settings_api) {
        $this->settings_api = $settings_api;
        $this->plugin_name = $plugin;
        $this->plugin_text_domain = $plugin_text_domain;
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());
        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page('User Login History', 'User Login History', 'delete_posts', $this->plugin_name."-settings", array($this, 'plugin_page'));
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => $this->plugin_name . '_basics',
                'title' => esc_html__('Basic Settings', 'faulh'),
            ),
            array(
                'id' => $this->plugin_name . '_advanced',
                'title' => esc_html__('Advanced Settings', 'faulh'),
            )
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
            $this->plugin_name . '_basics' => array(
                array(
                    'name' => 'is_status_online',
                    'label' => esc_html__('Online', 'faulh'),
                    'desc' => esc_html__('Maximum number of minutes for online users. Default is', 'faulh') . " " . NS\DEFAULT_IS_STATUS_ONLINE_MIN,
                    'min' => 1,
                    'step' => '1',
                    'type' => 'number',
                    'default' => NS\DEFAULT_IS_STATUS_ONLINE_MIN,
                    'sanitize_callback' => 'absint'
                ),
                array(
                    'name' => 'is_status_idle',
                    'label' => esc_html__('Idle', 'faulh'),
                    'desc' => esc_html__('Maximum number of minutes for idle users. This should be greater than that of online users. Default is', 'faulh') . " " . NS\DEFAULT_IS_STATUS_IDLE_MIN,
                    'min' => 1,
                    'step' => '1',
                    'type' => 'number',
                    'default' => NS\DEFAULT_IS_STATUS_IDLE_MIN,
                    'sanitize_callback' => 'absint'
                ),
            ),
            $this->plugin_name . '_advanced' => array(
                array(
                    'name' => 'is_geo_tracker_enabled',
                    'label' => esc_html__('Geo Tracker', 'faulh') . "<br>" . esc_html__('(Not Recommended)', 'faulh'),
                    'desc' => esc_html__('Enable tracking of country and timezone. This functionality is dependent on a free third-party API service, hence not recommended. For more info, see the "Help" page under the plugin menu.', 'faulh'),
                    'type' => 'checkbox',
                    'default' => FALSE,
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
