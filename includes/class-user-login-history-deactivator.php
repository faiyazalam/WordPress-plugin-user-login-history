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
        self::drop_table();
        self::delete_options();
        self::delete_user_meta();
    }

    public static function drop_table() {
        global $wpdb;
        $table = $wpdb->prefix . ULH_TABLE_NAME;
        $sql = "DROP TABLE IF EXISTS .$table";
        $wpdb->query($sql);
    }

    public static function delete_user_meta() {
        global $wpdb;
        $option_prefix = ULH_PLUGIN_OPTION_PREFIX."user_timezone";
        $sql_user_meta = "delete from $wpdb->usermeta where meta_key LIKE '$option_prefix'";
        $wpdb->query($sql_user_meta);
    }

    public static function delete_options() {
        $option_prefix = ULH_PLUGIN_OPTION_PREFIX;
        delete_option($option_prefix . "_version");
        delete_option($option_prefix . "_frontend_fields");
        delete_option($option_prefix . "_frontend_limit");
        delete_option($option_prefix . "_preferred_timezone");
    }

}
