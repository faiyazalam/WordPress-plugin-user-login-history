<?php

/**
 * This is used to create admin setting page.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 * @access private
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
     * The transient for admin notice purpose.
     *
     * @access   private
     * @var      string    $admin_notice_transient
     */

    function __construct($plugin_name) {
        require_once  plugin_dir_path(dirname(__FILE__)).'vendors/tareq1988/class.settings-api.php';
        $this->plugin_name = $plugin_name;
        $this->settings_api = new WeDevs_Settings_API();
    }

    /**
     * Callback function for the action - admin_init
     */
    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

        /**
     * Callback function for the action - admin_menu
     */
    function admin_menu() {
        add_options_page( Faulh_Template_Helper::plugin_name(), Faulh_Template_Helper::plugin_name(), 'manage_options', $this->plugin_name.'-admin-setting', array($this, 'render_setting_page') );
    }

    /**
     * Get all the setting sections.
     * @return array
     */
    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => $this->plugin_name.'_basics',
                'title' => esc_html__( 'Basic Settings', 'faulh' ),
            ),
            array(
                'id'    => $this->plugin_name.'_advanced',
                'title' => esc_html__( 'Advanced Settings', 'faulh' ),
            )
           
        );
        return $sections;
    }

    /**
     * Get all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            $this->plugin_name.'_basics' => array(
                array(
                    'name'              => 'is_status_online',
                    'label'             => esc_html__( 'Online', 'faulh' ),
                    'desc'              => esc_html__( 'Maximum number of minutes for online users. Default is', 'faulh' )." ".FAULH_DEFAULT_IS_STATUS_ONLINE_MIN,
                    'min'               => 0,
                    'max'               => 100,
                    'step'              => '1',
                    'type'              => 'number',
                    'default'           => FAULH_DEFAULT_IS_STATUS_ONLINE_MIN,
                    'sanitize_callback' => 'absint'
                ),
                 array(
                    'name'              => 'is_status_idle',
                    'label'             => esc_html__( 'Idle', 'faulh' ),
                    'desc'              => esc_html__( 'Maximum number of minutes for idle users. This should be greater than that of online users. Default is', 'faulh' )." ".FAULH_DEFAULT_IS_STATUS_IDLE_MIN,
                    'min'               => 0,
                    'max'               => 100,
                    'step'              => '1',
                    'type'              => 'number',
                    'default'           => FAULH_DEFAULT_IS_STATUS_IDLE_MIN,
                    'sanitize_callback' => 'absint'
                ),
            ),
             $this->plugin_name.'_advanced' => array(
                array(
                    'name'              => 'is_geo_tracker_enabled',
                    'label'             => esc_html__( 'Geo Tracker', 'faulh' )."<br>".esc_html__( '(Not Recommended)', 'faulh' ),
                    'desc'              => esc_html__( 'Enable tracking of country and timezone. This functionality is dependent on a free third-party API service, hence not recommended. For more info, see the "Help" page under the plugin menu.', 'faulh' ),
                    'type'              => 'checkbox',
                    'default'           => FALSE,
                ),
            ),
            
        );

        return $settings_fields;
    }

    /**
     * Callback function to render setting page.
     */
    function render_setting_page() {
        echo '<div class="wrap">';
echo Faulh_Template_Helper::head();
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    } 
    
    /**
     * Get setting value
     * @param type $name
     * @return type mixed
     */
    public function get_options($name = '') {
        if($name)
        {
             return get_option($this->plugin_name."_$name"); 
        }
       
         
        $sections = $this->get_settings_sections();
        $options = array();
        
        foreach ($sections as $key => $value) {
            $options[$value['id']] =  get_option($value['id']);
        }
      
        return $options;
    }

}
endif;
