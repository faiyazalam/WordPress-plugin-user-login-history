<?php

/**
 * This class contains all the template related functions.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if (!class_exists('Faulh_Template_Helper')) {

    class Faulh_Template_Helper {

        /**
         * Print out option html elements for all the time field types.
         * @param string $selected
         */
        static public function dropdown_time_field_types($selected = '') {
            $r = '';
            $types = array(
                'login' => esc_html__("Login", "faulh"),
                'logout' => esc_html__("Logout", "faulh"),
                'last_seen' => esc_html__("Last Seen", "faulh"),
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
            $timezones = Faulh_Date_Time_Helper::get_timezone_list();
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

        /**
         * Print the head section.
         * @param string $page
         */
        static public function head($page = '') {
            $author_urls = self::plugin_author_links();
            $h = "<h1>" . self::plugin_name() . " " . FAULH_VERSION . " " . esc_html__('(Basic Version)', 'faulh') . "</h1>";
            $h .= "<div>";

            if (!empty($author_urls['wordpress'])) {
                $h .= "<a href='" . $author_urls['wordpress']['url'] . "' target='_blank'> " . esc_html__('About Author', 'faulh') . "</a>";

                if (!empty($author_urls['paypal'])) {
                    $h .= " | <a href='" . $author_urls['paypal']['url'] . "' target='_blank'> " . $author_urls['paypal']['label'] . "</a>";
                }
            }
            
   $h .= " | <a href='http://www.userloginhistory.com' target='_blank'>Plugin Website</a>";

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
         * Return options for login statuses.
         * @return array
         */
        static public function login_statuses() {
            $types = array(
                Faulh_User_Tracker::LOGIN_STATUS_LOGIN => esc_html__("Logged In", "faulh"),
                Faulh_User_Tracker::LOGIN_STATUS_LOGOUT => esc_html__("Logged Out", "faulh"),
                Faulh_User_Tracker::LOGIN_STATUS_FAIL => esc_html__("Failed", "faulh"),
            );

            if (is_multisite()) {
                $types[Faulh_User_Tracker::LOGIN_STATUS_BLOCK] = esc_html__("Blocked", "faulh");
            }
            return $types;
        }

        /**
         * Return options for author links.
         * @return array
         */
        static public function plugin_author_links() {
            return array(
                'stackoverflow' => array('url' => 'http://stackoverflow.com/users/4380588/faiyaz-alam', 'label' => 'Stack Overflow'),
                'wordpress' => array('url' => 'https://profiles.wordpress.org/faiyazalam', 'label' => 'WordPress'),
                'github' => array('url' => 'https://github.com/faiyazalam', 'label' => 'GitHub'),
                'linkedin' => array('url' => 'https://www.linkedin.com/in/er-faiyaz-alam-0704219a', 'label' => 'Linkedin'),
                'upwork' => array('url' => 'https://www.upwork.com/o/profiles/users/_~01737016f9bf37a62b/', 'label' => 'Upwork'),
                'peopleperhour' => array('url' => 'https://www.peopleperhour.com/freelancer/er-faiyaz/php-cakephp-zend-magento-moodle-tot/1016456', 'label' => 'People Per Hour'),
                'paypal' => array('url' => 'https://www.paypal.me/erfaiyazalam/', 'label' => esc_html__('Donate', 'faulh')),
            );
        }

        /**
         * Print html link in button style.
         */
        static public function create_button($url = '', $label = '') {
            echo "<a href='$url' class='button-secondary' target='_blank'>$label</a>";
        }

    }

}
