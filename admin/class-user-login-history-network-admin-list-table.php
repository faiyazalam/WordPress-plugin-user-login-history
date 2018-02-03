<?php

class User_Login_History_Network_Admin_List_Table extends User_Login_History_Abstract_List_Table {

    public function __construct($args = array(), $plugin_name) {
        parent::__construct(array(
            'singular' => __('network_admin_user', 'user-login-history'), //singular name of the listed records
            'plural' => __('network_admin_users', 'user-login-history'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
                ), $plugin_name);
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb($item) {
        $blog_id = !empty($item['blog_id']) ? $item['blog_id'] : 0;
        return sprintf(
                '<input type="checkbox" name="bulk-delete[blog_id][%s][]" value="%s" />', $blog_id, $item['id']
        );
    }

    public function get_rows($per_page = 20, $page_number = 1) {
      
        global $wpdb;
        $where_query = $this->prepare_where_query();
        $get_values = array();
        $table_usermeta = $wpdb->usermeta;
        $i = 0;
        $sql = "";
        $blog_ids = $this->get_current_network_blog_ids();

        foreach ($blog_ids as $blog_id) {
            $table = $wpdb->get_blog_prefix($blog_id) . USER_LOGIN_HISTORY_TABLE_NAME;
            if (0 < $i) {
                $sql .= " UNION ";
            }

            $sql .= " SELECT"
                    . " FaUserLogin.*,"
                    . " UserMeta.meta_value,"
                    . " $blog_id as blog_id"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $table_usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                    . " WHERE 1 ";

            if ($where_query) {
                $sql .= $where_query;
            }

            $sql .= ' GROUP BY FaUserLogin.id ';
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

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        if ("" != $wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }

    /**
     * 
     * @global type $wpdb
     * @return array
     */
    private function get_current_network_blog_ids() {
        global $wpdb;
        $where = "";

        if (!empty($_GET['blog_id'])) {
            $where .= " AND `blog_id` = " . esc_sql(absint($_GET['blog_id']));
        }

        $where .= " AND `site_id` = " . get_current_network_id();
        $sql = "SELECT blog_id FROM $wpdb->blogs WHERE 1 $where";

        $result = $wpdb->get_col($sql);
        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }

    /**
     * Returns the count of records in the table.
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
        $blog_ids = $this->get_current_network_blog_ids();
        
        foreach ($blog_ids as $blog_id) {
            $table = $wpdb->get_blog_prefix($blog_id) . USER_LOGIN_HISTORY_TABLE_NAME;

            if (0 < $i) {
                $sql .= " UNION ";
            }


            $sql .= "SELECT COUNT(*) as count FROM  (SELECT"
                    . " FaUserLogin.id"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $table_usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                    . " WHERE 1 ";

            if ($where_query) {
                $sql .= $where_query;
            }

            $sql .= ' GROUP BY FaUserLogin.id) AS FaUserLoginSubCount';
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
        $blog_id = !empty($item['blog_id']) ? $item['blog_id'] : 0;
        $delete_nonce = wp_create_nonce(USER_LOGIN_HISTORY_OPTION_PREFIX . 'delete_row_by_' . $this->_args['singular']);
        $title = '<strong>' . $item['username'] . '</strong>';
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&blog_id=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($blog_id), absint($item['id']), $delete_nonce),
        );
        return $title . $this->row_actions($actions);
    }

    public function process_bulk_action() {
        $status = FALSE;
        $nonce = !empty($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : "";
        $bulk_action = 'bulk-' . $this->_args['plural'];

        switch ($this->current_action()) {
            case 'bulk-delete':
                if (!empty($_POST['bulk-delete'])) {
                    if (!wp_verify_nonce($nonce, $bulk_action)) {
                        wp_die('invalid nonce');
                    }
                    $ids = $_POST['bulk-delete']['blog_id'];
                    foreach ($ids as $blog_id => $record_ids) {
                        switch_to_blog($blog_id);
                        $this->delete_rows($record_ids, $blog_id);
                    }
                    restore_current_blog();
                    $status = TRUE;
                }
                break;
            case 'bulk-delete-all-admin':

                if (!wp_verify_nonce($nonce, $bulk_action)) {
                    wp_die('invalid nonce');
                }
                $blog_ids = $this->get_current_network_blog_ids();
                foreach ($blog_ids as $blog_id) {
                    switch_to_blog($blog_id);
                    $this->delete_all_rows();
                }
                restore_current_blog();
                $status = TRUE;
                break;

            case 'delete':
                if (!wp_verify_nonce($nonce, USER_LOGIN_HISTORY_OPTION_PREFIX . 'delete_row_by_' . $this->_args['singular'])) {
                    wp_die('invalid nonce');
                }
                //get the blog id of current network.
                $blog_ids = $this->get_current_network_blog_ids();
                $blog_id = !empty($blog_ids[0]) ? absint($blog_ids[0]) : NULL;

                if ($blog_id) {
                    switch_to_blog($blog_id);
                    $this->delete_rows($_GET['customer']);
                    restore_current_blog();
                    $status = TRUE;
                }

                break;

            default:
                $status = FALSE;
                break;
        }
        return $status;
    }

}
