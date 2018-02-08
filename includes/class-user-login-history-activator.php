<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 * 
 * @link       https://github.com/faiyazalam
 * 
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
class User_Login_History_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     */
    public static function activate($network_wide) {
        global $wpdb;
        if (is_multisite() && $network_wide) {
            // Get all blogs from current network the network and activate plugin on each one
            $site_id = get_current_network_id();
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs where site_id = $site_id");
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                self:: on_plugin_activate();
            }
            restore_current_blog();
        } else {
            self:: on_plugin_activate();
        }
    }

    /**
     * Creates plugin table and options 
     */
    public static function on_plugin_activate() {
        self::create_table();
        self::update_options();
    }

    /**
     * Create main table for the plugin.
     */
    public static function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . USER_LOGIN_HISTORY_TABLE_NAME;

        $sql = "CREATE TABLE $table (
`id` int(11) NOT NULL AUTO_INCREMENT,
`session_token` varchar(200) NOT NULL,
`user_id` int(11) NOT NULL,
`username` varchar(200) NOT NULL,
`time_login` datetime NOT NULL,
`time_logout` datetime NULL,
`time_last_seen` datetime NOT NULL,
`ip_address` varchar(200) NOT NULL,
`browser` varchar(200) NOT NULL,
`operating_system` varchar(200) NOT NULL,
`country_name` varchar(200) NOT NULL,
`country_code` varchar(200) NOT NULL,
`timezone` varchar(200) NOT NULL,
`old_role` varchar(200) NOT NULL, 
`user_agent` text NOT NULL, 
`login_status` varchar(50) NOT NULL, 
`is_super_admin` INT(1) NOT NULL, 
PRIMARY KEY (`id`),
KEY `session_token` (`session_token`)
) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

    /**
     * Create table whenever a new blog is created.
     */
    public static function on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {
        if (is_plugin_active_for_network('WordPress-plugin-user-login-history/user-login-history.php')) {
            switch_to_blog($blog_id);
            self:: create_table();
            self::update_options();
            restore_current_blog();
        }
    }

    /**
     * Update plugin options.
     */
    public static function update_options() {
        update_option(USER_LOGIN_HISTORY_OPTION_PREFIX . 'version', USER_LOGIN_HISTORY_VERSION);
    }

}
