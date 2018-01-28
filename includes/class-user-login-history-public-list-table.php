<?php

class User_Login_History_Public_List_Table {

    const DEFALUT_LIMIT = 20;
    const DEFALUT_PAGE_NUMBER = 1;
    const DEFALUT_QUERY_ARG_PAGE_NUMBER = 'pagenum';

    private $limit;
    private $page_number;
    private $pagination_links;
    private $items;
    private $table;
    private $plugin_name;

    public function __construct($limit = false) {

        $this->limit = $limit > 0 ? intval($limit) : self::DEFALUT_LIMIT;
        $this->page_number = !empty($_REQUEST[self::DEFALUT_QUERY_ARG_PAGE_NUMBER]) ? intval($_REQUEST[self::DEFALUT_QUERY_ARG_PAGE_NUMBER]) : self::DEFALUT_PAGE_NUMBER;
        $this->table = User_Login_History_DB_Helper::get_table_name();
        $this->plugin_name = USER_LOGIN_HISTORY_NAME;
    }

    public function prepare_items() {
        $this->items = $this->get_rows();

        $this->pagination_links = paginate_links(array(
            'base' => add_query_arg(self::DEFALUT_QUERY_ARG_PAGE_NUMBER, '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;', 'user-login-history'),
            'next_text' => __('&raquo;', 'user-login-history'),
            'total' => ceil($this->record_count() / $this->limit), //total pages
            'current' => $this->page_number
        ));
    }

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

    public function prepare_main_query() {
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
        return $sql;
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
    public function get_rows() {
        global $wpdb;
        $sql = $this->prepare_main_query();

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql .= ' ORDER BY id DESC';
        }

        if ($this->limit > 0) {
            $sql .= " LIMIT $this->limit";
            $sql .= ' OFFSET   ' . ( $this->page_number - 1 ) * $this->limit;
            ;
        }
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        if ("" != $wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }

    public function record_count() {
        global $wpdb;
        $sql = $this->prepare_main_query();

        $result = $wpdb->get_var("SELECT COUNT(*) as total FROM ($sql) AS FaUserLoginCount ");
        if ("" != $wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }

    public function get_columns() {
        $columns = array(
            'user_id' => __('User Id', 'user-login-history'),
            'username' => __('Username', 'user-login-history'),
            'role' => __('Current Role', 'user-login-history'),
            'old_role' => __('<span title="Role while user gets loggedin">Old Role(?)</span>', 'user-login-history'),
            'ip_address' => __('IP Address', 'user-login-history'),
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


        $columns = apply_filters('user_login_history_public_get_columns', $columns);
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
            'browser' => array('browser', true),
            'operating_system' => array('operating_system', false),
            'country_name' => array('country_name', false),
            'time_last_seen' => array('time_last_seen', false),
            'timezone' => array('timezone', false),
            'user_agent' => array('user_agent', false),
            'login_status' => array('login_status', false),
        );

        $sortable_columns = apply_filters('user_login_history_public_get_sortable_columns', $sortable_columns);
        return $sortable_columns;
    }

    private function display_pagination() {
        echo $this->pagination_links;
    }

    public function get_allowed_columns() {
        $allowed_columns = array();
        $columns = maybe_unserialize(get_option($this->plugin_name . '-basics'));
        if (isset($columns['frontend_columns']) && is_array($columns['frontend_columns'])) {
            $allowed_columns = $columns['frontend_columns'];
        }
        return $allowed_columns;
    }

    public function print_column_headers() {
        $columns = $this->get_columns();
        $allowed_columns = $this->get_allowed_columns();

        //log the exception
        if (empty($allowed_columns)) {
            User_Login_History_Error_Handler::error_log("No columns is selected for frontend listing table.", __LINE__, __FILE__);
            $this->pagination_links = ""; //disable pagination link.
            return;
        }

        $sortable_columns = $this->get_sortable_columns();
        foreach ($columns as $key => $column) {

            if (isset($allowed_columns[$key])) {
                echo "<th>";

                if (isset($sortable_columns[$key])) {
                    $orderby = isset($sortable_columns[$key][0]) ? $sortable_columns[$key][0] : $key;
                    if (isset($sortable_columns[$key][1]) && $sortable_columns[$key][1]) {
                        $order_a = 'asc';
                        $order_b = 'desc';
                    } else {
                        $order_a = 'desc';
                        $order_b = 'asc';
                    }
                    $order = !empty($_REQUEST['order']) && $_REQUEST['order'] === $order_a ? $order_b : $order_a;

                    $page_number_string = !empty($_REQUEST[self::DEFALUT_QUERY_ARG_PAGE_NUMBER]) ? "&" . self::DEFALUT_QUERY_ARG_PAGE_NUMBER . "=" . $this->page_number : "";

                    echo "<a href='?orderby=$orderby&order={$order}{$page_number_string}'>$column</a>";
                } else {
                    echo $column;
                }

                echo "</th>";
            }
        }
    }

    public function display() {
        ?>
        <table>
            <thead>
                <tr>
        <?php $this->print_column_headers(); ?>
                </tr>
            </thead>
            <tbody>
        <?php $this->display_rows_or_placeholder(); ?>
            </tbody>
        </table>
                    <?php
                    $this->display_pagination();
                }

                public function display_rows_or_placeholder() {
                    if ($this->has_items()) {
                        $this->display_rows();
                    } else {
                        echo '<tr><td>';
                        $this->no_items();
                        echo '</td></tr>';
                    }
                }

                /**
                 * Generate the table rows
                 *
                 * @since 3.1.0
                 */
                public function display_rows() {
                    foreach ($this->items as $item)
                        $this->single_row($item);
                }

                public function has_items() {
                    return !empty($this->items);
                }

                /**
                 * Message to be displayed when there are no items
                 *
                 * @since 3.1.0
                 */
                public function no_items() {
                    _e('No items found.');
                }

                public function single_row($item) {
                    echo '<tr>';
                    $this->single_row_columns($item);
                    echo '</tr>';
                }

                public function single_row_columns($item) {
                    $columns = $this->get_columns();
                    $allowed_columns = $this->get_allowed_columns();




                    foreach ($columns as $column_name => $value) {
                        if (in_array($column_name, $allowed_columns)) {
                            echo "<td>" . $this->column_default($item, $column_name) . "</td>";
                        }
                    }
                }

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

            }
            