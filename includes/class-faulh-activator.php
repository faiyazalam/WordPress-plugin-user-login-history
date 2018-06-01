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
if (!class_exists('Faulh_Activator')) {

    class Faulh_Activator {

        /**
         *
         * Fired on plugin activation.
         * 
         * @access public
         */
        public static function activate($network_wide) {
            global $wpdb;
            if (is_multisite() && $network_wide) {
                // Get all blogs from current network the network and activate plugin on each one
                $site_id = get_current_network_id();
                $blog_ids = Faulh_DB_Helper::get_blog_by_id_and_network_id(null, get_current_network_id());
                foreach ($blog_ids as $blog_id) {
                    switch_to_blog($blog_id);
                    self::create_table();
                    self::update_options();
                }
                restore_current_blog();
            } else {
                self::create_table();
                self::update_options();
            }
        }

        /**
         * Create main table for the plugin.
         * 
         * @access public
         */
        public static function create_table() {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table = $wpdb->prefix . FAULH_TABLE_NAME;

            $sql = "CREATE TABLE $table (
id int(11) NOT NULL AUTO_INCREMENT,
session_token varchar(100) NOT NULL,
user_id int(11) NOT NULL,
username varchar(200) NOT NULL,
time_login datetime NOT NULL,
time_logout datetime NULL,
time_last_seen datetime NOT NULL,
ip_address varchar(200) NOT NULL,
browser varchar(200) NOT NULL,
browser_version varchar(100) NOT NULL,
operating_system varchar(200) NOT NULL,
country_name varchar(200) NOT NULL,
country_code varchar(200) NOT NULL,
timezone varchar(200) NOT NULL,
old_role varchar(200) NOT NULL,
user_agent text NOT NULL,
login_status varchar(50) NOT NULL,
is_super_admin INT(1) NOT NULL,
PRIMARY KEY  (id),
INDEX faulh_user_traker_index (session_token,user_id)
) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($sql);

            if (!empty($wpdb->last_error)) {
                Faulh_Error_Handler::error_log("Error while creating or updating tables-" . $wpdb->last_error, __LINE__, __FILE__);
                wp_die($wpdb->last_error);
            }
        }

        /**
         * Create table whenever a new blog is created.
         * 
         * @access public
         */
        public static function on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {
            if (is_plugin_active_for_network(FAULH_BOOTSTRAP_FILE_PATH)) {
                switch_to_blog($blog_id);
                self:: create_table();
                self::update_options();
                restore_current_blog();
            }
        }

        /**
         * Update plugin options.
         * 
         * @access public
         */
        public static function update_options() {
            update_option(FAULH_OPTION_NAME_VERSION, FAULH_VERSION);
        }

    }

}