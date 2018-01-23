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
     * Constants
     */
    const LOGIN_STATUS_SUCCESS = 'success';
    const LOGIN_STATUS_FAIL = 'fail';
    const LOGIN_STATUS_LOGOUT = 'logout';
    static $instance;
    /**
     * The name of this plugin.
     *
     * @access   private
     * @var      string    $name    The name of this plugin.
     */
	private $plugin_name;
	private $session_token;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $plugin_table_name;
	private $plugin_table_name_with_prefix;
	private $plugin_option_prefix;



    /**
     * Holds key name for latest entry id for current user.
     *
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $last_insert_id_key;

    /**
     * The blog id on which user is logged-in.
     *
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $current_login_blog_id_key;

    /**
     * table name used to save current user info like ip, browser etc..
     *
     * @access   private
     * @var      string    $version    the main table of the plugin.
     */
    private $table;

    /**
     * user meta key to hold timezone of current user.
     * 
     * @access   private
     * @var      string    $user_meta_timezone    The timezone of current user.
     */
    private $user_meta_timezone;

   

    /**
     * Initialize the class and set its properties.
     *
     * @var      string    $name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version, $plugin_table_name, $plugin_option_prefix) {
       

        $this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_table_name = $plugin_table_name;
		$this->plugin_option_prefix = $plugin_option_prefix;
                
        $this->last_insert_id_key = 'last_insert_id';
        $this->current_login_blog_id_key = 'current_login_blog_id';
        $this->user_meta_timezone = $this->plugin_option_prefix."user_timezone";
        
        $this->set_plugin_table_name_with_prefix();
    }
    
    

    public function set_plugin_table_name_with_prefix() {
        if(is_multisite()){
             global $wpdb;
        $wpdb->get_blog_prefix($this->get_session_current_login_blog_id()) . $this->plugin_table_name;
        }  else {
            global $table_prefix;
        $this->plugin_table_name_with_prefix = $table_prefix.$this->plugin_table_name;
        }
    }


    public function get_plugin_table_name_with_prefix() {
        return   $this->plugin_table_name_with_prefix;
    } 
            
            
           
    /**
     * Get IP Address of user.
     *
     * @return string
     */
    static public function get_ip() {
        $ip_address = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $ip_address;
    }


    /**
     * Get geo location.
     *
     * @return string
     */
    static public function get_geo_location() {
        $apiUrl = "http://www.geoplugin.net/json.gp?ip=" . self::get_ip();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    /**
     * Set last insert id in the plugin session.
     *
     * @param int $id current lattitude
     */
    private function set_session_last_insert_id($id = NULL) {
        $_SESSION[$this->name][$this->last_insert_id_key] = $id ? $id : FALSE;
    }

    /**
     * Set the blog id on which user is logged-in.
     *
     * @param int $id current lattitude
     */
    private function set_session_current_login_blog_id($id = NULL) {
        $_SESSION[$this->name][$this->current_login_blog_id_key] = $id ? $id : get_current_blog_id();
    }

    /**
     * Get the blog id on which user is logged-in.
     *
     * @param int $id current lattitude
     */
    private function get_session_current_login_blog_id() {
        return isset($_SESSION[$this->name][$this->current_login_blog_id_key]) ? $_SESSION[$this->name][$this->current_login_blog_id_key] : FALSE;
    }

    /**
     * Get last insert id from the plugin session.
     *
     */
    private function get_session_last_insert_id() {
        return isset($_SESSION[$this->name][$this->last_insert_id_key]) ? $_SESSION[$this->name][$this->last_insert_id_key] : FALSE;
    }

    /**
     * Fire if login success
     *
     * @param string $user_login username
     * @param object $user wp user object
     */
    public function user_login($user_login, $user) {
        $this->save_login($user_login, $user, self::LOGIN_STATUS_SUCCESS);
    }

    /**
     * Update last seen time for current user.
     *
     */
    public function update_time_last_seen() {
        
        global $wpdb;
        $current_user = wp_get_current_user();
        $table = $this->get_plugin_table_name_with_prefix();
        $current_date = User_Login_History_Date_Time_Helper::get_current_date_time();
        $user_id = $current_user->ID;
        
        $last_id = $this->get_session_last_insert_id() ;
        if (!$user_id || !$last_id) {
            return;
        }
        $sql = "update $this->plugin_table_name_with_prefix set time_last_seen='$current_date' where id = '$last_id' and user_id = '$user_id'";

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
        $last_id = $this->get_session_last_insert_id();

        if (!$last_id) {
            return;
        }

        $sql = "update $this->plugin_table_name_with_prefix  set time_logout='$time_logout', time_last_seen='$time_logout', login_status = '".self::LOGIN_STATUS_LOGOUT."' where id = '$last_id' ";
        $wpdb->query($sql);

        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }

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
        $current_date = User_Login_History_Date_Time_Helper::get_current_date_time();
        
        $user_id = isset($user->ID) ? $user->ID : FALSE;
        
        $old_roles = isset($user->roles) && !empty($user->roles) ? implode(",", $user->roles) : "";
        $geo_location = $this->get_geo_location();
        $country_name = isset($geo_location->geoplugin_countryName) ? $geo_location->geoplugin_countryName : "";
        $country_code = isset($geo_location->geoplugin_countryCode) ? $geo_location->geoplugin_countryCode : "";
        $lat = isset($geo_location->geoplugin_latitude) ? $geo_location->geoplugin_latitude : 0;
        $long = isset($geo_location->geoplugin_longitude) ? $geo_location->geoplugin_longitude : 0;

        if ($lat != 0 && $long != 0 && $country_code) {
            $user_timezone = User_Login_History_Date_Time_Helper::get_nearest_timezone($lat, $long, $country_code);
        }

        //now insert for new login
        $data = array(
            'user_id' => $user_id,
            'session_token' => $this->getSessionToken(),
            'username' => $user_login,
            'time_login' => $current_date,
            'ip_address' => self::get_ip(),
            'time_last_seen' => $current_date,
            'browser' => '',
            'operating_system' => 'get_operating_system',
            'country_name' => $country_name,
            'country_code' => $country_code,
            'old_role' => $old_roles,
            'timezone' => !empty($user_timezone)?$user_timezone:"",
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'login_status' => $status,
            'is_super_admin' => is_multisite() ? is_super_admin($user_id) : FALSE,
        );
        //this is used to modify data before saving in db.
        $filtered_data = apply_filters('user_login_history_save_user_login', $data, $lat, $long);

        if (is_array($filtered_data) && !empty($filtered_data)) {
            $data = array_merge($data, $filtered_data);
        }
        $wpdb->insert($this->plugin_table_name_with_prefix, $data);

        if ($wpdb->last_error) {
         
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            return;
        }

        if (self::LOGIN_STATUS_FAIL == $status) {
            return;
        }

        if ($wpdb->insert_id) {
            $this->set_session_last_insert_id($wpdb->insert_id);
            $this->get_session_current_login_blog_id();
            add_user_meta($user_id, $this->user_meta_timezone, $user_timezone, true);
        }

        if (is_multisite() && !is_user_member_of_blog($user_id) && !is_super_admin($user_id)) {
            wp_logout();
            wp_die(__('You are not a member of this site. Please contact administrator.', 'user-login-history'));
        }
        //this cab be used to send email.
        do_action('user_login_history_after_save_user_login_detail', $data);
    }

 public static function get_instance($plugin_name, $version, $plugin_table_name, $plugin_option_prefix) {
        if (!isset(self::$instance)) {
            self::$instance = new self($plugin_name, $version, $plugin_table_name, $plugin_option_prefix);
        }
        return self::$instance;
    }


    public function setSessionToken($token = '') {
        $this->session_token = $token;
    }
    
    public function getSessionToken() {
        return  $this->session_token ? $this->session_token : "";
    }
}