<?php

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

abstract class User_Login_History_Abstract_List_table extends WP_List_Table {

    private $table;

    /** Class constructor */
    public function __construct($args = array()) {

        parent::__construct(array(
            'singular' => __('User', 'user-login-history'), //singular name of the listed records
            'plural' => __('Users', 'user-login-history'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));

        $this->table = User_Login_History_DB_Helper::get_table_name();
    }

    public function no_items() {
        _e('No records avaliable.', 'user-login-history');
    }

    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public function prepare_where_query() {
        $where_query = '';

        $fields = array(
            'user_id',
            'username',
            'country_name',
            'browser',
            'operating_system',
            'ip_address',
            'timezone',
            'country_name',
            'browser',
            'operating_system',
            'login_status',
        );

        foreach ($fields as $field) {
            if (isset($_GET[$field]) && "" != $_GET[$field]) {
                $where_query .= " AND `FaUserLogin`.`$field` = '" . esc_sql($_GET[$field]) . "'";
            }
        }

        if (isset($_GET['role']) && "" != $_GET['role']) {

            if ('superadmin' == $_GET['role']) {
                $site_admins = get_super_admins();
                $site_admins_str = implode("', '", $site_admins);
                $where_query .= " AND `FaUserLogin`.`username` IN ('$site_admins_str')";
            } else {
                $where_query .= " AND `UserMeta`.`meta_value` LIKE '%" . esc_sql($_GET['role']) . "%'";
            }
        }

        if (isset($_GET['old_role']) && "" != $_GET['old_role']) {
            if ('superadmin' == $_GET['old_role']) {
                $where_query .= " AND `FaUserLogin`.`is_super_admin` LIKE '1'";
            } else {
                $where_query .= " AND `FaUserLogin`.`old_role` LIKE '%" . esc_sql($_GET['old_role']) . "%'";
            }
        }

        if (!empty($_GET['date_type'])) {
            $date_type = esc_sql($_GET['date_type']);

            if (in_array($date_type, array('login', 'logout', 'last_seen'))) {
                if (isset($_GET['date_from']) && "" != $_GET['date_from']) {
                    $where_query .= " AND `FaUserLogin`.`time_$date_type` >= '" . esc_sql($_GET['date_from']) . " 00:00:00'";
                }

                if (isset($_GET['date_to']) && "" != $_GET['date_to']) {
                    $where_query .= " AND `FaUserLogin`.`time_$date_type` <= '" . esc_sql($_GET['date_to']) . " 23:59:59'";
                }
            }
        }
        $where_query = apply_filters('user_login_history_admin_prepare_where_query', $where_query);
        return $where_query;
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

        $sql = " SELECT"
                . " FaUserLogin.*, "
                . " UserMeta.meta_value "
                . " FROM " . $this->table . "  AS FaUserLogin"
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
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public function delete_rows($ids = array()) {

        global $wpdb;
        if (!empty($ids)) {
            if (!is_array($ids)) {
                $ids = array($ids);
            }
            $ids = implode(',', array_map('absint', $ids));

            $status = $wpdb->query("DELETE FROM $this->table WHERE id IN($ids)");
            if ($wpdb->last_error) {
                User_Login_History_Error_Handler::error_log($wpdb->last_error . " " . $wpdb->last_query, __LINE__, __FILE__);
            }
            return $status;
        }
        return FALSE;
    }

    public function delete_all_rows() {
        global $wpdb;
        $status = $wpdb->query("TRUNCATE $this->table");
        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log($wpdb->last_error . " " . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $status;
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public function record_count() {
        global $wpdb;
        $sql = " SELECT"
                . " FaUserLogin.id"
                . " FROM " . $this->table . " AS FaUserLogin"
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
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name) {
        global $current_user;
        $timezone = get_user_meta($current_user->ID, USER_LOGIN_HISTORY_OPTION_PREFIX . "user_timezone", TRUE);

        $timezone = $timezone && "unknown" != strtolower($timezone) ? $timezone : FALSE;

        $unknown = __('Unknown', 'user-login-history');
        $new_column_data = apply_filters('manage_user_login_history_admin_custom_column', '', $item, $column_name);
        switch ($column_name) {
            case 'user_id':
                if (!$item[$column_name]) {
                    return $unknown;
                }
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'username':
                if (!$item['user_id']) {
                    return $item[$column_name];
                }

                $profile_link = get_edit_user_link($item['user_id']);
                return "<a href= '$profile_link'>$item[$column_name]</a>";
            case 'role':
                if (!$item['user_id']) {
                    return $unknown;
                }
                $user_data = get_userdata($item['user_id']);
                return isset($user_data->roles) && !empty($user_data->roles) ? implode(',', $user_data->roles) : $unknown;
            case 'old_role':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'browser':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'time_login':
                return User_Login_History_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone);
            case 'time_logout':
                if (!$item['user_id']) {
                    return $unknown;
                }
                return strtotime($item[$column_name]) > 0 ? User_Login_History_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone) : __('Logged In', 'user-login-history');
            case 'ip_address':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'timezone':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'operating_system':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'country_name':
                $item['country_code'] = isset($item['country_code']) && "" != $item['country_code'] ? $item['country_code'] : $unknown;
                return in_array(strtolower($item[$column_name]), array("", strtolower($unknown))) ? $unknown : $item[$column_name] . "(" . $item['country_code'] . ")";
            case 'time_last_seen':
                if (!$item['user_id']) {
                    return $unknown;
                }
                $time_last_seen = User_Login_History_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone);
                $human_time_diff = human_time_diff(strtotime($item[$column_name]));
                return "<span title = '$time_last_seen'>" . $human_time_diff . " " . __('ago', 'user-login-history') . '</span>';
            case 'user_agent':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'duration':
                $duration = human_time_diff(strtotime($item['time_login']), User_Login_History_Date_Time_Helper::get_last_time($item['time_logout'], $item['time_last_seen']));
                ;
                return $duration ? $duration : $unknown;

            case 'login_status':
                return $item[$column_name] ? $item[$column_name] : $unknown;

            case 'site_id':
                return $item[$column_name] ? $item[$column_name] : $unknown;

            case 'blog_id':
                return $item[$column_name] ? $item[$column_name] : $unknown;

            case 'is_super_admin':
                return $item[$column_name] ? __('Yes', 'user-login-history') : __('No', 'user-login-history');


            default:
                if ($new_column_data) {
                    echo $new_column_data;
                    return;
                }
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_username($item) {

        $delete_nonce = wp_create_nonce(USER_LOGIN_HISTORY_OPTION_PREFIX . 'delete_row');

        $title = '<strong>' . $item['username'] . '</strong>';

        $actions = [
            'delete' => sprintf('<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce),
        ];

        return $title . $this->row_actions($actions);
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'user_id' => __('User Id', 'user-login-history'),
            'username' => __('Username', 'user-login-history'),
            'role' => __('Current Role', 'user-login-history'),
            'old_role' => __('<span title="Role while user gets loggedin">Old Role(?)</span>', 'user-login-history'),
            'ip_address' => __('IP', 'user-login-history'),
            'browser' => __('Browser', 'user-login-history'),
            'operating_system' => __('OS', 'user-login-history'),
            'country_name' => __('Country', 'user-login-history'),
            'duration' => __('Duration', 'user-login-history'),
            'time_last_seen' => __('<span title="Last seen time in the session">Last Seen(?)</span>', 'user-login-history'),
            'timezone' => __('Timezone', 'user-login-history'),
            'time_login' => __('Login', 'user-login-history'),
            'time_logout' => __('Logout', 'user-login-history'),
            'user_agent' => __('User Agent', 'user-login-history'),
            'login_status' => __('Login Status', 'user-login-history'),
        );

        if (is_multisite()) {
            $columns['is_super_admin'] = __('Super Admin', 'user-login-history');
        }
        $columns = apply_filters('user_login_history_admin_get_columns', $columns);
        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'user_id' => array('user_id', true),
            'username' => array('username', true),
            'old_role' => array('old_role', true),
            'time_login' => array('time_login', false),
            'time_logout' => array('time_logout', false),
            'ip_address' => array('ip_address', false),
            'browser' => array('browser', false),
            'operating_system' => array('operating_system', false),
            'country_name' => array('country_name', false),
            'time_last_seen' => array('time_last_seen', false),
            'timezone' => array('timezone', false),
            'user_agent' => array('user_agent', false),
            'login_status' => array('login_status', false),
            'is_super_admin' => array('is_super_admin', false),
        );
         if (is_multisite()) {
            $sortable_columns['is_super_admin'] = array('is_super_admin', false);
        }
        $sortable_columns = apply_filters('user_login_history_admin_get_sortable_columns', $sortable_columns);
        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => 'Delete Selected Records',
            'bulk-delete-all-admin' => 'Delete All Records',
        );

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
      //  $this->process_bulk_action();

        $per_page = $this->get_items_per_page('rows_per_page');
        $current_page = $this->get_pagenum();
        $total_items = $this->record_count();

        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ));

        $this->items = $this->get_rows($per_page, $current_page);
    }

    public function process_bulk_action() {

        $status = FALSE;
       $nonce =  !empty($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : "";
       $bulk_action = 'bulk-' . $this->_args['plural'];
       
            switch ($this->current_action()) {
                case 'bulk-delete':
                    if (!empty($_POST['bulk-delete'])) {
                        if (!wp_verify_nonce($nonce, $bulk_action)) {
                            wp_die('invalid nonce');
                        }
                        $this->delete_rows(esc_sql($_POST['bulk-delete']));
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

                case 'delete':
                    if (!wp_verify_nonce($nonce, USER_LOGIN_HISTORY_OPTION_PREFIX . 'delete_row')) {
                        wp_die('invalid nonce');
                    }
                    $this->delete_rows(absint($_GET['customer']));
                    $status = TRUE;
                    break;

                default:
                    $status = FALSE;
                    break;
            }
        
        return $status;
    }
    
        /**
     * Generates content for a single row of the table
     * Over-ridden method.
     */
    public function single_row($item) {
        $login_status = !empty($item['login_status']) ? "login_status_" . $item['login_status'] : "";
        echo "<tr class='$login_status'>";
        $this->single_row_columns($item);
        echo '</tr>';
    }

}
