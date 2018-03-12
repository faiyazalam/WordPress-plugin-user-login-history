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
if(!class_exists('Faulh_Geo_Helper'))
{
    class Faulh_Geo_Helper {
        
    const GEO_API_URL = 'http://www.geoplugin.net/json.gp?ip=';
    
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
      //  return '163.47.142.27';
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
        $options = get_option($this->plugin_name."_advance");
    if(empty($options['is_geo_tracker_enabled']) || 'on' != $options['is_geo_tracker_enabled'])
    {
        return FALSE;
    }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, self::GEO_API_URL . $this->get_ip());
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

}
}

