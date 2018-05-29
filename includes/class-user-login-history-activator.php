<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 */
class User_Login_History_Activator {

    public static function activate() {
        self::create_table();
        self::update_options();
    }

    public static function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . ULH_TABLE_NAME;

        $sql = "CREATE TABLE $table (
id int(11) NOT NULL AUTO_INCREMENT,
user_id int(11) ,
`username` varchar(200) NOT NULL,
`time_login` datetime NOT NULL,
`time_logout` datetime NULL,
`time_last_seen` datetime NOT NULL,
`ip_address` varchar(200) NOT NULL,
`browser` varchar(200) NOT NULL,
`operating_system` varchar(200) NOT NULL,
`country_name` varchar(200) NOT NULL,
`country_code` varchar(200) NOT NULL	,
`timezone` varchar(200) NOT NULL	,
`old_role` varchar(200) NOT NULL	, 
`user_agent` text NOT NULL	, 
PRIMARY KEY (`id`)
) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

    public static function update_options() {
        update_option(ULH_PLUGIN_OPTION_PREFIX . 'version', ULH_PLUGIN_VERSION);
        update_option(ULH_PLUGIN_OPTION_PREFIX . 'frontend_fields', array(
            'ip_address' => 1,
            'old_role' => 1,
            'country' => 1,
            'login' => 1,
            'logout' => 1,
            'duration' => 1,
        ));
        update_option(ULH_PLUGIN_OPTION_PREFIX . 'frontend_limit', '20');
    }

}
