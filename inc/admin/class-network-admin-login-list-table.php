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
final class Network_Admin_Login_List_Table extends Login_List_Table implements Admin_Csv_Interface, Admin_List_Table_Interface {

    private $rows_sql = '';
    private $count_sql = '';

    public function init() {
        parent::init();
        $this->prepare_sql_queries();
    }

    private function prepare_sql_queries() {
        global $wpdb;
        $where_query = $this->prepare_where_query();
        $rows_sql = '';
        $count_sql = '';

        $i = 0;
        $blog_ids = Db_Helper::get_blog_ids_by_site_id();
        foreach ($blog_ids as $blog_id) {
            $blog_prefix = $wpdb->get_blog_prefix($blog_id);
            $table = $blog_prefix . $this->table;

            if (!$this->is_plugin_active_for_network && !Db_Helper::is_table_exist($table)) {
                continue;
            }

            if (0 < $i) {
                $rows_sql .= " UNION ALL";
                $count_sql .= " UNION ALL";
            }

            $rows_sql .= " ( SELECT"
                    . " FaUserLogin.id, "
                    . " FaUserLogin.user_id,"
                    . " FaUserLogin.username,"
                    . " FaUserLogin.time_login,"
                    . " FaUserLogin.time_logout,"
                    . " FaUserLogin.time_last_seen,"
                    . " FaUserLogin.ip_address,"
                    . " FaUserLogin.operating_system,"
                    . " FaUserLogin.browser,"
                    . " FaUserLogin.browser_version,"
                    . " FaUserLogin.country_name,"
                    . " FaUserLogin.country_code,"
                    . " FaUserLogin.timezone,"
                    . " FaUserLogin.old_role,"
                    . " FaUserLogin.user_agent,"
                    . " FaUserLogin.login_status,"
                    . " FaUserLogin.is_super_admin,"
                    . " UserMeta.meta_value, "
                    . " TIMESTAMPDIFF(SECOND,FaUserLogin.time_login,FaUserLogin.time_last_seen) as duration,"
                    . " $blog_id as blog_id"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $wpdb->usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key LIKE '" . $blog_prefix . "capabilities' )"
                    . " WHERE 1 ";


            $count_sql .= " ( SELECT"
                    . " COUNT(FaUserLogin.id) AS count"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $wpdb->usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key LIKE '" . $blog_prefix . "capabilities' )"
                    . " WHERE 1 ";

            if ($where_query) {
                $rows_sql .= $where_query;
                $count_sql .= $where_query;
            }

            $rows_sql .= " )";
            $count_sql .= " ) ";
            $i++;
        }

        $this->rows_sql = $rows_sql;
        $this->count_sql = "SELECT SUM(count) as total FROM ($count_sql) AS FaUserLoginCount";
    }

    public function get_columns() {
        $columns = array_merge(parent::get_columns(), array(
            'blog_id' => esc_html__('Blog ID', $this->plugin_text_domain),
            'is_super_admin' => esc_html__('Super Admin', $this->plugin_text_domain),
        ));

        return apply_filters($this->plugin_name . "_network_admin_login_list_get_columns", $columns);
    }

    public function get_sortable_columns() {
        $columns = array_merge(parent::get_columns(), array(
            'is_super_admin' => array('is_super_admin', false),
            'blog_id' => array('is_super_admin', false),
        ));

        return apply_filters($this->plugin_name . "_network_admin_login_list_get_sortable_columns", $columns);
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     * @access public
     * @return string
     */
    public function column_cb($item) {
        $blog_id = !empty($item['blog_id']) ? absint($item['blog_id']) : 0;
        return sprintf(
                '<input type="checkbox" name="bulk-delete-ids[%s][]" value="%s" />', $blog_id, $item['id']
        );
    }

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

        if (!empty($_REQUEST['orderby'])) {
            $this->rows_sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $this->rows_sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $this->rows_sql .= ' ORDER BY id DESC';
        }

        if ($per_page > 0) {
            $this->rows_sql .= " LIMIT $per_page";
            $this->rows_sql .= ' OFFSET   ' . ( $page_number - 1 ) * $per_page;
        }

        return Db_Helper::get_results($this->rows_sql);
    }

    /**
     * Returns the count of records in the database.
     * 
     * @access   public
     * @return null|string
     */
    public function record_count() {
        return Db_Helper::get_var($this->count_sql);
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function column_username($item) {

        if (empty($item['user_id'])) {
            $title = esc_html($item['username']);
        } else {

            $edit_link = get_edit_user_link($item['user_id']);

            $title = !empty($edit_link) ? "<a href='" . $edit_link . "'>" . esc_html($item['username']) . "</a>" : '<strong>' . esc_html($item['username']) . '</strong>';

            if (empty($item['blog_id'])) {
                return $title;
            }
        }

        $delete_nonce = wp_create_nonce($this->delete_action_nonce);
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&blog_id=%s&record_id=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), $this->delete_action, absint($item['blog_id']), absint($item['id']), $delete_nonce),
        );

        return $title . $this->row_actions($actions);
    }

    public function process_bulk_action() {
        $this->set_message(esc_html__('Please try again.', $this->plugin_text_domain));

        switch ($this->current_action()) {
            case 'bulk-delete':

                if (empty($_POST['bulk-delete-ids'])) {
                    return;
                }
                $ids = $_POST['bulk-delete-ids'];

                foreach ($ids as $blog_id => $record_ids) {
                    switch_to_blog($blog_id);
                    $status = Db_Helper::delete_rows_by_table_and_ids($this->table, $record_ids);
                    restore_current_blog();
                    if (!$status) {
                        break;
                    }
                }
                $this->set_message(esc_html__('Selected record(s) deleted.', $this->plugin_text_domain));
                break;
            case 'bulk-delete-all-admin':
                $blog_ids = Db_Helper::get_blog_ids_by_site_id();
                foreach ($blog_ids as $blog_id) {
                    switch_to_blog($blog_id);
                    $status = Db_Helper::truncate_table($this->table);
                    restore_current_blog();
                    if (!$status) {
                        break;
                    }
                }
                $this->set_message(esc_html__('All record(s) deleted.', $this->plugin_text_domain));
                break;
            default:
                $status = FALSE;
                break;
        }
        return $status;
    }

    public function process_single_action() {

        if (empty($_GET['record_id']) || empty($_GET['blog_id'])) {
            return;
        }

        $id = absint($_GET['record_id']);
        $blog_id = absint($_GET['blog_id']);

        if (!Db_Helper::is_blog_exist($blog_id)) {
            return;
        }
        $this->set_message(esc_html__('Please try again.', $this->plugin_text_domain));
        switch ($this->current_action()) {
            case $this->delete_action:
                switch_to_blog($blog_id);
                $status = Db_Helper::delete_rows_by_table_and_ids($this->table, array($id));
                if ($status) {
                    $this->set_message(esc_html__('Selected record deleted.', $this->plugin_text_domain));
                }
                restore_current_blog();
                break;

            default:
                $status = FALSE;
                break;
        }

        return $status;
    }

}
