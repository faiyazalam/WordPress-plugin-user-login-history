<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
class User_Login_History_Network_Admin_Setting 
{
    public $plugin_name;
    
    function __construct($plugin_name = 'user-login-history') {
        $this->plugin_name = $plugin_name;
    }
    /**
      * This method will be used to register
      * our custom settings admin page
      */
 

 
    /**
      * This method will be used to register
      * our custom settings admin page
      */
 
    public function add_setting_menu()
    {
        add_submenu_page(
            'settings.php',
            __('User Login History', 'user-login-history'),
            __('User Login History'),
            'manage_options',
            $this->plugin_name.'-setting',
            array($this, 'screen')
        );
 
        return $this;
    }
 
    /**
      * This method will parse the contents of
      * our custom settings age
      */
 
    public function screen()
    {
        require_once plugin_dir_path((__FILE__)) . 'partials/settings/network-admin.php';
    }
 
    /**
      * Check for POST (form submission)
      * Verifies nonce first then calls
      * update_settings method to update.
      */
 
    public function update()
    {
     
        if ( isset($_POST[$this->plugin_name."_network_admin_setting_submit"]) ) {
             
            if ( empty( $_POST[$this->plugin_name.'_network_admin_setting_nonce'] ) )
            {
               return false;  
            }
               
 
            if ( !wp_verify_nonce($_POST[$this->plugin_name.'_network_admin_setting_nonce'], $this->plugin_name.'_network_admin_setting_nonce') )
            {
                return false;
            }
 
            return $this->update_settings();
        }
    }
 
    /**
      * Updates settings
      */
 
    public function update_settings()
    {
        $settings = array();
       
 
        if ( isset($_POST['block_user']) ) {
            $settings['block_user'] = 1;
        }
        if ( isset($_POST['block_user_message']) ) {
            $settings['block_user_message'] = sanitize_textarea_field($_POST['block_user_message']);
        }
 
      
        if ( $settings ) {
            // update new settings
            global $wpdb;
            update_site_option($this->plugin_name.'_settings', $settings);
            
        } else {
            // empty settings, revert back to default
            delete_site_option($this->plugin_name.'_settings');
        }
   
        return TRUE;
    }
 
    /**
      * Updates settings
      *
      * @param $setting string optional setting name
      */
 
    public function get_settings($setting='')
    {
        global $settings;
 
        if ( isset($settings) ) {
            if ( $setting ) {
                return isset($settings[$setting]) ? $settings[$setting] : null;
            }
            return $settings;
        }
 
        $settings = wp_parse_args(get_site_option($this->plugin_name.'_settings'), array(
            'block_user' => null,
            'block_user_message' => 'Please contact website administrator.',
        ));
 
        if ( $setting ) {
            return isset($settings[$setting]) ? $settings[$setting] : null;
        }
        return $settings;
    }
}
