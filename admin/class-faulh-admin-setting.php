<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
if ( !class_exists('Faulh_Admin_Setting' ) ):
class Faulh_Admin_Setting {

    private $settings_api;
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
     * The transient for admin notice purpose.
     *
     * @access   private
     * @var      string    $admin_notice_transient
     */

    function __construct($plugin_name, $version) {
        require_once  plugin_dir_path(dirname(__FILE__)).'vendors/tareq1988/class.settings-api.php';
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->settings_api = new WeDevs_Settings_API();
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( 'User Login History', 'User Login History', 'manage_options', 'faulh-admin-setting', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => $this->plugin_name.'_basics',
                'title' => __( 'Basic Settings', 'faulh' ),
            ),
            array(
                'id'    => $this->plugin_name.'_advance',
                'title' => __( 'Advance Settings', 'faulh' ),
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
            $this->plugin_name.'_basics' => array(
                array(
                    'name'              => 'is_status_online',
                    'label'             => __( 'Online', 'faulh' ),
                    'desc'              => __( 'Maximum number of minutes for online users.', 'faulh' ),
                    'placeholder'       => __( '1.99', 'faulh' ),
                    'min'               => 0,
                    'max'               => 100,
                    'step'              => '0.01',
                    'type'              => 'number',
                    'default'           => FAULH_DEFAULT_IS_STATUS_ONLINE_MIN,
                    'sanitize_callback' => 'floatval'
                ),
                 array(
                    'name'              => 'is_status_idle',
                    'label'             => __( 'Idle', 'faulh' ),
                    'desc'              => __( 'Maximum number of minutes for idle users.', 'faulh' ),
                    'placeholder'       => __( '1.99', 'faulh' ),
                    'min'               => 0,
                    'max'               => 100,
                    'step'              => '0.01',
                    'type'              => 'number',
                    'default'           => FAULH_DEFAULT_IS_STATUS_IDLE_MIN,
                    'sanitize_callback' => 'floatval'
                ),
            ),
             $this->plugin_name.'_advance' => array(
                array(
                    'name'              => 'is_geo_tracker_enabled',
                    'label'             => __( 'Enable Geo Traker', 'faulh' ),
                    'desc'              => __( 'Enable tracking of country and timezone.', 'faulh' ),
                    'type'              => 'checkbox',
                    'default'           => FALSE,
                ),
                
            ),
            
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';
echo Faulh_Template_Helper::head();
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    } 

}
endif;
