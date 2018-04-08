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
 * @link       https://github.com/faiyazalam
 * @since      1.0.0
 *
 * @package    User_Login_History
 */
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

if (!defined('FAULH_PLUGIN_NAME')) {
    define('FAULH_PLUGIN_NAME', 'faulh');
}

if (!defined('FAULH_TABLE_NAME')) {
    define('FAULH_TABLE_NAME', 'fa_user_logins');
}

require_once plugin_dir_path(__FILE__) . 'includes/class-faulh-db-helper.php';


if (!function_exists('faulh_delete_plugin_options')) {

    function faulh_delete_plugin_options() {
        $options = array(
            'fa_userloginhostory_version',
            FAULH_PLUGIN_NAME . "_basics",
            FAULH_PLUGIN_NAME . "_advanced",
        );

        foreach ($options as $option) {
            delete_option($option);
        }
    }

}



if (!function_exists('faulh_uninstall_plugin')) {

    function faulh_uninstall_plugin() {
     
        global $wpdb;
        if (is_multisite()) {
            $blog_ids = Faulh_DB_Helper::get_blog_by_id_and_network_id(null, get_current_network_id());
            foreach ($blog_ids as $blog_id) {
                switch_to_blog($blog_id);
                $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . FAULH_TABLE_NAME);
                faulh_delete_plugin_options();
            }
            restore_current_blog();
        } else {
            $wpdb->query("DROP TABLE " . $wpdb->prefix . FAULH_TABLE_NAME);
            faulh_delete_plugin_options();
        }

        $wpdb->query("DELETE FROM $wpdb->usermeta WHERE meta_key IN ('".FAULH_PLUGIN_NAME."_timezone', '".FAULH_PLUGIN_NAME."_rows_per_page', 'managetoplevel_page_".FAULH_PLUGIN_NAME."-admin-listing-networkcolumnshidden', 'managetoplevel_page_".FAULH_PLUGIN_NAME."-admin-listingcolumnshidden')");
    }

}

faulh_uninstall_plugin();
?>