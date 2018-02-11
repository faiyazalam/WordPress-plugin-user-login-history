<?php

/**
 * This is used to detect ip address and geo location.
 *
 * @link       https://github.com/faiyazalam
 * @package    Faulh
 * @subpackage Faulh/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
class Faulh_Geo_Helper {

    private $api_geo_url;

    public function __construct() {

        $this->api_geo_url = 'http://www.geoplugin.net/json.gp?ip=';
        //https://tools.keycdn.com/geo.json?host=
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
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->api_geo_url . $this->get_ip());
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

}
