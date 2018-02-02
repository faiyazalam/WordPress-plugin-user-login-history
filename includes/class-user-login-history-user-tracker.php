<?php

/**
 * class User_Login_History_User_Tracker {

 * This class is used to track user based on different attributes e.g. ip, browser etc.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 */
class User_Login_History_User_Tracker {

    /**
     * Login Status Constants
     * DO NOT CHANGE THE VALUE OF THESE CONSTANTS BECAUSE THEY ARE SAVED IN DB.
     */
    const LOGIN_STATUS_LOGIN = 'Login';
    const LOGIN_STATUS_FAIL = 'Fail';
    const LOGIN_STATUS_LOGOUT = 'Logout';

    static $instance;
    private $session_token;
    private $table_name;

    /**
     * user meta key to hold timezone of current user.
     * 
     * @access   private
     * @var      string    $user_meta_timezone    The timezone of current user.
     */
    private $user_meta_timezone;
    private $geo_object;
    private $browser_object;

    /**
     * Initialize the class and set its properties.
     *
     * @var      string    $name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct() {
        $this->user_meta_timezone = USER_LOGIN_HISTORY_USER_META_PREFIX . "user_timezone";
    }

   
    
    /**
     * Fire if login success
     *
     * @param string $user_login username
     * @param object $user wp user object
     */
    public function user_login($user_login, $user) {
        $this->save_login($user_login, $user, self::LOGIN_STATUS_LOGIN);
    }

    /**
     * Update last seen time for current user.
     *
     */
    public function update_time_last_seen() {

        global $wpdb;
        $current_user = wp_get_current_user();
        $table = User_Login_History_DB_Helper::get_table_name();
        $current_date = User_Login_History_Date_Time_Helper::get_current_date_time();
        $user_id = $current_user->ID;

        $last_id = User_Login_History_Session_Helper::get_last_insert_id();
        if (!$user_id || !$last_id) {
            return;
        }
        $sql = "update $table set time_last_seen='$current_date' where id = '$last_id' and user_id = '$user_id'";

        $wpdb->query($sql);
        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
    }

    /**
     * Fire if login failed.
     *
     * @param string $user_login username
     */
    public function user_login_failed($user_login) {
        $this->save_login($user_login, NULL, self::LOGIN_STATUS_FAIL);
    }

    /**
     * Fire on logout
     * Save logout time of current user.
     *
     */
    public function user_logout() {
        global $wpdb;
        $time_logout = User_Login_History_Date_Time_Helper::get_current_date_time();
        $last_id = User_Login_History_Session_Helper::get_last_insert_id();

        if (!$last_id) {
            return;
        }
        $table = User_Login_History_DB_Helper::get_table_name();
        $sql = "update $table  set time_logout='$time_logout', time_last_seen='$time_logout', login_status = '" . self::LOGIN_STATUS_LOGOUT . "' where id = '$last_id' ";
        $wpdb->query($sql);

        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
       unset($_SESSION[USER_LOGIN_HISTORY_NAME]);
    }

    private function set_geo_object() {
         require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-geo-helper.php';
        $this->geo_object = new User_Login_History_Geo_Helper();
    }
    private function set_browser_object() {
         require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-browser-helper.php';
           $this->browser_object = new User_Login_History_Browser_Helper();
    }
    /**
     * Save User Info just after login.
     * In the previous version it was save_user_login().
     *
     * @param string $user_login username
     * @param object $user wp user object
     * @param string $status success, fail or logout
     */
    private function save_login($user_login, $user, $status = '') {

        global $wpdb;
        $table = User_Login_History_DB_Helper::get_table_name();
        $this->set_geo_object();
        $this->set_browser_object();
     
        $current_date = User_Login_History_Date_Time_Helper::get_current_date_time();

        $user_id = isset($user->ID) ? $user->ID : FALSE;

        $old_roles = isset($user->roles) && !empty($user->roles) ? implode(",", $user->roles) : "";
        $geo_location = $this->geo_object->get_geo_location();
        $country_name = isset($geo_location->geoplugin_countryName) ? $geo_location->geoplugin_countryName : "";
        $country_code = isset($geo_location->geoplugin_countryCode) ? $geo_location->geoplugin_countryCode : "";
        $lat = isset($geo_location->geoplugin_latitude) ? $geo_location->geoplugin_latitude : 0;
        $long = isset($geo_location->geoplugin_longitude) ? $geo_location->geoplugin_longitude : 0;

        if ($lat != 0 && $long != 0 && $country_code) {
            $user_timezone = User_Login_History_Date_Time_Helper::get_nearest_timezone($lat, $long, $country_code);
        }
$user_timezone = !empty($user_timezone) ? $user_timezone : "";
        //now insert for new login
        $data = array(
            'user_id' => $user_id,
            'session_token' => $this->get_session_token(),
            'username' => $user_login,
            'time_login' => $current_date,
            'ip_address' => $this->geo_object->get_ip(),
            'time_last_seen' => $current_date,
            'browser' => $this->browser_object->getBrowser(),
            'operating_system' => $this->browser_object->getPlatform(),
            'country_name' => $country_name,
            'country_code' => $country_code,
            'old_role' => $old_roles,
            'timezone' => $user_timezone,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'login_status' => $status,
            'is_super_admin' => is_multisite() ? is_super_admin($user_id) : FALSE,
        );
        //this is used to modify data before saving in db.
        $filtered_data = apply_filters('user_login_history_save_user_login', $data, $lat, $long);

        if (is_array($filtered_data) && !empty($filtered_data)) {
            $data = array_merge($data, $filtered_data);
        }
        $wpdb->insert($table, $data);
        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            return;
        }

        if (self::LOGIN_STATUS_FAIL == $status) {
            return;
        }

        if ($wpdb->insert_id) {
            User_Login_History_Session_Helper::set_last_insert_id($wpdb->insert_id);
            User_Login_History_Session_Helper::set_current_login_blog_id();
        
            add_user_meta($user_id, $this->user_meta_timezone, $user_timezone, true);
        }

        if (is_multisite() && !is_user_member_of_blog($user_id) && !is_super_admin($user_id)) {
            wp_logout();
            wp_die(__('You are not a member of this site. Please contact administrator.', 'user-login-history'));
        }
        do_action('user_login_history_after_save_user_login_detail', $data);
    }

   

    public function set_session_token($logged_in_cookie, $expire, $expiration, $user_id, $logged_in_text, $token) {
        $this->session_token = $token;
    }

    public function get_session_token() {
        return $this->session_token ? $this->session_token : "";
    }

}
