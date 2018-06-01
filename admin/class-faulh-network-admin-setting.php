<?php

/**
 * Faulh_Network_Admin_Setting
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 * @access private
 */
if(!class_exists('Faulh_Network_Admin_Setting'))
{
   class Faulh_Network_Admin_Setting {

    /**
     * The unique identifier of this plugin.
     *
     * @access   private
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    private $plugin_name;

    /**
     * Initialize the class and set its properties.
     *
     * @access public
     * @param      array    $args       The overridden arguments.
     * @param      string    $plugin_name       The name of this plugin.
     */
    function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
    }

    /**
     * The callback function for the action hook - network_admin_menu.
     */
    public function add_setting_menu() {
        add_submenu_page(
                'settings.php', Faulh_Template_Helper::plugin_name(), Faulh_Template_Helper::plugin_name(), 'manage_options', $this->plugin_name . '-setting', array($this, 'screen')
        );
    }

    /**
     * The template file for setting page.
     * 
     * @access public
     * @return string The template file path.
     */
    public function screen() {
        require_once plugin_dir_path((__FILE__)) . 'partials/settings/network-admin.php';
    }

    /**
     * Check nonce and form submission and then update the settings.
     * 
     * @access public
     */
    public function update() {

        if (isset($_POST[$this->plugin_name . "_network_admin_setting_submit"])) {

            if (empty($_POST[$this->plugin_name . '_network_admin_setting_nonce'])) {
                return false;
            }

            if (!wp_verify_nonce($_POST[$this->plugin_name . '_network_admin_setting_nonce'], $this->plugin_name . '_network_admin_setting_nonce')) {
                return false;
            }

            return $this->update_settings();
        }
    }

    /**
     * Update the settings.
     * 
     * @access private
     */
    private function update_settings() {
        $settings = array();
      

        if (isset($_POST['block_user'])) {
            $settings['block_user'] = 1;
        }
        if (isset($_POST['block_user_message'])) {
            $settings['block_user_message'] = sanitize_textarea_field($_POST['block_user_message']);
        }
       

        if ($settings) {
            // update new settings
            global $wpdb;
            update_site_option($this->plugin_name . '_settings', $settings);
        } else {
            // empty settings, revert back to default
            delete_site_option($this->plugin_name . '_settings');
        }

        return TRUE;
    }

    /**
     * Get the setting by name.
     *
     * @param $setting string optional setting name
     */
    public function get_settings($setting = '') {
        global $settings;

        if (isset($settings)) {
            if ($setting) {
                return isset($settings[$setting]) ? maybe_unserialize($settings[$setting]) : null;
            }
            return $settings;
        }

        $settings = wp_parse_args(get_site_option($this->plugin_name . '_settings'), array(
            'block_user' => null,
            'block_user_message' => 'Please contact website administrator.',
        ));

        if ($setting) {
            return isset($settings[$setting]) ?maybe_unserialize($settings[$setting]) : null;
        }
        return $settings;
    }

} 
}

