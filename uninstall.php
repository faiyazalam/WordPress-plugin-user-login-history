<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://userloginhistory.com
 * @since      1.0.0
 *
 * @package    User_Login_History
 */
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


if (!function_exists('faulh_delete_plugin_options')) {

    function faulh_delete_plugin_options($prefix = '') {
        if (empty($prefix)) {
            return;
        }
        $options = array(
            'fa_userloginhostory_version',
            $prefix . "_basics",
            $prefix . "_advanced",
        );

        foreach ($options as $option) {
            delete_option($option);
        }
    }

}



if (!function_exists('faulh_uninstall_plugin')) {

    function faulh_uninstall_plugin() {
        $plugin_name = 'faulh';
        $table_name = 'fa_user_logins';

        global $wpdb;
        if (is_multisite()) {
            $blog_ids = faulh_get_blogs_of_current_network();
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . $table_name);
                faulh_delete_plugin_options($plugin_name);
            }
            restore_current_blog();
        } else {
            $wpdb->query("DROP TABLE " . $wpdb->prefix . $table_name);
            faulh_delete_plugin_options($plugin_name);
        }

        $user_meta_keys = array(
            $plugin_name . "_timezone",
            $plugin_name . "_rows_per_page",
            "managetoplevel_page_" . $plugin_name . "-login-listingcolumnshidden",
        );
        $sql_in = "'" . implode("', '", $user_meta_keys) . "'";

        $wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key IN ($sql_in)");
    }

}


if (!function_exists('faulh_get_blogs_of_current_network')) {

    function faulh_get_blogs_of_current_network() {
        global $wpdb;
        $sql = "SELECT blog_id FROM $wpdb->blogs WHERE site_id = " . get_current_network_id() . "  ORDER BY blog_id ASC";
        $result = $wpdb->get_col($sql);
        if ($wpdb->last_error) {
            faulh_error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
        }
        return $result;
    }

}

if (!function_exists('faulh_error_log')) {

    function faulh_error_log($message = '') {
        ini_set('error_log', WP_CONTENT_DIR . '/user-login-history.log');
        error_log("Error While Uninstalling: " . $message);
    }

}



faulh_uninstall_plugin();
