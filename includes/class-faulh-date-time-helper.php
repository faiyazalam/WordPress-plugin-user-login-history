<?php

/**
 * Faulh_Date_Time_Helper
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    Faulh
 * @subpackage Faulh/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
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
        $format = $format ? $format : self::DEFAULT_FORMAT;
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
     * Gets nearest time zone of the user.
     *
     * @param string $cur_lat current lattitude
     * @param string $cur_long current longitude
     * @param string $country_code country code
     * @return boolean|string
     */
    static public function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
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