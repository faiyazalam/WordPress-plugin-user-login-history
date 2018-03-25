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
         * @global object $wpdb
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
         * @global object $wpdb
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

        static public function head($page = '') {
            $h = "<h1>" . self::plugin_name() . " " . FAULH_VERSION . " " . esc_html__('(Basic Version)', 'faulh') . "</h1>";
            $h .= "<span class='aboutAuthor'> <a href='https://profiles.wordpress.org/faiyazalam' title='" . esc_attr__('Click here to visit author profile', 'faulh') . "' target='_blank'> " . esc_html__('About Author', 'faulh') . " </span></a>";
            if (!empty($page)) {
                $h .= "<h2>$page</h2>";
            }
            echo $h;
        }

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

        static public function super_admin_statuses() {
            return array(
                '1' => esc_html__("Yes", "faulh"),
                '0' => esc_html__("No", "faulh"),
            );
        }

        static public function login_statuses() {
            $types = array(
                Faulh_User_Tracker::LOGIN_STATUS_LOGIN => esc_html__("Logged in", "faulh"),
                Faulh_User_Tracker::LOGIN_STATUS_LOGOUT => esc_html__("Logged out", "faulh"),
                Faulh_User_Tracker::LOGIN_STATUS_FAIL => esc_html__("Failed", "faulh"),
            );

            if (is_multisite()) {
                $types[Faulh_User_Tracker::LOGIN_STATUS_BLOCK] = esc_html__("Blocked", "faulh");
            }
            return $types;
        }
        
        static function checkbox_all_columns($selected = array(), $field_name = 'test') {
            if(is_string($selected))
            {
                $selected = explode(',', $selected);
                $selected = array_map('trim', $selected);
            }
            $all_columns = Faulh_DB_Helper::all_columns();
            
                        $r = '';
          
            foreach ($all_columns as $key => $option_name) {
                $checked = in_array($key, $selected) ? 'checked' : '';
         $r .= "<lable for='column_$key'><input id='column_$key' $checked type='checkbox' name='$field_name' value='" . $key . "'>$option_name</lable><br>";

            }
            echo $r;
            
            
        }
        }

}
