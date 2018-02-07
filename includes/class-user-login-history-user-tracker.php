<?php

/**
 * The class that saves user's login detail.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam <support@userloginhistory.com>
 */
class User_Login_History_User_Tracker {

    /**
     * Login Status Constants.
     * 
     */
    const LOGIN_STATUS_LOGIN = 'login';
    const LOGIN_STATUS_FAIL = 'fail';
    const LOGIN_STATUS_LOGOUT = 'logout';

    /**
     * LOGIN_STATUS_BLOCK
     * This will be saved in db if user is not allowed to login on another blog.
     * This is for network enabled mode only.
     */
    const LOGIN_STATUS_BLOCK = 'block';

    /**
     * The unique identifier of this plugin.
     *
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Prefix for usermeta key.
     *
     * @access   protected
     * @var      string    $usermeta_prefix
     */
    protected $usermeta_prefix;

    /**
     * Stores user session token.
     * 
     * @access private
     * @var string $session_token The session token of user.
     */
    private $session_token;

    /**
     * Stores the status of login.
     * 
     * @access private
     * @var string|bool $login_status The login status of user.
     */
    private $login_status = false;

    /**
     * Stores usermeta key for timezone.
     * 
     * @access   private
     * @var      string    $usermeta_key_timezone
     */
    private $usermeta_key_timezone;

    /**
     * Stores instance of geo helper class.
     * 
     * @access   private
     * @var      string    $geo_object
     */
    private $geo_object;

    /**
     * Stores instance of browser helper class.
     * 
     * @access   private
     * @var      string    $browser_object
     */
    private $browser_object;

    /**
     * Initialize the class and set its properties.
     *
     * @var      string    $plugin_name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     * @var      string    $usermeta_prefix    The prefix for usermeta key.
     */
    public function __construct($plugin_name, $version, $usermeta_prefix) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->usermeta_key_timezone = $usermeta_prefix . "user_timezone";
    }

    /**
     * Block user if the user is not allowed to login on another blog.
     * @access private
     */
    private function is_blocked_user_on_this_blog($user_id) {
        if (is_multisite() && !is_user_member_of_blog($user_id) && !is_super_admin($user_id)) {
            $Network_Admin_Setting = new User_Login_History_Network_Admin_Setting();
            if ($Network_Admin_Setting->get_settings('block_user')) {

                $this->login_status = self::LOGIN_STATUS_BLOCK;
                wp_logout();
                wp_die($Network_Admin_Setting->get_settings('block_user_message'));
            }
        }
    }

    /**
     * Set object for Geo Helper.
     * @access private
     */
    private function set_geo_object() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-geo-helper.php';
        $this->geo_object = new User_Login_History_Geo_Helper();
    }

    /**
     * Set object for Browser Helper.
     * @access private
     */
    private function set_browser_object() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-user-login-history-browser-helper.php';
        $this->browser_object = new User_Login_History_Browser_Helper();
    }

    /**
     * Saves user login details.
     * 
     * @access private
     * @param string $user_login username
     * @param object $user WP_User object
     * @param string $status success, fail, logout, block etc.
     */
    private function save_login($user_login, $user, $status = '') {
        global $wpdb;
        $table = $wpdb->get_blog_prefix() . USER_LOGIN_HISTORY_TABLE_NAME;
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

            add_user_meta($user_id, $this->usermeta_key_timezone, $user_timezone, true);
        }
        $this->is_blocked_user_on_this_blog($user_id);
        do_action('user_login_history_after_save_user_login_detail', $data);
    }

    /**
     * Fires if login success.
     * @access  public
     * @param string $user_login username
     * @param object $user wp user object
     */
    public function user_login($user_login, $user) {
        $this->save_login($user_login, $user, self::LOGIN_STATUS_LOGIN);
    }

    /**
     * Update last seen time for the current user.
     * 
     * @access  public
     * @global object $wpdb
     * @return bool|int The number of records updated.
     */
    public function update_time_last_seen() {
        global $wpdb;
        $current_user = wp_get_current_user();
        $table = $wpdb->get_blog_prefix(User_Login_History_Session_Helper::get_current_login_blog_id()) . USER_LOGIN_HISTORY_TABLE_NAME;
        $current_date = User_Login_History_Date_Time_Helper::get_current_date_time();
        $user_id = $current_user->ID;
        $last_id = User_Login_History_Session_Helper::get_last_insert_id();

        if (!$user_id || !$last_id) {
            return;
        }

        $sql = "update $table set time_last_seen='$current_date' where id = '$last_id' and user_id = '$user_id'";

        $status = $wpdb->query($sql);

        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }

        return $status;
    }

    /**
     * Fires if login failed.
     * 
     * @access public
     * @param string $user_login username
     */
    public function user_login_failed($user_login) {
        $this->save_login($user_login, NULL, self::LOGIN_STATUS_FAIL);
    }

    /**
     * Fires on logout.
     * Save logout time of current user.
     * 
     * @access public
     */
    public function user_logout() {
        global $wpdb;
        $time_logout = User_Login_History_Date_Time_Helper::get_current_date_time();
        $last_id = User_Login_History_Session_Helper::get_last_insert_id();
        $login_status = $this->login_status ? $this->login_status : self::LOGIN_STATUS_LOGOUT;

        if (!$last_id) {
            return;
        }
        $table = $wpdb->get_blog_prefix(User_Login_History_Session_Helper::get_current_login_blog_id()) . USER_LOGIN_HISTORY_TABLE_NAME;
        ;
        $sql = "update $table  set time_logout='$time_logout', time_last_seen='$time_logout', login_status = '" . $login_status . "' where id = '$last_id' ";
        $wpdb->query($sql);

        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        User_Login_History_Session_Helper::destroy();
    }

    /**
     * Sets session token.
     * 
     * @param string $logged_in_cookie
     * @param string $expire
     * @param string $expiration
     * @param string|int $user_id
     * @param string $logged_in_text
     * @param string $token The session token.
     */
    public function set_session_token($logged_in_cookie, $expire, $expiration, $user_id, $logged_in_text, $token) {
        $this->session_token = $token;
    }

    /**
     * Gets session token.
     * 
     * @return string The session token.
     */
    public function get_session_token() {
        return $this->session_token ? $this->session_token : "";
    }

}
