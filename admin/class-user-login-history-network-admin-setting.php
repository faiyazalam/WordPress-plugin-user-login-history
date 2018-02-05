<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
class User_Login_History_Network_Admin_Setting 
{
    public $updated;
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
        ?>
 
        <div class="wrap">
 
            <h2><?php echo User_Login_History_Template_Helper::plugin_name() ?></h2>
 
            <?php if ( $this->updated ) : ?>
                <div class="updated notice is-dismissible">
                    <p><?php _e('Settings updated successfully!', 'my-plugin-domain'); ?></p>
                </div>
            <?php endif; ?>
 
            <form method="post">
                <input type="hidden" name="<?php echo $this->plugin_name.'_network_admin_setting_submit'?>" >
                <fieldset>
                     <label>
                        <?php _e('Block User', 'user-login-history'); ?>
                        <br/>
                        <input type="checkbox" <?php checked( $this->getSettings('block_user'), 1 ); ?>  name="block_user" value="<?php echo esc_attr($this->getSettings('block_user')); ?>" size="50" />
                    </label>
                </fieldset>
              

 
                <?php wp_nonce_field($this->plugin_name.'_network_admin_setting_nonce', $this->plugin_name.'_network_admin_setting_nonce'); ?>
                <?php submit_button(); ?>
 
            </form>
 
        </div>
 
        <?php
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
               return;  
            }
               
 
            if ( !wp_verify_nonce($_POST[$this->plugin_name.'_network_admin_setting_nonce'], $this->plugin_name.'_network_admin_setting_nonce') )
            {
                return;
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
 
      
        if ( $settings ) {
            // update new settings
            global $wpdb;
            update_site_option($this->plugin_name.'_settings', $settings);
            
        } else {
            // empty settings, revert back to default
            delete_site_option($this->plugin_name.'_settings');
        }
   
        $this->updated = true;
    }
 
    /**
      * Updates settings
      *
      * @param $setting string optional setting name
      */
 
    public function getSettings($setting='')
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
        ));
 
        if ( $setting ) {
            return isset($settings[$setting]) ? $settings[$setting] : null;
        }
        return $settings;
    }
}
