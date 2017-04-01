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
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . ULH_TABLE_NAME;

        $sql = "CREATE TABLE $table (
id int(11) NOT NULL AUTO_INCREMENT,
user_id int(11) ,
`username` varchar(100) NOT NULL,
`time_login` datetime NOT NULL,
`time_logout` datetime NOT NULL,
`time_last_seen` datetime NOT NULL,
`ip_address` varchar(20) NOT NULL,
`browser` varchar(100) NOT NULL,
`operating_system` varchar(100) NOT NULL,
`country_name` varchar(100) NOT NULL,
`country_code` varchar(20) NOT NULL	,
`timezone` varchar(20) NOT NULL	,
`old_role` varchar(200) NOT NULL	, 
PRIMARY KEY (`id`)
) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
        update_option(ULH_PLUGIN_OPTION_PREFIX . 'version', ULH_PLUGIN_VERSION);
       update_option(ULH_PLUGIN_OPTION_PREFIX.'frontend_fields', array(
           'ip_address'=>1,
           'old_role'=>1, 
           'country'=>1, 
           'login'=>1, 
           'logout'=>1,
           'duration'=>1,
           ));
       update_option(ULH_PLUGIN_OPTION_PREFIX.'frontend_limit', '20');
    }

}
