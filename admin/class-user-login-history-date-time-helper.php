<?php
/**
 * User_Login_History_Date_Time_Helper
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
class User_Login_History_Date_Time_Helper {

    private $default_date_time_format;
    private $default_timezone;
    
 /**
     * Initialize the class and set its properties.
     *
     * @since    1.4.1
     * @var      string    $format       
     * @var      string    $timezone   
     */
    public function __construct($format = '', $timezone = '') {
        $this->default_date_time_format = $format ? $format : 'Y-m-d H:i:s';
        $this->default_timezone = $timezone ? $timezone : 'UTC';
        date_default_timezone_set($this->default_timezone);
    }


    /**
     * get default timezone

     * @since    1.4.1
     * @return string timezone
     */
    public function get_default_timezone() {
        return $this->default_timezone;
    }

        /**
     * get current date time
     *
     * @since    1.4.1
     * @var      string    $format   format the output datetime string 
     * @return string datetime
     */
    public function get_current_date_time($format = "") {
        $format = $format ? $format : $this->default_date_time_format;
        return date($format);
    }

    
            /**
     * get list of all timezones
     *
     * @since    1.4.1
     * @return array list of all timezones
     */
            static public function get_timezone_list() {
        $zones_array = array();
        $timestamp = time();
        foreach (timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $zones_array[$key]['zone'] = $zone;
            $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
        }
        return $zones_array;
    }

    
     /**
     * Convert datetime into user timezone.
     *
     * @since    1.4.1
     * @param string $datetime input datetime to be converted.
     * @param string $preferred_format datetime format in which the datetime is to be converted.
     * @param string $preferred_timezone timezone in which the datetime is to be converted.
     */
    
    public function convert_to_user_timezone($datetime = "", $preferred_format = '', $preferred_timezone = '') {
        if ("" == $datetime) {
            return FALSE;
        }

        $preferred_format = $preferred_format ? $preferred_format : $this->default_date_time_format;
        $preferred_timezone = $preferred_timezone ? $preferred_timezone : $this->default_timezone;
        $date = new DateTime($datetime, new DateTimeZone($this->default_timezone));
        $date->setTimezone(new DateTimeZone($preferred_timezone));
        return $date->format($preferred_format);
    }

    /**
     * Find time difference in words.
     * 
     * @param string $datetime 
     * @param string $timezone timezone
     * @return string time difference in words. e.g. 2 minutes ago.
     */
    public function human_time_diff_from_now($datetime, $timezone) {
        $datetime = $this->convert_to_user_timezone($datetime, '', $timezone);
        $current_date_time = $this->convert_to_user_timezone($this->get_current_date_time(), '', $timezone);
        return "<span title = '$datetime'>" . human_time_diff(strtotime($datetime), strtotime($current_date_time)) . ' ago</span>';
    }

}
