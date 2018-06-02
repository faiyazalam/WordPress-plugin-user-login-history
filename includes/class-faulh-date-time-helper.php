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
if (!class_exists('Faulh_Date_Time_Helper')) {

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
            $format = empty($format) ? get_option('date_format') . " " . get_option('time_format') : $format;
            return mysql2date($format, $date);
        }

        /**
         * This is used to get the current date to be saved in DB.
         * @return string The current date.
         */
        static public function get_current_date_time($format = '', $timezone = '') {
            date_default_timezone_set($timezone ? $timezone : self::DEFAULT_TIMEZONE);
            return date($format ? $format : self::DEFAULT_FORMAT);
        }

        /**
         * Retrieves the list of all the timezones.
         *
         * @return array The array containing all the timezones.
         */
        static public function get_timezone_list() {
            $current_default_timezone = date_default_timezone_get();
            $zones_array = array();
            $timestamp = time();
            foreach (timezone_identifiers_list() as $key => $zone) {
                date_default_timezone_set($zone);
                $zones_array[$key]['zone'] = $zone;
                $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
            }
            //reset the timezone for the script
            date_default_timezone_set($current_default_timezone ? $current_default_timezone : self::DEFAULT_TIMEZONE);
            return $zones_array;
        }

        /**
         * Converts datetime into a different timezone and format.
         * 
         * @param type $input_datetime
         * @param type $input_timezone default is UTC
         * @param type $output_timezone default is UTC
         * @return string If success, returns converted datetime in default format i.e. Y-m-d H:i:s, otherwise false.
         */
        static public function convert_timezone($input_datetime = "", $input_timezone = "", $output_timezone = "") {
            if (empty($input_datetime) || !(strtotime($input_datetime) > 0)) {
                return FALSE;
            }
            //timezone is compared with 'unknown' for backward compatibility.
            $input_timezone = !empty($input_timezone) && "unknown" != strtolower($input_timezone) ? $input_timezone : self::DEFAULT_TIMEZONE;
            $output_timezone = !empty($output_timezone) && "unknown" != strtolower($output_timezone) ? $output_timezone : self::DEFAULT_TIMEZONE;
           
            try {
            $date = new DateTime($input_datetime, new DateTimeZone($input_timezone));
            $date->setTimezone(new DateTimeZone($output_timezone));
            return $date->format(self::DEFAULT_FORMAT);
            } catch (Exception $exc) {
               Faulh_Error_Handler::error_log("Error while converting timezone. Error message: ".$exc->getMessage().", input timezone: $input_timezone, output_timezone: $output_timezone input_datetime: $input_datetime", __LINE__, __FILE__);
               return FALSE;
            }

           
        }


    }

}
