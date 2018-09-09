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


    static public function insert($table = '', $data = array()) {
        if (!$table || !$data) {
            return FALSE;
        }
        global $wpdb;

        $wpdb->insert($wpdb->prefix . $table, $data);

        if ($wpdb->last_error || !$wpdb->insert_id) {
            ErrorLogHelper::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
            return FALSE;
        }
        return $wpdb->insert_id;
    }
    
    static public function query($sql ='') {
        if (empty($sql)) {
            return FALSE;
        }
        global $wpdb;

        $wpdb->query($sql);

        if ($wpdb->last_error) {
            ErrorLogHelper::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
            return FALSE;
        }
        return $wpdb->insert_id;
    }

    static public function get_results($sql = '', $type = 'ARRAY_A') {
        if (!$sql) {
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

    static public function get_var($sql = '') {
        if (!$sql) {
            return FALSE;
        }
        global $wpdb;

        $result = $wpdb->get_var($sql);

        if ($wpdb->last_error) {
            ErrorLogHelper::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
            return FALSE;
        }
        return $result;
    }
    
    
    
    /**
         * Delete multiple records from a table.
         *
         * @access public
         * @param int $id The record ID
         */
        static public function delete_rows_by_table_and_ids($table='', $ids = array()) {
            if(empty($table) || empty($ids) || !is_array($ids))
            {
                return FALSE;
            }
            global $wpdb;
            $table = $wpdb->prefix . $table;
    
              
                $ids = implode(',', array_map('absint', $ids));
                $status = $wpdb->query("DELETE FROM $table WHERE id IN($ids)");
                
                if ($wpdb->last_error) {
                    ErrorLogHelper::error_log($wpdb->last_error . " " . $wpdb->last_query);
                }
                return $status;
            
        }
        static public function truncate_table($table='') {
            if(empty($table))
            {
                return FALSE;
            }
            global $wpdb;
            $table = $wpdb->prefix . $table;
                $status = $wpdb->query("TRUNCATE $table");
                
                if ($wpdb->last_error) {
                    ErrorLogHelper::error_log($wpdb->last_error . " " . $wpdb->last_query);
                }
                return $status;
            
        }
        
         static public function get_col($sql = '') {
         
           if(empty($sql))
           {
               return;
           }
            
            global $wpdb;

            $result = $wpdb->get_col($sql);
            
            if ($wpdb->last_error) {
                ErrorLogHelper::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            }
            
            return $result;
        }
         static public function get_blog_ids_by_site_id($site_id = NULL) {
         
            if (is_null($site_id)) {
                $site_id = get_current_network_id();
            }
            
            if(!is_numeric($site_id))
            {
                return FALSE;
            }
            return self::get_col("SELECT blog_id FROM $wpdb->blogs WHERE `site_id` = ". absint($site_id)." ORDER BY blog_id ASC");
            
        }

        
        static public function dbDelta($sql = '') {
            if(empty($sql))
            {
                return FALSE;
            }
            global $wpdb;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            
           dbDelta($sql);
        if (!empty($wpdb->last_error)) {
            ErrorLogHelper::error_log("Error while creating or updatiing tables-" . $wpdb->last_error, __LINE__, __FILE__);
            wp_die($wpdb->last_error);
        }
        }
}
