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

    public function __construct($format = '', $timezone = '') {
        $this->default_date_time_format = $format ? $format : 'Y-m-d H:i:s';
        $this->default_timezone = $timezone ? $timezone : 'UTC';
        date_default_timezone_set($this->timezone);
    }

    public function get_default_timezone() {
        return $this->default_timezone;
    }

    public function get_current_date_time($format = "") {
        $format = $format ? $format : $this->default_date_time_format;
        return date($format);
    }

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

    public function human_time_diff_from_now($time, $timezone) {
        $time = $this->convert_to_user_timezone($time, '', $timezone);
        $current_date_time = $this->convert_to_user_timezone($this->get_current_date_time(), '', $timezone);
        return "<span title = '$time'>" . human_time_diff(strtotime($time), strtotime($current_date_time)) . ' ago</span>';
    }

}
