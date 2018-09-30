<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;
use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Common\Abstracts\List_Table as List_Table_Abstract;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;

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
final class Network_Login_List_Table extends List_Table_Abstract implements Admin_Csv_Interface {

    public function __construct($plugin_name, $version, $plugin_text_domain) {
        $args = array(
            'singular' => $plugin_name . '_user_login', //singular name of the listed records
            'plural' => $plugin_name . '_user_logins', //plural name of the listed records
        );
        parent::__construct($plugin_name, $version, $plugin_text_domain, $args);
        $this->table = NS\PLUGIN_TABLE_FA_USER_LOGINS;

        $this->bulk_action_form = $this->_args['singular'] . "_form";
        $this->delete_action_nonce = $this->_args['singular'] . "_delete_none";
        $this->delete_action = $this->_args['singular'] . "_delete";
        $this->bulk_action_nonce = 'bulk-' . $this->_args['plural'];
    }

    public function get_message() {
        return $this->message;
    }

    public function get_bulk_action_form() {
        return $this->bulk_action_form;
    }

    public function set_message($message = '') {
        $this->message = $message;
    }

    /**
     * Prepares the where query.
     *
     * @access public
     * @return string
     */
    private function prepare_where_query() {

        $where_query = '';

        $fields = array(
            'user_id',
            'username',
            'browser',
            'operating_system',
            'ip_address',
            'timezone',
            'country_name',
            'old_role',
        );

        foreach ($fields as $field) {
            if (!empty($_GET[$field])) {
                $where_query .= " AND `FaUserLogin`.`$field` = '" . esc_sql(trim($_GET[$field])) . "'";
            }
        }

        if (!empty($_GET['role'])) {
            $where_query .= " AND `UserMeta`.`meta_value` LIKE '%" . esc_sql($_GET['role']) . "%'";
        }


        if (!empty($_GET['date_type'])) {
            $UserProfile = new User_Profile($this->plugin_name, $this->version, $this->plugin_text_domain);
            $input_timezone = $UserProfile->get_user_timezone();
            $date_type = $_GET['date_type'];
            if (in_array($date_type, array('login', 'logout', 'last_seen'))) {

                if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) {
                    $date_type = esc_sql($date_type);
                    $date_from = Date_Time_Helper::convert_timezone($_GET['date_from'] . " 00:00:00", $input_timezone);
                    $date_to = Date_Time_Helper::convert_timezone($_GET['date_to'] . " 23:59:59", $input_timezone);
                    $where_query .= " AND `FaUserLogin`.`time_$date_type` >= '" . esc_sql($date_from) . "'";
                    $where_query .= " AND `FaUserLogin`.`time_$date_type` <= '" . esc_sql($date_to) . "'";
                } else {
                    unset($_GET['date_from']);
                    unset($_GET['date_to']);
                }
            }
        }


        if (!empty($_GET['login_status'])) {
            $login_status = $_GET['login_status'];
            $login_status_value = "unknown" == $login_status ? "" : esc_sql($login_status);
            $where_query .= " AND `FaUserLogin`.`login_status` = '" . $login_status_value . "'";
        }

        $where_query = apply_filters('faulh_admin_prepare_where_query', $where_query);
        return $where_query;
    }

    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'user_id' => esc_html__('User ID', $this->plugin_text_domain),
            'username' => esc_html__('Username', $this->plugin_text_domain),
            'role' => esc_html__('Role', $this->plugin_text_domain),
            'old_role' => esc_html__('Old Role', $this->plugin_text_domain),
            'browser' => esc_html__('Browser', $this->plugin_text_domain),
            'operating_system' => esc_html__('Operating System', $this->plugin_text_domain),
            'ip_address' => esc_html__('IP Address', $this->plugin_text_domain),
            'timezone' => esc_html__('Timezone', $this->plugin_text_domain),
            'country_name' => esc_html__('Country', $this->plugin_text_domain),
            'user_agent' => esc_html__('User Agent', $this->plugin_text_domain),
            'duration' => esc_html__('Duration', $this->plugin_text_domain),
            'time_last_seen' => esc_html__('Last Seen', $this->plugin_text_domain),
            'time_login' => esc_html__('Login', $this->plugin_text_domain),
            'time_logout' => esc_html__('Logout', $this->plugin_text_domain),
            'login_status' => esc_html__('Login Status', $this->plugin_text_domain),
            'blog_id' => esc_html__('Blog ID', $this->plugin_text_domain),
            'is_super_admin' => esc_html__('Super Admin', $this->plugin_text_domain),
        );
        return $columns;
    }

    public function get_sortable_columns() {
        $columns = array(
            'user_id' => array('user_id', true),
            'username' => array('username', true),
            'old_role' => array('old_role', true),
            'time_login' => array('time_login', false),
            'time_logout' => array('time_logout', false),
            'browser' => array('browser', false),
            'operating_system' => array('operating_system', false),
            'country_name' => array('country_name', false),
            'time_last_seen' => array('time_last_seen', false),
            'timezone' => array('timezone', false),
            'user_agent' => array('user_agent', false),
            'login_status' => array('login_status', false),
            'is_super_admin' => array('is_super_admin', false),
            'blog_id' => array('is_super_admin', false),
            'duration' => array('duration', false),
        );

        return $columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @access public 
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => esc_html__('Delete Selected Records', 'faulh'),
            'bulk-delete-all-admin' => esc_html__('Delete All Records', 'faulh'),
        );

        return $actions;
    }



    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     * @access public
     * @return string
     */
    public function column_cb($item) {
        $blog_id = !empty($item['blog_id']) ? $item['blog_id'] : 0;
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
        global $wpdb;
        $where_query = $this->prepare_where_query();
        $i = 0;
        $sql = "";
        $blog_ids = Db_Helper::get_blog_ids_by_site_id();



        foreach ($blog_ids as $blog_id) {

            $blog_prefix = $wpdb->get_blog_prefix($blog_id);
            $table = $blog_prefix . $this->table;


//if plugin is not activated network wide, we have to check existence of plugin table.
            if (!$this->is_plugin_active_for_network) {
                if (!Db_Helper::is_table_exist($table)) {
                    continue;
                }
            }

            if (0 < $i) {
                $sql .= " UNION ALL";
            }

            $sql .= " ( SELECT"
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

            if ($where_query) {
                $sql .= $where_query;
            }
            $sql .= " )";
            $i++;
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
        $get_values = array();
        $where_query = $this->prepare_where_query();
        $table_usermeta = $wpdb->usermeta;
        $table_users = $wpdb->users;
        $i = 0;
        $sql = "";
        $blog_ids = Db_Helper::get_blog_ids_by_site_id();

        foreach ($blog_ids as $blog_id) {
            $blog_prefix = $wpdb->get_blog_prefix($blog_id);
            $table = $blog_prefix . $this->table;


            if (!$this->is_plugin_active_for_network) {
                if (!Db_Helper::is_table_exist($table)) {
                    continue;
                }
            }


            if (0 < $i) {
                $sql .= " UNION ALL";
            }


            $sql .= " ( SELECT"
                    . " COUNT(FaUserLogin.id) AS count"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $table_usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key LIKE '" . $blog_prefix . "capabilities' )"
                    . " WHERE 1 ";

            if ($where_query) {
                $sql .= $where_query;
            }
            $sql .= " ) ";
            $i++;
        }
        $sql_count = "SELECT SUM(count) as total FROM ($sql) AS FaUserLoginCount";

        return $wpdb->get_var($sql_count);
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_username($item) {

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
        
        if(!Db_Helper::is_blog_exist($blog_id))
        {
            
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
