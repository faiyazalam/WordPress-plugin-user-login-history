<?php

class User_Login_History_Admin_List_Table extends User_Login_History_Abstract_List_Table {

    public function __construct($args = array(), $plugin_name = '') {
        parent::__construct(array(
            'singular' => $plugin_name . '_admin_user', //singular name of the listed records
            'plural' => $plugin_name . '_admin_users', //plural name of the listed records
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

        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']);
    }

    /**
     * Retrieve rows
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @access   public
     * @return mixed
     */
    public function get_rows($per_page = 20, $page_number = 1) {
        global $wpdb;
        $table = $wpdb->prefix . USER_LOGIN_HISTORY_TABLE_NAME;
        $sql = " SELECT"
                . " FaUserLogin.*, "
                . " UserMeta.meta_value "
                . " FROM " . $table . "  AS FaUserLogin"
                . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                . " WHERE 1 ";

        $where_query = $this->prepare_where_query();
        if ($where_query) {
            $sql .= $where_query;
        }
        $sql .= ' GROUP BY FaUserLogin.id';
        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
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
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public function record_count() {
        global $wpdb;
        $table = $wpdb->prefix . USER_LOGIN_HISTORY_TABLE_NAME;
        $sql = " SELECT"
                . " FaUserLogin.id"
                . " FROM " . $table . " AS FaUserLogin"
                . " LEFT JOIN $wpdb->users AS User ON User.ID = FaUserLogin.user_id"
                . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                . " WHERE 1 ";

        $where_query = $this->prepare_where_query();

        if ($where_query) {
            $sql .= $where_query;
        }
        $sql .= ' GROUP BY FaUserLogin.id';
        $sql_count = "SELECT COUNT(*) as total FROM ($sql) AS FaUserLoginCount ";

        $result = $wpdb->get_var($sql_count);
        if ("" != $wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_username($item) {
        $delete_nonce = wp_create_nonce($this->plugin_name . 'delete_row_by_' . $this->_args['singular']);
        $title = '<strong>' . $item['username'] . '</strong>';
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), $this->plugin_name . '_admin_listing_table_delete_single_row', absint($item['id']), $delete_nonce),
        );

        return $title . $this->row_actions($actions);
    }

    public function process_bulk_action() {
        if (!isset($_POST[$this->plugin_name . '_admin_listing_table']) || empty($_POST['_wpnonce'])) {
            return FALSE;
        }

        $status = FALSE;
        $nonce = $_POST['_wpnonce'];
        $bulk_action = 'bulk-' . $this->_args['plural'];

        switch ($this->current_action()) {
            case 'bulk-delete':
                if (!empty($_POST['bulk-delete'])) {
                    if (!wp_verify_nonce($nonce, $bulk_action)) {
                        wp_die('invalid nonce');
                    }
                    $this->delete_rows($_POST['bulk-delete']);
                    $status = TRUE;
                }
                break;
            case 'bulk-delete-all-admin':
                if (!wp_verify_nonce($nonce, $bulk_action)) {
                    wp_die('invalid nonce');
                }

                $this->delete_all_rows();
                $status = TRUE;
                break;



            default:
                $status = FALSE;
                break;
        }

        return $status;
    }

    public function delete_single_row() {
        if (empty($_GET['action']) || $this->plugin_name . '_admin_listing_table_delete_single_row' != $_GET['action'] || empty($_REQUEST['_wpnonce'])) {
            return FALSE;
        }

        $nonce = $_GET['_wpnonce'];

        if (!wp_verify_nonce($nonce, $this->plugin_name . 'delete_row_by_' . $this->_args['singular'])) {
            wp_die('invalid nonce');
        }

        return $this->delete_rows($_GET['customer']);
    }

}
