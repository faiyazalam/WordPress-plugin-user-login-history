<?php

/**
 * This abstract class can help to create admin listing table.
 *
 * @link       https://github.com/faiyazalam
 * 
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if (!class_exists('Faulh_Abstract_List_Table')) {

    abstract class Faulh_Abstract_List_Table extends WP_List_Table {

        /**
         * Default timezone for the table.
         */
        const DEFAULT_TABLE_TIMEZONE = 'UTC';

        /**
         * The unique identifier of this plugin.
         *
         * @access   protected
         * @var      string    $plugin_name    The string used to uniquely identify this plugin.
         */
        protected $plugin_name;

        /**
         * The name of the table.
         *
         * @access   protected
         * @var      string    $table_name
         */
        protected $table_name;

        /**
         * Holds the timezone to be used in table.
         *
         * @access   private
         * @var      string    $table_timezone
         */
        private $table_timezone;

        /**
         * Initialize the class and set its properties.
         *
         * @access public
         * @param      array    $args       The overridden arguments.
         * @param      string    $plugin_name       The name of this plugin.
         * @param      string    $table_name    The table name.
         * @param      string    $table_timezone   The timezone for table.
         */
        public function __construct($args = array(), $plugin_name, $table_name, $table_timezone = '') {
            parent::__construct($args);
            $this->plugin_name = $plugin_name;
            $this->table_name = $table_name; //main table of the plugin
            $this->set_table_timezone($table_timezone);
        }

        /**
         * Message to be displayed when there are no items
         *
         * @access public
         */
        public function no_items() {
            esc_html_e('No records avaliable.', 'faulh');
        }

        /**
         * Sets the timezone to be used for table listing.
         * 
         * @access public
         * @param string $timezone
         */
        public function set_table_timezone($timezone = '') {
            $this->table_timezone = $timezone;
        }

        /**
         * Gets the timezone to be used for table listing.
         * 
         * @access public
         * @return string
         */
        public function get_table_timezone() {
            return $this->table_timezone ? $this->table_timezone : self::DEFAULT_TABLE_TIMEZONE;
        }

        /**
         * Timezone edit link
         * 
         * @return string
         */
        public function table_timezone_edit() {
            return esc_html__('This table is showing time in the timezone', 'faulh') . " - <strong>" . $this->get_table_timezone() . "</strong>&nbsp;<span><a class='' href='" . get_edit_user_link() . "#" . $this->plugin_name . "'>" . esc_html__('Edit', 'faulh') . "</a></span>";
        }

        /**
         * Prepares the where query.
         *
         * @access public
         * @return string
         */
        public function prepare_where_query() {
            
            $where_query = '';

            $fields = array(
                'user_id',
                'username',
                'browser',
                'operating_system',
                'ip_address',
                'timezone',
                'country_name',
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

            if (isset($_GET['is_super_admin'])) {
                $is_super_admin = $_GET['is_super_admin'];
               
                if ('1' == $is_super_admin) {
                    $where_query .= " AND `FaUserLogin`.`is_super_admin` = '1'";
                } elseif ('0' == $is_super_admin) {
                    $where_query .= " AND `FaUserLogin`.`is_super_admin` = '0'";
                }
            }

            $where_query = apply_filters('faulh_admin_prepare_where_query', $where_query);
            return $where_query;
        }

        /**
         * Deletes all records from the plugin table.
         * 
         * @access public
         */
        public function delete_all_rows() {
            global $wpdb;
            $table = $wpdb->prefix . $this->table_name;
            $status = $wpdb->query("TRUNCATE $table");
            if ($wpdb->last_error) {
                Faulh_Error_Handler::error_log($wpdb->last_error . " " . $wpdb->last_query, __LINE__, __FILE__);
            }
            return $status;
        }

        /**
         * Render a column when no column specific method exist.
         *
         * @access public
         * @param array $item
         * @param string $column_name
         *
         * @return mixed
         */
        public function column_default($item, $column_name) {
            $timezone = $this->get_table_timezone();
            $unknown = 'unknown';
            $new_column_data = apply_filters('manage_faulh_admin_custom_column', '', $item, $column_name);

            switch ($column_name) {
                case 'user_id':
                    if (!$item[$column_name]) {
                        return $unknown;
                    }
                    return $item[$column_name] ? $item[$column_name] : $unknown;
                case 'role':
                    if (!$item['user_id']) {
                        return $unknown;
                    }
                    $user_data = get_userdata($item['user_id']);
                    return !empty($user_data->roles) ? implode(',', $user_data->roles) : $unknown;
                case 'old_role':
                    return $item[$column_name] ? $item[$column_name] : $unknown;
                case 'browser':
                    return $item[$column_name] ? $item[$column_name] . "<br>(" . $item['browser_version'] . ")" : $unknown;
                case 'time_login':
                    return Faulh_Date_Time_Helper::convert_format(Faulh_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone));
                case 'time_logout':
                    if (!$item['user_id']) {
                        return $unknown;
                    }
                    return strtotime($item[$column_name]) > 0 ? Faulh_Date_Time_Helper::convert_format(Faulh_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone)) : esc_html__('Logged In', 'faulh');
                case 'ip_address':
                    return $item[$column_name] ? esc_html($item[$column_name]) : $unknown;

                case 'timezone':
                    return $item[$column_name] ? esc_html($item[$column_name]) : $unknown;

                case 'country_name':
                    $country_code = empty($item['country_code']) || $unknown == strtolower($item['country_code']) ? $unknown : $item['country_code'];
                    return in_array(strtolower($item[$column_name]), array("", $unknown)) ? $unknown : esc_html($item[$column_name] . "(" . $country_code . ")");
                case 'country_code':
                    return empty($item['country_code']) || $unknown == strtolower($item['country_code']) ? $unknown : esc_html($item['country_code']);


                case 'operating_system':
                    return $item[$column_name] ? $item[$column_name] : $unknown;

                case 'time_last_seen':
                    if (!$item['user_id']) {
                        return $unknown;
                    }
                    $time_last_seen_unix = strtotime($item[$column_name]);
                    $time_last_seen = Faulh_Date_Time_Helper::convert_format(Faulh_Date_Time_Helper::convert_timezone($item[$column_name], '', $timezone));
                    $human_time_diff = human_time_diff($time_last_seen_unix);
                    $is_online_str = 'offline';
                    if (Faulh_User_Tracker::LOGIN_STATUS_LOGIN == $item['login_status']) {
                        $minutes = ((time() - $time_last_seen_unix) / 60);
                        $settings = get_option($this->plugin_name . "_basics");
                        $minute_online = !empty($settings['is_status_online']) ? $settings['is_status_online'] : FAULH_DEFAULT_IS_STATUS_ONLINE_MIN;
                        $minute_idle = !empty($settings['is_status_idle']) ? $settings['is_status_idle'] : FAULH_DEFAULT_IS_STATUS_IDLE_MIN;
                        if ($minutes <= $minute_online) {
                            $is_online_str = 'online';
                        } elseif ($minutes <= $minute_idle) {
                            $is_online_str = 'idle';
                        }
                    }

                    return "<div class='is_status_$is_online_str' title = '$time_last_seen'>" . $human_time_diff . " " . esc_html__('ago', 'faulh') . '</div>';
                case 'user_agent':
                    return $item[$column_name] ? esc_html($item[$column_name]) : $unknown;
                case 'duration':
                    $duration = human_time_diff(strtotime($item['time_login']), strtotime(Faulh_Date_Time_Helper::get_last_time($item['time_logout'], $item['time_last_seen'])));
                    return $duration ? $duration : $unknown;
                case 'login_status':
                    $login_statuses = Faulh_Template_Helper::login_statuses();
                    return !empty($login_statuses[$item[$column_name]]) ? $login_statuses[$item[$column_name]] : $unknown;

                case 'site_id':
                    return $item[$column_name] ? $item[$column_name] : $unknown;

                case 'blog_id':
                    return $item[$column_name] ? $item[$column_name] : $unknown;

                case 'is_super_admin':
                    $super_admin_statuses = Faulh_Template_Helper::super_admin_statuses();
                    return !empty($super_admin_statuses[$item[$column_name]]) ? $super_admin_statuses[$item[$column_name]] : $unknown;

                default:
                    if ($new_column_data) {
                        return $new_column_data;
                    }
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }

        /**
         *  Associative array of columns
         *
         * @access public
         * @return array
         */
        public function get_columns() {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'user_id' => esc_html__('User Id', 'faulh'),
                'username' => esc_html__('Username', 'faulh'),
                'role' => esc_html__('Current Role', 'faulh'),
                'old_role' => "<span title='" . esc_attr__('Role while user gets logged in', 'faulh') . "'>" . esc_html__('Old Role (?)', 'faulh') . "</span>",
                'ip_address' => esc_html__('IP Address', 'faulh'),
                'country_name' => "<span title='" . esc_attr__('To track country name, Geo Tracker setting must be enabled.', 'faulh') . "'>" . esc_html__('Country (?)', 'faulh') . "</span>",
                'browser' => esc_html__('Browser', 'faulh'),
                'operating_system' => esc_html__('Operating System', 'faulh'),
                'timezone' => "<span title='" . esc_attr__('To track timezone, "Geo Tracker" setting must be enabled.', 'faulh') . "'>" . esc_html__('IP Address (?)', 'faulh') . "</span>",
                'user_agent' => esc_html__('User Agent', 'faulh'),
                'duration' => esc_html__('Duration', 'faulh'),
                'time_last_seen' => "<span title='" . esc_attr__('Last seen time in the session', 'faulh') . "'>" . esc_html__('Last Seen (?)', 'faulh') . "</span>",
                'time_login' => esc_html__('Login', 'faulh'),
                'time_logout' => esc_html__('Logout', 'faulh'),
                'login_status' => esc_html__('Login Status', 'faulh'),
            );

            if (is_network_admin()) {
                $columns['blog_id'] = esc_html__('Blog ID', 'faulh');
                $columns['is_super_admin'] = esc_html__('Super Admin', 'faulh');
            }
            $columns = apply_filters('faulh_admin_get_columns', $columns);
            return $columns;
        }

        /**
         * Columns to make sortable.
         *
         * @access public
         * @return array
         */
        public function get_sortable_columns() {
            $sortable_columns = array(
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
                'duration' => array('duration', false),
            );
            if (is_multisite()) {
                $sortable_columns['is_super_admin'] = array('is_super_admin', false);
                $sortable_columns['blog_id'] = array('blog_id', false);
            }
            $sortable_columns = apply_filters('faulh_admin_get_sortable_columns', $sortable_columns);
            return $sortable_columns;
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
         * Handles data query and filter, sorting, and pagination.
         * 
         * @access public
         */
        public function prepare_items() {
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();

            // here we configure table headers, defined in our methods
            $this->_column_headers = array($columns, $hidden, $sortable);
            $per_page = 20; //$this->get_items_per_page();
            $current_page = $this->get_pagenum();
            $total_items = $this->record_count();

            $this->set_pagination_args(array(
                'total_items' => $total_items,
                'per_page' => $per_page
            ));

            $this->items = $this->get_rows($per_page, $current_page);
        }

        /**
         * Generates content for a single row of the table
         * Over-ridden method.
         * 
         * @access public
         */
        public function single_row($item) {
            $login_status = !empty($item['login_status']) ? "login_status_" . $item['login_status'] : "";
            echo "<tr class='$login_status'>";
            $this->single_row_columns($item);
            echo '</tr>';
        }

        /**
         * Exports CSV
         * 
         * @access public
         * @global type $current_user
         */
        public function export_to_CSV() {
            global $current_user;
            $timezone = $this->get_table_timezone();
            $data = $this->get_rows(0); // pass zero to get all the records
            //date string to suffix the file nanme: month - day - year - hour - minute
            $suffix = $this->plugin_name . "_" . date('n-j-y_H-i');
            // send response headers to the browser
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=' . $suffix . '.csv');

            if (!$data) {
                $this->no_items();
                exit;
            }

            $fp = fopen('php://output', 'w');
            $i = 0;
            $record = array();
            foreach ($data as $row) {
                $record['user_id'] = $this->column_default($row, 'user_id');
                $record['current_role'] = $this->column_default($row, 'role');
                $record['old_role'] = $this->column_default($row, 'old_role');
                $record['ip_address'] = $this->column_default($row, 'ip_address');
                $record['browser'] = $this->column_default($row, 'browser');
                $record['operating_system'] = $this->column_default($row, 'operating_system');
                $record['country_code'] = $this->column_default($row, 'country_code');
                $record['country_name'] = $this->column_default($row, 'country_name');
                $record['timezone'] = $this->column_default($row, 'timezone');
                $record['duration'] = $this->column_default($row, 'duration');
                $time_last_seen = $row['time_last_seen'];
                $human_time_diff = human_time_diff(strtotime($time_last_seen));
                $time_last_seen = Faulh_Date_Time_Helper::convert_format(Faulh_Date_Time_Helper::convert_timezone($time_last_seen, '', $timezone));
                $record['time_last_seen'] = $human_time_diff . " " . esc_html__('ago', 'faulh') . " ($time_last_seen)";
                $record['time_login'] = $this->column_default($row, 'time_login');
                $record['time_logout'] = $this->column_default($row, 'time_logout');
                $record['login_status'] = $this->column_default($row, 'login_status');
                $record['user_agent'] = $this->column_default($row, 'user_agent');
                if (is_multisite()) {
                    $record['is_super_admin'] = $this->column_default($row, 'is_super_admin');
                    $record['blog_id'] = $this->column_default($row, 'blog_id');
                }
                //output header row
                if (0 == $i) {
                    fputcsv($fp, array_keys($record));
                }
                fputcsv($fp, $record);
                $i++;
            }
            fclose($fp);
            die();
        }

        /**
         * Delete a record from the plugin table.
         *
         * @access public
         * @param int $id The record ID
         */
        public function delete_rows($ids = array()) {
            global $wpdb;
            $table = $wpdb->prefix . $this->table_name;
            if (!empty($ids)) {
                if (!is_array($ids)) {
                    $ids = array($ids);
                }
                $ids = esc_sql(implode(',', array_map('absint', $ids)));
                $status = $wpdb->query("DELETE FROM $table WHERE id IN($ids)");
                if ($wpdb->last_error) {
                    Faulh_Error_Handler::error_log($wpdb->last_error . " " . $wpdb->last_query, __LINE__, __FILE__);
                }
                return $status;
            }
            return FALSE;
        }

    }

}

