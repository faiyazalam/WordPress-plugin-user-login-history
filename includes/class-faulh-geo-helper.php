<?php

/**
 * This is used to detect ip address and geo location.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if (!class_exists('Faulh_Geo_Helper')) {

    class Faulh_Geo_Helper {

        /**
         * URL of the thirst party service.
         */
        const GEO_API_URL = 'https://tools.keycdn.com/geo.json?host=';

        /**
         * The unique identifier of this plugin.
         *
         * @access   private
         * @var      string    $plugin_name    The string used to uniquely identify this plugin.
         */
        private $plugin_name;

        /**
         * Initialize the class and set its properties.
         *
         * @var      string    $plugin_name       The name of this plugin.
         */
        public function __construct($plugin_name) {
            $this->plugin_name = $plugin_name;
        }

        /**
         * Retrieve IP Address of user.
         *
         * @return string The IP address of user.
         */
        public function get_ip() {
            
            $ip_address = $_SERVER['REMOTE_ADDR'];

            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip_address = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            return $ip_address;
        }

        /**
         * Retrieve the geo location.
         *
         * @return string The response from geo API.
         */
        public function get_geo_location() {
            $options = get_option($this->plugin_name . "_advanced");
            if (empty($options['is_geo_tracker_enabled']) || 'on' != $options['is_geo_tracker_enabled']) {
                return FALSE;
            }

            $defaults = array('timeout' => 5);
            $args = apply_filters('faulh_remote_get_args', $defaults);
            $r = wp_parse_args($args, $defaults);
            $response = wp_remote_get(self::GEO_API_URL . $this->get_ip(), $r);
            if (is_wp_error($response)) {
                Faulh_Error_Handler::error_log("error while calling wp_remote_get method:" . $response->get_error_message(), __LINE__, __FILE__);
                return FALSE;
            }

            if (is_array($response)) {
                $decoded = json_decode($response['body']);
                if (isset($decoded->data->geo)) {
                    return (array) $decoded->data->geo;
                }
            }
            return FALSE;
        }

    }

}

