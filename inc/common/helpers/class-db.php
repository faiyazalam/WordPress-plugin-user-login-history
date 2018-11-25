<?php

namespace User_Login_History\Inc\Common\Helpers;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 *
 * @author    Er Faiyaz Alam
 */
class Db {

    static public function insert($table = '', $data = array()) {
        if (!$table || !$data) {
            return FALSE;
        }
        global $wpdb;

        $wpdb->insert($wpdb->prefix . $table, $data);

        if ($wpdb->last_error || !$wpdb->insert_id) {
            Error_Log::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
            return FALSE;
        }
        return $wpdb->insert_id;
    }

    static public function query($sql = '') {
        if (empty($sql)) {
            return FALSE;
        }
        global $wpdb;

        $wpdb->query($sql);

        if ($wpdb->last_error) {
            Error_Log::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
            return FALSE;
        }
        return !empty($wpdb->insert_id) ? $wpdb->insert_id : TRUE;
    }

    static public function get_results($sql = '', $type = 'ARRAY_A') {
        if (!$sql) {
            return FALSE;
        }
        global $wpdb;

        $results = $wpdb->get_results($sql, $type);

        if ($wpdb->last_error) {
            Error_Log::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
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
            Error_Log::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query);
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
    static public function delete_rows_by_table_and_ids($table = '', $ids) {
        if (empty($table) || empty($ids)) {
            return FALSE;
        }
        global $wpdb;
        $table = $wpdb->prefix . $table;


        $ids = is_array($ids) ? implode(',', array_map('absint', $ids)) : $ids;
        $status = $wpdb->query("DELETE FROM $table WHERE id IN($ids)");

        if ($wpdb->last_error) {
            Error_Log::error_log($wpdb->last_error . " " . $wpdb->last_query);
            return FALSE;
        }
        return $status;
    }

    static public function truncate_table($table = '') {

        if (empty($table)) {
            return FALSE;
        }
        global $wpdb;
        return self::query("TRUNCATE {$wpdb->prefix}$table");
    }

    static public function drop_table($table = '') {
        if (empty($table)) {
            return FALSE;
        }

        global $wpdb;
        return self::query("DROP TABLE IF EXISTS {$wpdb->prefix}$table");
    }

    static public function get_col($sql = '') {

        if (empty($sql)) {
            return;
        }

        global $wpdb;

        $result = $wpdb->get_col($sql);

        if ($wpdb->last_error) {
            Error_Log::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            return FALSE;
        }

        return $result;
    }

    static public function get_blog_ids_by_site_id($site_id = NULL) {

        if (is_null($site_id)) {

            $site_id = get_current_network_id();
        }

        if (!is_numeric($site_id) || !($site_id > 0)) {
            return FALSE;
        }
        global $wpdb;
        return self::get_col("SELECT blog_id FROM $wpdb->blogs WHERE `site_id` = " . absint($site_id) . " ORDER BY blog_id ASC");
    }

    static public function dbDelta($sql = '') {
        if (empty($sql)) {
            return FALSE;
        }
        global $wpdb;
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta($sql);
        if (!empty($wpdb->last_error)) {
            Error_Log::error_log("Error while creating or updatiing tables-" . $wpdb->last_error, __LINE__, __FILE__);
            wp_die($wpdb->last_error);
        }
    }

    static public function is_table_exist($table = '') {
        if (empty($table) || !is_string($table)) {

            return FALSE;
        }
        global $wpdb;

        $query = $wpdb->prepare("SHOW TABLES LIKE %s", $wpdb->esc_like($table));
        return $wpdb->get_var($query) == $table;
    }

    static public function is_blog_exist($blog_id = NULL, $site_id = NULL) {
        if (is_null($blog_id) || !is_numeric($blog_id) || !($blog_id > 0)) {
            return FALSE;
        }

        if (is_null($site_id)) {

            $site_id = get_current_network_id();
        }

        if (!is_numeric($site_id) || !($site_id > 0)) {
            return FALSE;
        }
        global $wpdb;
        return self::get_var("SELECT blog_id FROM $wpdb->blogs WHERE `blog_id` = " . absint($blog_id) . " AND `site_id` = " . absint($site_id) . " ORDER BY blog_id ASC");
    }

}
