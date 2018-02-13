<?php
/**
 * This is used to create listing table.
 * 
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 * @access private
 */
if (!class_exists('Faulh_Public_List_Table')) {

    class Faulh_Public_List_Table {

        const DEFALUT_LIMIT = 20;
        const DEFALUT_PAGE_NUMBER = 1;
        const DEFALUT_QUERY_ARG_PAGE_NUMBER = 'pagenum';
        const DEFAULT_TABLE_TIMEZONE = 'UTC';

        private $limit;
        private $page_number;
        private $pagination_links;
        private $items;
        private $table;
        private $plugin_name;
        private $options;
        private $allowed_columns;
        private $table_timezone;
        private $table_date_format;
        private $table_time_format;

        public function __construct($plugin_name) {
            $this->plugin_name = $plugin_name;
            $this->page_number = !empty($_REQUEST[self::DEFALUT_QUERY_ARG_PAGE_NUMBER]) ? absint($_REQUEST[self::DEFALUT_QUERY_ARG_PAGE_NUMBER]) : self::DEFALUT_PAGE_NUMBER;
            $this->set_table_name();
            $this->set_table_timezone();
        }

        public function get_table_date_time_format() {
            return $this->get_table_date_format() . " " . $this->get_table_time_format();
        }

        public function set_table_date_format($format = "") {
            $this->table_date_format = $format;
        }

        public function get_table_date_format() {
            return !empty($this->table_date_format) ? $this->table_date_format : "";
        }

        public function set_table_time_format($format = "") {
            $this->table_time_format = $format;
        }

        public function get_table_time_format() {
            return !empty($this->table_time_format) ? $this->table_time_format : "";
        }

        private function set_table_name() {
            global $wpdb;
            $this->table = $wpdb->prefix . FAULH_TABLE_NAME;
        }

        public function set_limit($limit = false) {
            $this->limit = $limit ? absint($limit) : self::DEFALUT_LIMIT;
        }

        public function prepare_items() {
            $this->items = $this->get_rows();

            $this->pagination_links = paginate_links(array(
                'base' => add_query_arg(self::DEFALUT_QUERY_ARG_PAGE_NUMBER, '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;', 'faulh'),
                'next_text' => __('&raquo;', 'faulh'),
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
                'ip_address',
                'timezone',
                'country_name',
                'browser',
                'operating_system',
                'login_status',
            );

            foreach ($fields as $field) {
                if (!empty($_GET[$field])) {
                    $where_query .= " AND `FaUserLogin`.`$field` = '" . esc_sql($_GET[$field]) . "'";
                }
            }

            if (!empty($_GET['role'])) {

                if ('superadmin' == $_GET['role']) {
                    $site_admins = get_super_admins();
                    $site_admins_str = implode("', '", $site_admins);
                    $where_query .= " AND `FaUserLogin`.`username` IN ('$site_admins_str')";
                } else {
                    $where_query .= " AND `UserMeta`.`meta_value` LIKE '%" . esc_sql($_GET['role']) . "%'";
                }
            }

            if (!empty($_GET['old_role'])) {
                if ('superadmin' == $_GET['old_role']) {
                    $where_query .= " AND `FaUserLogin`.`is_super_admin` LIKE '1'";
                } else {
                    $where_query .= " AND `FaUserLogin`.`old_role` LIKE '%" . esc_sql($_GET['old_role']) . "%'";
                }
            }

            if (!empty($_GET['date_type'])) {
                $date_type = esc_sql($_GET['date_type']);

                if (in_array($date_type, array('login', 'logout', 'last_seen'))) {
                    if (!empty($_GET['date_from'])) {
                        $where_query .= " AND `FaUserLogin`.`time_$date_type` >= '" . esc_sql($_GET['date_from']) . " 00:00:00'";
                    }

                    if (!empty($_GET['date_to'])) {
                        $where_query .= " AND `FaUserLogin`.`time_$date_type` <= '" . esc_sql($_GET['date_to']) . " 23:59:59'";
                    }
                }
            }

            $where_query = apply_filters('faulh_public_prepare_where_query', $where_query);
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
        public function get_rows() {
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
            //   $sql .= ' GROUP BY FaUserLogin.id';

            if (!empty($_REQUEST['orderby'])) {
                $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
                $sql .=!empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
            } else {
                $sql .= ' ORDER BY FaUserLogin.time_login DESC';
            }

            if ($this->limit > 0) {
                $sql .= " LIMIT $this->limit";
                $sql .= ' OFFSET   ' . ( $this->page_number - 1 ) * $this->limit;
                ;
            }
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            if ("" != $wpdb->last_error) {

                Faulh_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            }

            return $result;
        }

        /**
         * Count the records.
         * 
         * @global type $wpdb
         * @return string The number of records found.
         */
        public function record_count() {
            global $wpdb;
            $sql = " SELECT"
                    . " COUNT(FaUserLogin.id) as total "
                    . " FROM " . $this->table . "  AS FaUserLogin"
                    . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                    . " WHERE 1 ";

            $where_query = $this->prepare_where_query();
            if ($where_query) {
                $sql .= $where_query;
            }
            //  $sql .= ' GROUP BY FaUserLogin.id';
            $result = $wpdb->get_var($sql);
            if ("" != $wpdb->last_error) {
                Faulh_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            }
            return $result;
        }

        public function get_columns() {
            $columns = array(
                'user_id' => __('User Id', 'faulh'),
                'username' => __('Username', 'faulh'),
                'role' => __('Current Role', 'faulh'),
                'old_role' => __('<span title="Role while user gets loggedin">Old Role(?)</span>', 'faulh'),
                'ip_address' => __('IP Address', 'faulh'),
                'browser' => __('Browser', 'faulh'),
                'operating_system' => __('Platform', 'faulh'),
                'country_name' => __('Country', 'faulh'),
                'duration' => __('Duration', 'faulh'),
                'time_last_seen' => __('<span title="Last seen time in the session">Last Seen(?)</span>', 'faulh'),
                'timezone' => __('Timezone', 'faulh'),
                'time_login' => __('Login', 'faulh'),
                'time_logout' => __('Logout', 'faulh'),
                'user_agent' => __('User Agent', 'faulh'),
                'login_status' => __('Login Status', 'faulh'),
            );

            $columns = apply_filters('faulh_public_get_columns', $columns);
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
              //  'ip_address' => array('ip_address', false),
                'browser' => array('browser', true),
                'operating_system' => array('operating_system', false),
                'country_name' => array('country_name', false),
                'time_last_seen' => array('time_last_seen', false),
                'timezone' => array('timezone', false),
                'user_agent' => array('user_agent', false),
                'login_status' => array('login_status', false),
            );

            $sortable_columns = apply_filters('faulh_public_get_sortable_columns', $sortable_columns);
            return $sortable_columns;
        }

        private function display_pagination() {
            echo $this->pagination_links;
        }

        public function get_allowed_columns() {
            return $this->allowed_columns;
        }

        public function set_allowed_columns($columns = array()) {
            $columns = is_array($columns) ? $columns : (is_string($columns) ? explode(',', $columns) : array());
            $this->allowed_columns = array_map('trim', $columns);
            return $this->allowed_columns;
        }

        public function print_column_headers() {
            $allowed_columns = $this->get_allowed_columns();
            //log the exception
            if (empty($allowed_columns)) {
                Faulh_Error_Handler::error_log("No columns is selected for frontend listing table.", __LINE__, __FILE__);
                $this->pagination_links = ""; //disable pagination link.
                return;
            }
            $columns = $this->get_columns();
            $order_by_key = 'orderby';
            $sortable_columns = $this->get_sortable_columns();

            foreach ($columns as $key => $column) {
                $direction = '';
//print only allowed column headers
                if (in_array($key, $allowed_columns)) {
                    echo "<th>";
//add sorting link to sortable column only
                    if (isset($sortable_columns[$key])) {
                        //set query value for query param 'orderby'
                        $sortable_column_name = !empty($sortable_columns[$key][0]) ? $sortable_columns[$key][0] : "";
                        $orderby = $sortable_column_name ? $sortable_column_name : $key;
                        //find default direction for the current column
                        if (isset($sortable_columns[$key][1]) && $sortable_columns[$key][1]) {
                            $order_a = 'asc';
                            $order_b = 'desc';
                        } else {
                            $order_a = 'desc';
                            $order_b = 'asc';
                        }

                        $requested_order = !empty($_GET['order']) ? $_GET['order'] : "";
                        //reverse the order
                        $order = $requested_order === $order_a ? $order_b : $order_a;
                        $page_number_string = !empty($_GET[self::DEFALUT_QUERY_ARG_PAGE_NUMBER]) ? "&" . self::DEFALUT_QUERY_ARG_PAGE_NUMBER . "=" . $this->page_number : "";

                        if (isset($_GET[$order_by_key]) && $sortable_column_name == $_GET[$order_by_key]) {
                            $direction = $requested_order;
                        }
                        echo "<a href='?$order_by_key=$orderby&order={$order}{$page_number_string}'>$column<span class='sorting_hover_$order'>($order)</span><span class='sorting $direction'>$direction</span></a>";
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

                public function set_table_timezone($timezone = '') {
                    $this->table_timezone = $timezone;
                }

                public function get_table_timezone() {
                    return $this->table_timezone ? $this->table_timezone : self::DEFAULT_TABLE_TIMEZONE;
                }

                public function column_default($item, $column_name) {
                    $timezone = $this->get_table_timezone();
                    $unknown = 'unknown';
                    $new_column_data = apply_filters('manage_faulh_public_custom_column', '', $item, $column_name);
                 
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
                            return Faulh_Date_Time_Helper::convert_format(Faulh_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone), $this->get_table_date_time_format());
                        case 'time_logout':
                            if (!$item['user_id']) {
                                return $unknown;
                            }
                            return strtotime($item[$column_name]) > 0 ? Faulh_Date_Time_Helper::convert_format(Faulh_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone), $this->get_table_date_time_format()) : __('Logged In', 'faulh');
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
                            $time_last_seen = Faulh_Date_Time_Helper::convert_format(Faulh_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone), $this->get_table_date_time_format());
                            $human_time_diff = human_time_diff(strtotime($item[$column_name]));
                            return "<span title = '$time_last_seen'>" . $human_time_diff . " " . __('ago', 'faulh') . '</span>';
                        case 'user_agent':
                            return $item[$column_name] ? $item[$column_name] : $unknown;
                        case 'duration':
                            $duration = human_time_diff(strtotime($item['time_login']), strtotime(Faulh_Date_Time_Helper::get_last_time($item['time_logout'], $item['time_last_seen'])));
                            return $duration ? $duration : $unknown;
                        case 'login_status':
                            return $item[$column_name] ? $item[$column_name] : $unknown;
                        case 'site_id':
                            return $item[$column_name] ? $item[$column_name] : $unknown;
                        case 'blog_id':
                            return $item[$column_name] ? $item[$column_name] : $unknown;
                        case 'is_super_admin':
                            return $item[$column_name] ? __('Yes', 'faulh') : __('No', 'faulh');
                        default:
                            if ($new_column_data) {
                                return $new_column_data;
                            }
                            return print_r($item, true); //Show the whole array for troubleshooting purposes
                    }
                }

            }

        }

