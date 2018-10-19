<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;
use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Common\Abstracts\List_Table as List_Table_Abstract;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;
use User_Login_History\Inc\Common\Interfaces\Admin_List_Table as Admin_List_Table_Interface;

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
final class Admin_Login_List_Table extends Login_List_Table implements Admin_Csv_Interface, Admin_List_Table_Interface {

    /**
     * Retrieve rows
     * 
     * @access   public
     * @param int $per_page
     * @param int $page_number
     * @access   public
     * @return mixed
     */
    public function get_rows($per_page = 20, $page_number = 1) {
        global $wpdb;
        $table = $wpdb->prefix . $this->table;
        $sql = " SELECT"
                . " FaUserLogin.*, "
                . " UserMeta.meta_value, TIMESTAMPDIFF(SECOND,FaUserLogin.time_login,FaUserLogin.time_last_seen) as duration"
                . " FROM " . $table . "  AS FaUserLogin"
                . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                . " AND UserMeta.meta_key LIKE  '" . $wpdb->prefix . "capabilities' )"
                . " WHERE 1 ";

        $where_query = $this->prepare_where_query();
        if ($where_query) {
            $sql .= $where_query;
        }
        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql .= ' ORDER BY id DESC';
        }

        if ($per_page > 0) {
            $sql .= " LIMIT $per_page";
            $sql .= ' OFFSET   ' . ( $page_number - 1 ) * $per_page;
        }
        
        return Db_Helper::get_results($sql);
    }

    /**
     * Returns the count of records in the database.
     * 
     * @access   public
     * @return null|string
     */
    public function record_count() {
        global $wpdb;
        $table = $wpdb->prefix . $this->table;
        $sql = " SELECT"
                . " COUNT(FaUserLogin.id) AS total"
                . " FROM " . $table . " AS FaUserLogin"
                . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                . " AND UserMeta.meta_key LIKE '" . $wpdb->prefix . "capabilities' )"
                . " WHERE 1 ";

        $where_query = $this->prepare_where_query();

        if ($where_query) {
            $sql .= $where_query;
        }

        return Db_Helper::get_var($sql);
    }



    /**
     * Method for name column
     * 
     * @access   public
     * @param array $item an array of DB data
     * @return string
     */
   
    public function column_username($item) {
        $username = $this->is_empty($item['username']) ? $this->unknown_symbol : esc_html($item['username']);
        if ($this->is_empty($item['user_id'])) {
            $title = $username;
        } else {
            $edit_link = get_edit_user_link($item['user_id']);
            $title = !empty($edit_link) ? "<a href='" . $edit_link . "'>" . $username . "</a>" : '<strong>' . $username . '</strong>';
        }

        $delete_nonce = wp_create_nonce($this->delete_action_nonce);
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&record_id=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), $this->delete_action, absint($item['id']), $delete_nonce),
        );
        return $title . $this->row_actions($actions);
    }

    

    /**
     * Render the bulk edit checkbox
     * 
     * @access   public
     * @param array $item
     * @return string
     */
    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="bulk-action-ids[]" value="%s" />', $item['id']);
    }

    

    public function process_bulk_action() {
         $this->set_message(esc_html__('Please try again.', $this->plugin_text_domain));
        switch ($this->current_action()) {
            case 'bulk-delete':
                $status = Db_Helper::delete_rows_by_table_and_ids($this->table, $_POST['bulk-action-ids']);
                if ($status) {
                    $this->set_message(esc_html__('Selected record(s) deleted.', $this->plugin_text_domain));
                }
                break;
            case 'bulk-delete-all-admin':
                $status = Db_Helper::truncate_table($this->table);
                if ($status) {
                    $this->set_message(esc_html__('All record(s) deleted.', $this->plugin_text_domain));
                }
                break;
            default:
                $status = FALSE;
                break;
        }
        return $status;
    }

    public function process_single_action() {
        if (empty($_GET['record_id'])) {
            return;
        }

        $id = absint($_GET['record_id']);
        $this->set_message(esc_html__('Please try again.', $this->plugin_text_domain));
        switch ($this->current_action()) {
            case $this->delete_action:
                $status = Db_Helper::delete_rows_by_table_and_ids($this->table, array($id));
                if ($status) {
                    $this->set_message(esc_html__('Record deleted.', $this->plugin_text_domain));
                }
                break;

            default:
                $status = FALSE;
                break;
        }

        return $status;
    }
    
    public function get_columns() {
         return apply_filters($this->plugin_name."_admin_login_list_get_columns", parent::get_columns());
    }
    public function get_sortable_columns() {
         return apply_filters($this->plugin_name."_admin_login_list_get_columns", parent::get_sortable_columns());
    }

}
