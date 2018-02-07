<?php

/**
 * User_Login_History_Template_Helper
 * 
 * This class contains all the template related functions.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam <support@userloginhistory.com>
 */
class User_Login_History_Template_Helper {

    /**
     * Print out option html elements for all the blogs of the current network.
     * @global object $wpdb
     * @param string $selected
     */
    static public function dropdown_blogs($selected = '') {
        global $wpdb;
        $r = '';
        $site_id = get_current_network_id();
        $blogs = $wpdb->get_results("SELECT blog_id, domain, path FROM $wpdb->blogs where site_id = $site_id", 'ARRAY_A');
        foreach ($blogs as $blog) {
            $name = $blog['domain'] . $blog['path'];
            if ($selected == $blog['blog_id']) {
                $r .= "\n\t<option selected='selected' value='" . esc_attr($blog['blog_id']) . "'>$name</option>";
            } else {
                $r .= "\n\t<option value='" . esc_attr($blog['blog_id']) . "'>$name</option>";
            }
        }
        echo $r;
    }

    /**
     * Print out option html elements for all the networks.
     * @global object $wpdb
     * @param string $selected
     */
    static public function dropdown_sites($selected = '') {
        global $wpdb;
        $r = '';
        $sites = $wpdb->get_results("SELECT id, domain, path FROM $wpdb->site", 'ARRAY_A');
        foreach ($sites as $site) {
            $name = $site['domain'] . $site['path'];
            if ($selected == $site['id']) {
                $r .= "\n\t<option selected='selected' value='" . esc_attr($site['id']) . "'>$name</option>";
            } else {
                $r .= "\n\t<option value='" . esc_attr($site['id']) . "'>$name</option>";
            }
        }
        echo $r;
    }

    /**
     * Print out option html elements for all the time field types.
     * @global object $wpdb
     * @param string $selected
     */
    static public function dropdown_time_field_types($selected = '') {
        $r = '';
        $types = array(
            'login' => __("Login", "user-login-history"),
            'logout' => __("Logout", "user-login-history"),
            'last_seen' => __("Last Seen", "user-login-history"),
        );
        foreach ($types as $key => $type) {
            $name = $type;
            if ($selected == $key) {
                $r .= "\n\t<option selected='selected' value='" . $key . "'>$name</option>";
            } else {
                $r .= "\n\t<option value='" . $key . "'>$name</option>";
            }
        }
        echo $r;
    }

    /**
     * Print out option html elements for all the login statuses.
     * @global object $wpdb
     * @param string $selected
     */
    static public function dropdown_login_statuses($selected = '') {
        $r = '';
        $types = array(
            User_Login_History_User_Tracker::LOGIN_STATUS_LOGIN => __("Login", "user-login-history"),
            User_Login_History_User_Tracker::LOGIN_STATUS_LOGOUT => __("Logout", "user-login-history"),
            User_Login_History_User_Tracker::LOGIN_STATUS_FAIL => __("Fail", "user-login-history"),
        );

        if (is_multisite()) {
            $types[User_Login_History_User_Tracker::LOGIN_STATUS_BLOCK] = __("Block", "user-login-history");
        }

        foreach ($types as $key => $type) {
            $name = $type;
            if ($selected == $key) {
                $r .= "\n\t<option selected='selected' value='" . $key . "'>$name</option>";
            } else {
                $r .= "\n\t<option value='" . $key . "'>$name</option>";
            }
        }
        echo $r;
    }

    /**
     * Print out option html elements for all the timezones.
     * @global object $wpdb
     * @param string $selected
     */
    static public function dropdown_timezones($selected = '') {
        $r = '';
        $timezones = User_Login_History_Date_Time_Helper::get_timezone_list();
        foreach ($timezones as $timezone) {
            $key = $timezone['zone'];
            $name = $timezone['zone'] . "(" . $timezone['diff_from_GMT'] . ")";
            if ($selected == $key) {
                $r .= "\n\t<option selected='selected' value='" . $key . "'>$name</option>";
            } else {
                $r .= "\n\t<option value='" . $key . "'>$name</option>";
            }
        }
        echo $r;
    }

    /**
     * Returns plugin name.
     * @return string Returns plugin name.
     */
    static public function plugin_name() {
        return "User Login History";
    }

}