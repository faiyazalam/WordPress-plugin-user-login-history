<?php

namespace User_Login_History\Inc\Common\Helpers;

use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Login_Tracker;
use User_Login_History as NS;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 *
 * @author    Er Faiyaz Alam
 */
class Template {

    /**
     * Print out option html elements for all the time field types.
     * @param string $selected
     */
    static public function dropdown_time_field_types($selected = '') {
        $r = '';
        $types = self::time_field_types();
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
     * @param string $selected
     */
    static public function dropdown_login_statuses($selected = '') {
        $r = '';
        $types = self::login_statuses();

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
        $timezones = Date_Time_Helper::get_timezone_list();
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
     * Print the head section.
     * @param string $page
     */
    static public function head($page = '') {
        $author_urls = self::plugin_author_links();
        $h = "<h1>" . NS\PLUGIN_NAME . " " . NS\PLUGIN_VERSION . " " . esc_html__('(Basic Version)', 'faulh') . "</h1>";
        $h .= "<div>";

        if (!empty($author_urls) && is_array($author_urls)) {
            foreach ($author_urls as $key => $author_url) {
                if ($key > 0) {
                    $h .= " | ";
                }
                $h .= "<a href='" . $author_url['url'] . "' target='_blank'> " . $author_url['label'] . "</a>";
            }
        }

        $h .= "</div>";

        if (!empty($page)) {
            $h .= "<h2>$page</h2>";
        }
        echo $h;
    }

    /**
     * Print out option html elements for super admin.
     * @param type $selected
     */
    static public function dropdown_is_super_admin($selected = '') {
        $r = '';
        $types = self::super_admin_statuses();
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
     * Return options for super admin statuses.
     * @return array
     */
    static public function super_admin_statuses() {
        return array(
            'yes' => esc_html__("Yes", "faulh"),
            'no' => esc_html__("No", "faulh"),
        );
    }

    /**
     * This is used on filter form to filter the record based on given time interval.
     * @return array
     */
    static public function time_field_types() {
        return array(
            'login' => esc_html__("Login", "faulh"),
            'logout' => esc_html__("Logout", "faulh"),
            'last_seen' => esc_html__("Last Seen", "faulh"),
        );
    }

    /**
     * Return options for login statuses.
     * @return array
     */
    static public function login_statuses() {
        $types = array(
            Login_Tracker::LOGIN_STATUS_LOGIN => esc_html__("Logged In", "faulh"),
            Login_Tracker::LOGIN_STATUS_LOGOUT => esc_html__("Logged Out", "faulh"),
            Login_Tracker::LOGIN_STATUS_FAIL => esc_html__("Failed", "faulh"),
        );

        if (is_multisite()) {
            $types[Login_Tracker::LOGIN_STATUS_BLOCK] = esc_html__("Blocked", "faulh");
        }
        return $types;
    }

    /**
     * Return options for author links.
     * @return array
     */
    static public function plugin_author_links() {
        return array(
            array('key' => 'userloginhistory', 'url' => 'http://userloginhistory.com/', 'label' => 'Official Website'),
            array('key' => 'linkedin', 'url' => 'https://www.linkedin.com/in/er-faiyaz-alam-0704219a', 'label' => 'Linkedin'),
            array('key' => 'paypal', 'url' => 'https://www.paypal.me/erfaiyazalam/', 'label' => esc_html__('Donate', 'faulh')),
        );
    }

    /**
     * Print html link in button style.
     */
    static public function create_button($url = '', $label = '') {
        echo "<a href='$url' class='button-secondary' target='_blank'>$label</a>";
    }

}
