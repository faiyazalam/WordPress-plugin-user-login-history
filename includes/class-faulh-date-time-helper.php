<?php

/**
 * Faulh_Date_Time_Helper
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if(!class_exists('Faulh_Date_Time_Helper'))
{
    class Faulh_Date_Time_Helper {

    /**
     * Defalult timizone.
     */
    const DEFAULT_TIMEZONE = 'UTC';

    /**
     * Defalult date format.
     */
    const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    //this is used to show on html
    /**
     * Converts date format.
     * It is used to print date on html.
     * 
     * @param string $date The date with database format i.e. Y-m-d H:i:s
     * @param string $format The date format in which the date is to be converted.
     * @return string|int|bool Formatted date string or Unix timestamp. False if $date is empty.
     */
 static public function convert_format($date, $format = "") {
        $format = empty($format) ? get_option('date_format')." ".get_option('time_format') : $format;
        return mysql2date($format, $date);
    }

    /**
     * This is used to get the current date to be saved in DB.
     * @return string The current date.
     */
    static public function get_current_date_time() {
        return date(self::DEFAULT_FORMAT);
    }

    /**
     * Retrieves the list of all the timezones.
     *
     * @return array The array containing all the timezones.
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
     * Converts datetime into a different timezone and format.
     * 
     * @param type $input_datetime
     * @param type $input_timezone default is UTC
     * @param type $output_timezone default is UTC
     * @return string returns converted datetime in default format i.e. Y-m-d H:i:s.
     */
    static public function convert_timezone($input_datetime = "", $input_timezone = "", $output_timezone = "") {
        if (!$input_datetime) {
            return FALSE;
        }
        $input_timezone = $input_timezone ? $input_timezone : self::DEFAULT_TIMEZONE;
        $output_timezone = $output_timezone ? $output_timezone : self::DEFAULT_TIMEZONE;
        $date = new DateTime($input_datetime, new DateTimeZone($input_timezone));
        $date->setTimezone(new DateTimeZone($output_timezone));
        return $date->format(self::DEFAULT_FORMAT); // do not convert format here, use get_convert_date_time().
    }

    /**
     * Compares the two input date-time and returns the latest one.
     * @param string $time_one
     * @param string $time_two
     * @return string The latest input date-time.
     */
    static public function get_last_time($time_one, $time_two) {
        $time_one_str = strtotime($time_one);
        $time_two_str = strtotime($time_two);
        return $time_one_str > $time_two_str ? $time_one : $time_two;
    }

}
}
