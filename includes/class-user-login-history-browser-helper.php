<?php

/**
 * User_Login_History_Date_Time_Helper
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 */
  require_once plugin_dir_path(dirname(__FILE__)) . 'vendors/Browser/lib/Browser.php';
class User_Login_History_Browser_Helper extends Browser {
    private $api_geo_url;
    
     public function __construct($userAgent = "")
    {
         parent::__construct();
         $this->api_geo_url = 'http://www.geoplugin.net/json.gp?ip=';
    }
    
    
    /**
     * Get IP Address of user.
     *
     * @return string
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
     * Get geo location.
     *
     * @return string
     */
    public function get_geo_location() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->api_geo_url.$this->get_ip());
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }
}
