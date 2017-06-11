<?php
// configurations
define('ULH_PDIR_PATH', plugin_dir_path(__FILE__));
define('ULH_SERVER_DEFAULT_TIMEZONE', 'UTC');
define('ULH_DATE_DEFAULT_FORMAT', 'Y-m-d H:i:s');
define('ULH_PLUGIN_NAME_FA', 'FA - User Login History');
define('ULH_LISTING_PAGE_ADMIN', 'fa_user_login_history_listing');



//functions
date_default_timezone_set(ULH_SERVER_DEFAULT_TIMEZONE);

function ulh_delete_plugin_options()
{

    delete_option('ulh_settings');  
    delete_option('fa_userloginhostory_version');  
}

function ulh_save_user_login() {


    global $wpdb, $table_prefix, $current_user;
    $fa_user_logins_table = $table_prefix . 'fa_user_logins';
    $ipAddress = ulh_get_visitor_ip();
    $currentDate = ulh_get_current_date_time();
    $Unknown = "Unknown";
    $userId = $current_user->ID;
    if(!$userId)
    {
        return;
    }
    $timeLogin = $currentDate;
    $browser = ulh_get_visitor_browser();
    $operatingSystem = ulh_get_visitor_operating_system();

    $visitorCountryInfo = ulh_get_visitor_country_info();
    $countryName = $visitorCountryInfo->geoplugin_countryName ? $visitorCountryInfo->geoplugin_countryName : $Unknown;
    $countryCode = $visitorCountryInfo->geoplugin_countryCode ? $visitorCountryInfo->geoplugin_countryCode : $Unknown;

    //update for already logged in users who closed their browser without doing logout
    $sqlLoggedIn = "UPDATE $fa_user_logins_table SET time_logout = '$currentDate' WHERE time_logout = '0000-00-00 00:00:00' and user_id = $userId";
    $wpdb->query($sqlLoggedIn);

    //now insert for new login
    $sql = " insert into $fa_user_logins_table(user_id,time_login,ip_address,browser,operating_system,country_name, country_code) values('$userId','$timeLogin','$ipAddress','$browser','$operatingSystem','$countryName', '$countryCode'); ";

    return $wpdb->query($sql);
}

function ulh_get_visitor_ip() {

    $ipaddress = $_SERVER['REMOTE_ADDR'];

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return $ipaddress;
}

function ulh_debugVar($param, $isExit = FALSE) {
    echo '<pre>' . print_r($param, TRUE) . '</pre>';
    if ($isExit) {
        die('Exit');
    }
}


function ulh_get_current_date_time($format = '', $timezone = '') {
   
    $timezone = "" == $timezone?ULH_SERVER_DEFAULT_TIMEZONE:$timezone;
    $format = "" == $format?ULH_DATE_DEFAULT_FORMAT:$format;
   
    date_default_timezone_set($timezone);
    return date($format);
}


function ulh_get_visitor_browser() {

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $browsers = array(
        'Opera' => 'Opera',
        'Firefox' => '(Firebird)|(Firefox)',
        'Galeon' => 'Galeon',
        'Chrome' => 'Chrome',
        'MyIE' => 'MyIE',
        'Lynx' => 'Lynx',
        'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
        'Konqueror' => 'Konqueror',
        'SearchBot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
        'Internet Explorer 8' => '(MSIE 8\.[0-9]+)',
        'Internet Explorer 9' => '(MSIE 9\.[0-9]+)',
        'Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
        'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
        'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
        'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
    );

    foreach ($browsers as $browser => $pattern) {

        if (eregi($pattern, $userAgent)) {
            return $browser;
        }
    }
    return 'Unknown';
}
function ulh_get_visitor_operating_system() {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $os_platform    =   "Unknown";

    $os_array       =   array(
                            '/windows nt 10/i'     =>  'Windows 10',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );

    foreach ($os_array as $regex => $value) { 

        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }

    }   

    return $os_platform;
}

function ulh_get_visitor_country_info($option = FALSE) {
    /*
      {
      "geoplugin_request":"xxxx.xxxx.xxxx.xxxx",
      "geoplugin_status":206,
      "geoplugin_credit":"Some of the returned data includes GeoLite data created by MaxMind, available from <a href='http:\/\/www.maxmind.com'>http:\/\/www.maxmind.com<\/a>.",
      "geoplugin_city":"",
      "geoplugin_region":"",
      "geoplugin_areaCode":"0",
      "geoplugin_dmaCode":"0",
      "geoplugin_countryCode":"IN",
      "geoplugin_countryName":"India",
      "geoplugin_continentCode":"AS",
      "geoplugin_latitude":"20",
      "geoplugin_longitude":"77",
      "geoplugin_regionCode":"",
      "geoplugin_regionName":"",
      "geoplugin_currencyCode":"INR",
      "geoplugin_currencySymbol":"&#8360;",
      "geoplugin_currencySymbol_UTF8":"\u20a8",
      "geoplugin_currencyConverter":66.6555
      }
     */
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }


    $apiUrl = "http://www.geoplugin.net/json.gp?ip=" . $ip;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    $result = curl_exec($ch);
    curl_close($ch);

    $ip_data = json_decode($result);

    return $ip_data;
}

function ulh_get_time_zone_list() {
  $zones_array = array();
  $timestamp = time();
  foreach(timezone_identifiers_list() as $key => $zone) {
    date_default_timezone_set($zone);
    $zones_array[$key]['zone'] = $zone;
    $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
  }
  return $zones_array;
}

function  ulh_convertToUserTime($datetime, $format= '' ,$timezone = '')
{
     
    $format = (""==$format || "0"==$format)?ULH_DATE_DEFAULT_FORMAT:$format;
    $timezone =  ("" == $timezone || "0" == $timezone)?ULH_SERVER_DEFAULT_TIMEZONE:$timezone;
    

    
    $date = new DateTime($datetime, new DateTimeZone(ULH_SERVER_DEFAULT_TIMEZONE));
    $date->setTimezone( new DateTimeZone($timezone));
  
 
  
    return  $date->format($format);
   
}

function ulh_set_flash_message($message = '', $type = 'success')
{
  $_SESSION['ulh_flash_message']['type'] = $type;
  $_SESSION['ulh_flash_message']['message'] = $message;
}


function ulh_get_flash_message()
{
 
 if(isset($_SESSION['ulh_flash_message']))
 {
     $flashMessage = $_SESSION['ulh_flash_message'];
     $message = $flashMessage['message'];
     $type = $flashMessage['type'];
     unset($_SESSION['ulh_flash_message']);
  
     return "<div class='ulh_".$type."'>".$message."</div>";
 }
 return '';
}

function ulh_updateLastSeenTime() {


    global $wpdb, $table_prefix;
     $currentUser = wp_get_current_user();
    $fa_user_logins_table = $table_prefix . 'fa_user_logins';
    $currentDate = ulh_get_current_date_time();
    $userId = $currentUser->ID;
   
    if(!$userId)
    {
        return;
    }
   

    $sql = "UPDATE $fa_user_logins_table SET time_last_seen = '$currentDate' WHERE 1 and user_id = $userId";
   
    return   $wpdb->query($sql);


}