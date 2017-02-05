<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 */
class User_Login_History_Deactivator {

    /**
     * The function which handles deactivation of our plugin
     */
    public static function deactivate() {
        global $wpdb;
        $table = $wpdb->prefix . ULH_TABLE_NAME;
        $option_prefix = ULH_PLUGIN_OPTION_PREFIX;
        $sql = "DROP TABLE IF EXISTS .$table";
        $wpdb->query($sql);
        
        $sql_user_meta = "delete from {$wpdb->prefix}usermeta where meta_key LIKE '%$option_prefix%'";
        $wpdb->query($sql_user_meta);
        
        delete_option($option_prefix."_version");
        delete_option($option_prefix."_frontend_fields");
        delete_option($option_prefix."_frontend_limit");
        delete_option($option_prefix."_preferred_timezone");
    }

}
