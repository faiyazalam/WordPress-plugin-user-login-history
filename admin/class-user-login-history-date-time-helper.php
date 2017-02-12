<?php
/**
 * Class User_Login_History_Date_Time_Helper
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

    /**
     * The default date-time format.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $default_date_time_format
     */
    private $default_date_time_format;

    /**
     * The default timezone.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $default_timezone
     */
    private $default_timezone;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.4.1
     * @var      string    $format       date-time format, default is Y-m-d H:i:s.
     * @var      string    $timezone    timezone, default is UTC.
     */
    public function __construct($format = '', $timezone = '') {
        $this->default_date_time_format = $format ? $format : 'Y-m-d H:i:s';
        $this->default_timezone = $timezone ? $timezone : 'UTC';
        date_default_timezone_set($this->default_timezone);
    }

    /**
     * Get default timezone.
     *
     * @since    1.4.1
     * @return string
     */
    public function get_default_timezone() {
        return $this->default_timezone;
    }

    /**
     * Get current date-time
     * 
     * @var      string    $format       date-time format, default is Y-m-d H:i:s.
     * @since    1.4.1
     * @return string
     */
    public function get_current_date_time($format = "") {
        $format = $format ? $format : $this->default_date_time_format;
        return date($format);
    }

    /**
     * Get timezone list.
     * 
     * @since    1.4.1
     * @return array the list of all the timezones.
     */
    public function get_timezone_list() {
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
     * @param string $datetime a required input datetime to be converted.
     * @param string $preferred_format datetime format in which the datetime is to be converted,  default is Y-m-d H:i:s.
     * @param string $preferred_timezone timezone in which the datetime is to be converted, default is UTC.
     * @return string|bool if input datetime is given, it returns the converted datetime, otherwise false.
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
     * Converts the datetime into user timezone and then determines the difference between this datetime and current datetime.
     *
     * The difference is returned in a human readable format such as "1 hour ago",
     * "5 mins ago", "2 days ago" in span element.
     *
     * @since 1.4.1
     *
     * @param string $time date-time from which the difference begins.
     * @param string $timezone   timezone in which the datetime is to be converted, default is UTC.
     * @return string Human readable time difference.
     */
    public function human_time_diff_from_now($time, $timezone) {
        $time = $this->convert_to_user_timezone($time, '', $timezone);
        $current_date_time = $this->convert_to_user_timezone($this->get_current_date_time(), '', $timezone);
        return "<span title = '$time'>" . human_time_diff(strtotime($time), strtotime($current_date_time)) . ' ago</span>';
    }

}
