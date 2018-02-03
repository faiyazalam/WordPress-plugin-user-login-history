<?php

/**
 * User_Login_History_DB_Helper
 * 
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 */
class User_Login_History_DB_Helper {
    static public function get_blog_name_by_id($id = NULL) {
        if (!$id) {
            return '';
        }
        global $wpdb;
        return $wpdb->get_var("SELECT CONCAT(domain,path) AS blog_name FROM $wpdb->blogs WHERE blog_id = $id ");
    }

    static public function get_current_user_timezone() {
         global $current_user;
           $timezone = get_user_meta($current_user->ID, USER_LOGIN_HISTORY_USER_META_PREFIX . "user_timezone", TRUE);
           return $timezone && "unknown" != strtolower($timezone) ? $timezone : FALSE; 
    }

}
