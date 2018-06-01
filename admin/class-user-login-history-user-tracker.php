<?php

/**
 * class User_Login_History_User_Tracker {

 * This class is used to track user based on different attributes e.g. ip, browser etc.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 */
?>
<?php

class User_Login_History_User_Tracker {

    /**
     * The name of this plugin.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $name    The name of this plugin.
     */
    private $name;

    /**
     * The version of this plugin.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Holds key name for latest entry id for current user.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $last_insert_id_key;

    /**
     * table name used to save current user info like ip, browser etc..
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $version    the main table of the plugin.
     */
    private $table;

    /**
     * user meta key to hold timezone of current user.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $user_meta_timezone    The timezone of current user.
     */
    private $user_meta_timezone;

    /**
     * The option prefix of this plugin.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $version   To be used with options like user meta key, key for option table etc. for the plugin.
     */
    private $option_prefix;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.4.1
     * @var      string    $name       The name of this plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($name, $version, $option_prefix) {
        global $table_prefix;
        $this->name = $name;
        $this->version = $version;
        $this->table = $table_prefix . ULH_TABLE_NAME;
        $this->last_insert_id_key = 'last_insert_id_key';
        $this->option_prefix = $option_prefix;
        $this->user_meta_timezone = $this->option_prefix . "user_timezone";
    }

    /**
     * Get IP Address of user.
     *
     * @since    1.3
     * @return string
     */
    private function get_ip() {

        $ip_address = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }


        return $ip_address;
    }

    /**
     * Get browser name.
     *
     * @since    1.3
     * @return string
     */
    private function get_browser() {

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browsers = array(
            'Opera' => 'Opera',
            'Firefox' => '(Firebird)|(Firefox)',
            'Galeon' => 'Galeon',
            'Edge' => 'Edge',
            'Chrome' => 'Chrome',
            'Safari' => 'Safari',
            'MyIE' => 'MyIE',
            'Lynx' => 'Lynx',
            'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
            'Konqueror' => 'Konqueror',
            'SearchBot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
            'Internet Explorer 8' => '(MSIE 8\.[0-9]+)',
            'Internet Explorer 9' => '(MSIE 9\.[0-9]+)',
            'Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
            'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
            'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
            'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
        );

        foreach ($browsers as $browser => $pattern) {

            if (preg_match("@$pattern@i", $user_agent)) {
                return $browser;
            }
        }
        return 'Unknown';
    }

    /**
     * Get operating system name.
     *
     * @since    1.3
     * @return string
     */
    private function get_operating_system() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $os_array = array(
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
            '/cros/i' => 'Chrome'
        );

        foreach ($os_array as $regex => $os) {

            if (preg_match($regex, $user_agent)) {
                return $os;
            }
        }

        return 'Unknown';
    }

    /**
     * Get geo location.
     *
     * @since    1.3
     * @return string
     */
    private function get_geo_location() {

        $apiUrl = "http://www.geoplugin.net/json.gp?ip=" . $this->get_ip();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    /**
     * Get nearest timezone of the user.
     *
     * @since    1.4.1
     * @param string $cur_lat current lattitude
     * @param string $cur_long current longitude
     * @param string $country_code country code
     * @return boolean|string
     */
    private function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
        $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code) : DateTimeZone::listIdentifiers();

        if ($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

            $time_zone = '';
            $tz_distance = 0;

            //only one identifier?
            if (count($timezone_ids) == 1) {
                $time_zone = $timezone_ids[0];
            } else {

                foreach ($timezone_ids as $timezone_id) {
                    $timezone = new DateTimeZone($timezone_id);
                    $location = $timezone->getLocation();
                    $tz_lat = $location['latitude'];
                    $tz_long = $location['longitude'];

                    $theta = $cur_long - $tz_long;
                    $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat))) + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                    $distance = acos($distance);
                    $distance = abs(rad2deg($distance));

                    if (!$time_zone || $tz_distance > $distance) {
                        $time_zone = $timezone_id;
                        $tz_distance = $distance;
                    }
                }
            }
            return $time_zone;
        }
        return FALSE;
    }

    /**
     * Set last insert id in the plugin session.
     *
     * @since    1.4.1
     * @param int $id current lattitude
     */
    private function set_last_insert_id($id = NULL) {
        $_SESSION[$this->name][$this->last_insert_id_key] = $id ? $id : FALSE;
    }

    /**
     * Start the session if it is not started already.
     *
     * @since    1.4.1
     */
    public function do_session_start() {
        if ("" == session_id()) {
            session_start();
        }
    }

    /**
     * Get last insert id from the plugin session.
     *
     * @since    1.4.1
     */
    private function get_last_insert_id() {
        return isset($_SESSION[$this->name][$this->last_insert_id_key]) ? $_SESSION[$this->name][$this->last_insert_id_key] : FALSE;
    }

    /**
     * Get current date time.
     *
     * @since    1.4.1
     */
    private function get_current_date_time() {
        $Date_Time_Helper = new User_Login_History_Date_Time_Helper();
        return $Date_Time_Helper->get_current_date_time();
    }

    /**
     * Save User Info just after login.
     *
     * @since    1.3
     * @param string $user_login username
     * @param object $user wp user object
     */
    public function save_user_login($user_login, $user) {
        global $wpdb;

        $user_id = $user->ID;

        if (!$user_id) {
            return;
        }
        $Date_Time_Helper = new User_Login_History_Date_Time_Helper();
        $old_roles = implode(",", $user->roles);
        $table = $this->table;
        $ip_address = $this->get_ip();
        $unknown = "Unknown";
        $user_timezone = FALSE;

        $current_date = $this->get_current_date_time();
        $time_login = $current_date;
        $time_last_seen = $current_date;
        $browser = $this->get_browser();
        $operating_system = $this->get_operating_system();

        $geo_location = $this->get_geo_location();
        $country_name = isset($geo_location->geoplugin_countryName) ? $geo_location->geoplugin_countryName : $unknown;
        $country_code = isset($geo_location->geoplugin_countryCode) ? $geo_location->geoplugin_countryCode : $unknown;
        $lat = isset($geo_location->geoplugin_latitude) ? $geo_location->geoplugin_latitude : 0;
        $long = isset($geo_location->geoplugin_longitude )? $geo_location->geoplugin_longitude : 0;

        if ($lat != 0 && $long != 0 && $country_code != $unknown) {
            $user_timezone = $this->get_nearest_timezone($lat, $long, $country_code);
        }

        if (FALSE === $user_timezone) {
            $user_timezone = $unknown;
        }

        //now insert for new login
        $data = array(
            'user_id' => $user_id,
            'username' => $user_login,
            'time_login' => $time_login,
            'ip_address' => $ip_address,
            'time_last_seen' => $time_last_seen,
            'browser' => $browser,
            'operating_system' => $operating_system,
            'country_name' => $country_name,
            'country_code' => $country_code,
            'old_role' => $old_roles,
            'timezone' => $user_timezone,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        );
        
        //this is used to modify data before saving in db.
        $filtered_data = apply_filters('user_login_history_save_user_login', $data, $lat, $long);
        
        if(is_array($filtered_data) && !empty($filtered_data))
        {
            $data = array_merge($data, $filtered_data);
        }
        
        $wpdb->insert($table, $data);
        
        if ("" != $wpdb->last_error) {
           User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error. " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            return;
        }

        //save user time zone in user_meta table
        if ($wpdb->insert_id) {
            $this->set_last_insert_id($wpdb->insert_id);
            $this->set_current_user_timezone($user_timezone);
        }
         //this cab be used to send email.
        do_action('user_login_history_save_user_login_action', $data);
    }

    /**
     * Save logout time
     *
     * @since    1.3
     */
    public function save_user_logout() {
        global $wpdb, $current_user;
        $time_logout = $this->get_current_date_time();
        $user_id = $current_user->ID;
        $table = $this->table;
        $last_id = $this->get_last_insert_id();

        if (!$user_id || !$last_id) {
            return;
        }
        $sql = " update $table  set time_logout='$time_logout', time_last_seen='$time_logout' where id=$last_id ";
        $wpdb->query($sql);
        
         if ("" != $wpdb->last_error) {
          User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error. " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        
        session_destroy();
    }

    /**
     * Save timezone for current user
     *
     *  @since    1.4.1
     * @global object $current_user
     * @param string $user_timezone
     * @return int|false
     */
    public function set_current_user_timezone($user_timezone = '') {
        global $current_user;
        $user_id = $current_user->ID;
        return add_user_meta($user_id, $this->user_meta_timezone, $user_timezone, TRUE);
    }

    /**
     * Get timezone for current user
     * @since    1.4.1
     *
     * @return mixed
     */
    public function get_current_user_timezone() {
        global $current_user;
        $user_id = $current_user->ID;
        return get_user_meta($user_id, $this->user_meta_timezone, TRUE);
    }

    /**
     * Update last seen time for current user.
     *
     *  @since    1.3
     */
    public function update_time_last_seen() {
        global $wpdb;
        $current_user = wp_get_current_user();
        $table = $this->table;
        $current_date = $this->get_current_date_time();
        $user_id = $current_user->ID;
        $last_id = $this->get_last_insert_id();
        if (!$user_id || !$last_id) {
            return;
        }
        $sql = " update $table set time_last_seen='$current_date' where id=$last_id ";
        $wpdb->query($sql);
           if ("" != $wpdb->last_error) {
          User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error. " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
    }

    /**
     * Create listing table on backend.
     *
     *  @since   1.4.1
     */
    public function create_table() {
    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-table-page.php';
    User_Login_History_Table_Page::get_instance();
    }

}
