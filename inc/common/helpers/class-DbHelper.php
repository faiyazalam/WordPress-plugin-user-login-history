<?php

namespace User_Login_History\Inc\Common\Helpers;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 * @since      1.0.0
 *
 * @author    Er Faiyaz Alam
 */

class DbHelper {
        
    static public function insert($table ='', $data = array()) {
        if(!$table || !$data)
        {
            return FALSE;
        }
          global $wpdb;
          
          $wpdb->insert($wpdb->prefix.$table, $data);

            if ($wpdb->last_error || !$wpdb->insert_id) {
                ErrorLogHelper::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
                return FALSE;
            }
            return $wpdb->insert_id;

        
    }       
    
    static public function get_results($sql ='', $type = 'ARRAY_A') {
        if(!$sql)
        {
            return FALSE;
        }
          global $wpdb;
          
       $results = $wpdb->get_results($sql, $type);

            if ($wpdb->last_error) {
                ErrorLogHelper::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
                return FALSE;
            }
           
            return $results;

        
    }      
    
    static public function get_var($sql ='') {
        if(!$sql)
        {
            return FALSE;
        }
          global $wpdb;
          
       $result = $wpdb->get_var($sql, $type);

            if ($wpdb->last_error) {
                ErrorLogHelper::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
                return FALSE;
            }
            return $result;

        
    }        
        
       
}
