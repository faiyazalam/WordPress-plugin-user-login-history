<?php

namespace User_Login_History\Inc\Core;
use User_Login_History as NS;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       http://userloginhistory.com
 * @since      1.0.0
 *
 * @author     Er Faiyaz Alam
 **/
class Activator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
			$min_php = '5.6.0';

		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
		}
                
                self::create_table();
                
                

	}
        
        
         public static function create_table() {
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $table = $wpdb->prefix . NS\PLUGIN_TABLE_FA_USER_LOGINS;

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
geo_response text NOT NULL, 
login_status varchar(50) NOT NULL, 
is_super_admin INT(1) NOT NULL, 
PRIMARY KEY  (id),
INDEX faulh_user_traker_index (session_token,user_id)
) $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($sql);

            if (!empty($wpdb->last_error)) {
                Faulh_Error_Handler::error_log("Error while creating or updatiing tables-" . $wpdb->last_error, __LINE__, __FILE__);
                wp_die($wpdb->last_error);
            }
        }

}
