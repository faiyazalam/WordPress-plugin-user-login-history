<?php

/**
 * User_Login_History_List_Table
 * This class is used for listing table in admin panel.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 */
?>
<?php

require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-wp-list-table.php';

class User_Login_History_List_Table extends User_Login_History_WP_List_Table {

    /**
     * The name of the table used by this plugin.
     *
     * @since    1.4.1
     * @access   private
     * @var      string    $table    The name of the table used by this plugin.
     */
    static private $table;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.4.1
     */
    public function __construct() {
        global $table_prefix;
        parent::__construct(array(
            'singular' => __('User Login History', 'user-login-history'), //singular name of the listed records
            'plural' => __('User Login Histories', 'user-login-history'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));


        self::$table = $table_prefix . ULH_TABLE_NAME;
        ;
    }

    /**
     * Prepare sql where query.
     *
     * @since    1.4.1
     */
    public function prepare_where_query() {

        $fields = array(
            'user_id',
            'username',
            'country_name',
            'browser',
            'operating_system',
            'ip_address',
            'role',
            'old_role',
            'date_from',
            'date_to',
            'timezone',
        );

        $fields_2 = array(
            'country_name',
            'browser',
            'operating_system',
            'ip_address',
        );


        $sql_query = FALSE;
        $count_query = FALSE;
        $values = array();
        $date_type = FALSE;
        $ulh_table_alias = 'FaUserLogin';
        $user_meta_table_alias = 'UserMeta';
        $user_table_alias = 'User';

        $table_aliases = array(
            'user_meta' => $user_meta_table_alias,
            'user' => $user_table_alias,
            'ulh' => $ulh_table_alias,
        );


        if (isset($_GET['date_type'])) {
            if ("login" == $_GET['date_type']) {
                $date_type = 'login';
            }
            if ("logout" == $_GET['date_type']) {
                $date_type = 'logout';
            }
        }

        foreach ($fields as $field) {
            $data_type = "%s";
            $operator_sign = "=";
            if (isset($_GET[$field]) && "" != $_GET[$field]) {
                $getValue = $_GET[$field];

                if (in_array($field, $fields_2)) {
                    $operator_sign = "LIKE";
                    $getValue = "%" . $getValue . "%";
                    $field = "$ulh_table_alias.$field";
                }

                if ('old_role' == $field) {
                    $operator_sign = "LIKE";
                    $field = "$ulh_table_alias.$field";
                }

                if ('user_id' == $field) {
                    $data_type = "%d";
                    $field = "$ulh_table_alias.user_id";
                }

                if ('role' == $field) {
                    $field = "$user_meta_table_alias.meta_value";
                    $operator_sign = "LIKE";
                    $getValue = "%" . $getValue . "%";
                }

                if ('username' == $field) {
                    $operator_sign = "LIKE";
                    $getValue = "%" . $getValue . "%";
                    $field = "$user_table_alias.user_login";
                }

                if ($date_type && in_array($field, array('date_from', 'date_to'))) {
                    $Date_Time_Helper = new User_Login_History_Date_Time_Helper();
                    $default_timezone = $Date_Time_Helper->get_default_timezone();
                    $getValue = $Date_Time_Helper->convert_to_user_timezone($getValue, 'Y-m-d', $default_timezone);

                    if ('date_from' == $field) {
                        $field = "$ulh_table_alias.time_" . $date_type;
                        $operator_sign = ">=";
                        $getValue = $getValue . " 00:00:00";
                    }
                    if ('date_to' == $field) {
                        $field = "$ulh_table_alias.time_" . $date_type;
                        $operator_sign = "<=";
                        $getValue = $getValue . " 23:59:59";
                    }
                }

                $sql_query .= " AND $field $operator_sign $data_type ";
                $values[] = esc_sql($getValue);
            }
        }


        $where_sql_query = array('sql_query' => $sql_query, 'values' => $values);

        $where_sql_query = apply_filters('user_login_history_prepare_where_query', $where_sql_query, $table_aliases);

        $where_sql_query['count_query'] = $where_sql_query['sql_query']; // add count query element
        return $where_sql_query;
    }

    /**
     * Retrieve rows
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @since    1.4.1
     * @access   public
     * @return mixed
     */
    public function get_rows($per_page = 5, $page_number = 1) {
        global $wpdb;
        $get_values = array();

        $table = self::$table;
        $table_usermeta = $wpdb->usermeta;
        $table_users = $wpdb->users;

        $sql = "select "
                . "DISTINCT(FaUserLogin.id) as id, "
                . "User.user_login,"
                . "FaUserLogin.*, "
                . "UserMeta.* "
                . "FROM $table  AS FaUserLogin  "
                . " INNER JOIN $table_users as User ON User.ID = FaUserLogin.user_id"
                . " INNER JOIN $table_usermeta AS UserMeta ON UserMeta.user_id=FaUserLogin.user_id where UserMeta.meta_key = '{$wpdb->prefix}capabilities' AND  1 ";


        $where_query = $this->prepare_where_query();

        if ($where_query) {

            $sql .= $where_query['sql_query'];
            $get_values = $where_query['values'];
        }

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);

            $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {

            $sql .= ' ORDER BY id DESC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= '  OFFSET   ' . ( $page_number - 1 ) * $per_page . "   ";

        if (!empty($get_values)) {
            return $wpdb->get_results($wpdb->prepare($sql, $get_values), 'ARRAY_A');
        }
        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    /**
     * Delete a record.
     *
     * @param int $id row ID
     * @since    1.4.1
     * @access   public
     */
    public static function delete_record($id) {
        global $wpdb;
        $wpdb->delete(
                self::$table, array('id' => $id), array('%d')
        );
    }

    /**
     * Returns the count of records in the table.
     * @since    1.4.1
     * @access   public
     * @return null|string
     */
    public function record_count() {

        global $wpdb;
        $get_values = array();

        $table = self::$table;
        $table_usermeta = $wpdb->usermeta;
        $table_users = $wpdb->users;

        $sql = "select "
                . "COUNT(DISTINCT(FaUserLogin.id))"
                . "FROM " . $table . " AS FaUserLogin  "
                . "INNER JOIN " . $table_users . " AS User ON User.ID = FaUserLogin.user_id  "
                . " INNER JOIN $table_usermeta AS UserMeta ON UserMeta.user_id=FaUserLogin.user_id where UserMeta.meta_key = '{$wpdb->prefix}capabilities' AND  1 ";

        $where_query = $this->prepare_where_query();

        if ($where_query) {
            $sql .= $where_query['count_query'];
            $get_values = $where_query['values'];
        }

        if (!empty($get_values)) {
            return $wpdb->get_var($wpdb->prepare($sql, $get_values));
        }

        return $wpdb->get_var($sql);
    }

    /**
     * Text displayed when no record is available 
     * @since    1.4.1
     * @access   public
     */
    public function no_items() {
        _e('No Records Found!', 'user-login-history');
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     * @since    1.4.1
     * @access   public
     * @return mixed
     */
    public function column_default($item, $column_name) {
        global $current_user;
        $Date_Time_Helper = new User_Login_History_Date_Time_Helper();
        $timezone = get_user_meta($current_user->ID, ULH_PLUGIN_OPTION_PREFIX . "user_timezone", TRUE);
        $timezone = ("" != $timezone) ? $timezone : $Date_Time_Helper->get_default_timezone();
        $unknown = __('Unknown', 'user-login-history');

        $current_date_time = $Date_Time_Helper->get_current_date_time();
        $new_column_data = apply_filters('manage_user_login_history_custom_column', '', $item, $column_name);
        switch ($column_name) {
            case 'user_id':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'username':
                $profile_link = get_edit_user_link($item['user_id']);
                $username = $item['user_login'];
                return "<a href= '$profile_link'>$username</a>";
            case 'role':
                $user_data = get_userdata($item['user_id']);
                return implode(',', $user_data->roles);
            case 'old_role':
                 return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'browser':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'time_login':
                return $Date_Time_Helper->convert_to_user_timezone($item[$column_name], '', $timezone);
            case 'time_logout':
                return strtotime($item[$column_name]) > 0  ?$Date_Time_Helper->convert_to_user_timezone($item[$column_name], '', $timezone):  __('Logged In', 'user-login-history')  ;
            case 'ip_address':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'timezone':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'operating_system':
                return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'country_name':
                return in_array(strtolower($item[$column_name]), array("", strtolower($unknown))) ? $unknown : $item[$column_name] . "(" . $item['country_code'] . ")";
            case 'time_last_seen':
                return $Date_Time_Helper->human_time_diff_from_now($item[$column_name], $timezone);
            case 'user_agent':
              return $item[$column_name] ? $item[$column_name] : $unknown;
            case 'duration':
                $start_time_str = strtotime($item['time_login']);
                $logout_time_str = strtotime($item['time_logout']);
                 $end_time_str = $logout_time_str > 0 ?$logout_time_str: strtotime($item['time_last_seen']) ;
                return  human_time_diff($start_time_str, $end_time_str);
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
     * @since    1.4.1
     * @access   public
     * @return string
     */
    public function column_cb($item) {

        return sprintf(
                '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     * @since    1.4.1
     * @access   public
     * @return string
     */
    public function column_name($item) {

        $delete_nonce = wp_create_nonce(ULH_PLUGIN_OPTION_PREFIX . 'delete_record');

        $title = '<strong>' . $item['user_id'] . '</strong>';

        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&record=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce)
        );

        return $title . $this->row_actions($actions);
    }

    /**
     *  Associative array of columns
     * @since    1.4.1
     * @access   public
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
        );
        $columns = apply_filters('user_login_history_get_columns', $columns);
        return $columns;
    }

    /**
     * Columns to make sortable.
     * @since    1.4.1
     * @access   public
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'user_id' => array('FaUserLogin.user_id', true),
            'username' => array('username', true),
            'old_role' => array('old_role', true),
            'time_login' => array('time_login', false),
            'time_logout' => array('time_logout', false),
            'ip_address' => array('ip_address', false),
            'browser' => array('browser', false),
            'operating_system' => array('operating_system', false),
            'country_name' => array('country_name', false),
            'time_last_seen' => array('time_last_seen', false),
            'timezone' => array('time_last_seen', false),
            'user_agent' => array('user_agent', false),
        );
        $sortable_columns = apply_filters('user_login_history_get_sortable_columns', $sortable_columns);
        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     * @since    1.4.1
     * @access   public
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete' => 'Delete'
        );
        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     * @since    1.4.1
     * @access   public
     */
    public function prepare_items() {
        $this->_column_headers = $this->get_column_info();
        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('rows_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ));

        $this->items = self::get_rows($per_page, $current_page);
    }

    /**
     * Process bulk delete action.
     * @since    1.4.1
     * @access   public
     */
    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        $User_Login_History_Admin = new User_Login_History_Admin(ULH_PLUGIN_NAME, ULH_PLUGIN_VERSION, ULH_PLUGIN_OPTION_PREFIX);
        if ('delete' === $this->current_action()) {
            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, ULH_PLUGIN_OPTION_PREFIX . 'delete_record')) {
                wp_die(__('Go get a life script kiddies', 'user-login-history'));
            } else {
                self::delete_record(absint($_GET['record']));
                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                $User_Login_History_Admin->add_admin_notice('The record(s) has been deleted.');
                wp_redirect(esc_url_raw(add_query_arg()));
                exit;
            }
        }
        // If the delete bulk action is triggered
        if (( isset($_POST['action']) && $_POST['action'] == 'bulk-delete' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete' )
        ) {
            $delete_ids = esc_sql($_POST['bulk-delete']);
            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_record($id);
            }
            $User_Login_History_Admin->add_admin_notice('The record(s) has been deleted.');
            wp_redirect(esc_url_raw(add_query_arg()));
            exit;
        }
    }

}
