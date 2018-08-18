<?php

namespace User_Login_History\Inc\Admin;
use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\DbHelper;
use User_Login_History\Inc\Common\Helpers\TemplateHelper;
use User_Login_History\Inc\Common\Helpers\DateTimeHelper;
use User_Login_History\Inc\Admin\LoginTracker;

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
final class LoginListTable extends \User_Login_History\Inc\Common\Abstracts\ListTableAbstract {

    private $plugin_table;
    
    
    public function __construct($plugin_name, $version, $plugin_text_domain  ) {
            $args = array(
                'singular' => $plugin_name . '_user_login', //singular name of the listed records
                'plural' => $plugin_name . '_user_logins', //plural name of the listed records
            );
            parent::__construct($plugin_name, $version, $plugin_text_domain, $args);
            $this->plugin_table = NS\PLUGIN_TABLE_FA_USER_LOGINS;
        }
        
        private function prepare_where_query() {
            return '';
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
            $table = $wpdb->prefix . $this->plugin_table;
               $sql = " SELECT"
                    . " FaUserLogin.*, "
                    . " UserMeta.meta_value, TIMESTAMPDIFF(SECOND,FaUserLogin.time_login,FaUserLogin.time_last_seen) as duration"
                    . " FROM " . $table . "  AS FaUserLogin"
                    . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key LIKE  '".$wpdb->prefix."capabilities' )"
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

          return DbHelper::get_results($sql);
        }

        /**
         * Returns the count of records in the database.
         * 
         * @access   public
         * @return null|string
         */
        public function record_count() {
            global $wpdb;
            $table = $wpdb->prefix . $this->plugin_table;
             $sql = " SELECT"
                    . " COUNT(FaUserLogin.id) AS total"
                    . " FROM " . $table . " AS FaUserLogin"
                    . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key LIKE '".$wpdb->prefix."capabilities' )"
                    . " WHERE 1 ";

            $where_query = $this->prepare_where_query();

            if ($where_query) {
                $sql .= $where_query;
            }

           return DbHelper::get_var($sql);
        }
        
        
           public function get_columns() {
          $columns = array(
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
                'duration' => array('duration', false),
            );
           
            return $columns;
        }
        
        
          public function column_default($item, $column_name) {
            $timezone = $this->get_table_timezone();
         
           
            $new_column_data = apply_filters('manage_faulh_admin_custom_column', '', $item, $column_name);
            $country_code = in_array(strtolower($item['country_code']), array("", $this->unknown_string)) ? $this->unknown_string : $item['country_code'];

            switch ($column_name) {

                case 'user_id':
                    if (empty($item[$column_name])) {
                        return $this->unknown_symbol;
                    }
                    return (int) $item[$column_name];

                case 'username_csv':
                    return $item['username'];

                case 'role':
                    if (empty($item['user_id'])) {
                        return $this->unknown_symbol;
                    }

                    if (is_network_admin() && !empty($item['blog_id'])) {
                        switch_to_blog($item['blog_id']);
                        $user_data = get_userdata($item['user_id']);
                        restore_current_blog();
                    } else {
                        $user_data = get_userdata($item['user_id']);
                    }

                    return !empty($user_data->roles) ? esc_html(implode(',', $user_data->roles)) : $this->unknown_symbol;

                case 'old_role':
                    return !empty($item[$column_name]) ? esc_html($item[$column_name]) : $this->unknown_symbol;

                case 'browser':
                    if (in_array(strtolower($item[$column_name]), array("", $this->unknown_string))) {
                        return $this->unknown_string;
                    }

                    if (empty($item['browser_version'])) {
                        return esc_html($item[$column_name]);
                    }

                    return esc_html($item[$column_name] . " (" . $item['browser_version'] . ")");

                case 'time_login':
                    if (!(strtotime($item[$column_name]) > 0)) {
                        return $this->unknown_symbol;
                    }
                    $time_login = DateTimeHelper::convert_format(DateTimeHelper::convert_timezone($item[$column_name], '', $timezone));
                    return $time_login ? $time_login : $this->unknown_symbol;

                case 'time_logout':
                    if (empty($item['user_id']) || !(strtotime($item[$column_name]) > 0)) {
                        return $this->unknown_symbol;
                    }
                    $time_logout = DateTimeHelper::convert_format(DateTimeHelper::convert_timezone($item[$column_name], '', $timezone));
                    return $time_logout ? $time_logout : $this->unknown_symbol;
                case 'ip_address':
                    return !empty($item[$column_name]) ? esc_html($item[$column_name]) : $this->unknown_string;

                case 'timezone':
                    return in_array(strtolower($item[$column_name]), array("", $this->unknown_string)) ? $this->unknown_string : esc_html($item[$column_name]);


                case 'country_name':
                    return in_array(strtolower($item[$column_name]), array("", $this->unknown_string)) ? $this->unknown_string : esc_html($item[$column_name] . "(" . $country_code . ")");

                case 'country_name_csv':
                    return in_array(strtolower($item['country_name']), array("", $this->unknown_string)) ? $this->unknown_string : esc_html($item['country_name']);

                case 'country_code':
                    return esc_html($country_code);

                case 'operating_system':
                    return in_array(strtolower($item[$column_name]), array("", $this->unknown_string)) ? $this->unknown_string : esc_html($item[$column_name]);

                case 'time_last_seen':

                    $time_last_seen_unix = strtotime($item[$column_name]);
                    if (empty($item['user_id']) || !($time_last_seen_unix > 0)) {
                        return $this->unknown_symbol;
                    }
                    $time_last_seen = DateTimeHelper::convert_format(DateTimeHelper::convert_timezone($item[$column_name], '', $timezone));
                      

                    if (!$time_last_seen) {
                        return $this->unknown_symbol;
                    }

                    $human_time_diff = human_time_diff($time_last_seen_unix);
                    $is_online_str = 'offline';

                    if (in_array($item['login_status'], array("", LoginTracker::LOGIN_STATUS_LOGIN))) {
                        $minutes = ((time() - $time_last_seen_unix) / 60);
                        $settings = get_option($this->plugin_name . "_basics");
                        $minute_online = !empty($settings['is_status_online']) ? absint($settings['is_status_online']) : NS\DEFAULT_IS_STATUS_ONLINE_MIN;
                        $minute_idle = !empty($settings['is_status_idle']) ? absint($settings['is_status_idle']) : NS\DEFAULT_IS_STATUS_IDLE_MIN;
                        if ($minutes <= $minute_online) {
                            $is_online_str = 'online';
                        } elseif ($minutes <= $minute_idle) {
                            $is_online_str = 'idle';
                        }
                    }


                    return "<div class='is_status_$is_online_str' title = '$time_last_seen'>" . $human_time_diff . " " . esc_html__('ago', 'faulh') . '</div>';

                case 'user_agent':
                    return !empty($item[$column_name]) ? esc_html($item[$column_name]) : $this->unknown_symbol;

                case 'duration':
                    return human_time_diff(strtotime($item['time_login']), strtotime($item['time_last_seen']));

                case 'login_status':
                    $login_statuses = TemplateHelper::login_statuses();
                    return !empty($login_statuses[$item[$column_name]]) ? $login_statuses[$item[$column_name]] : $this->unknown_string;

                case 'blog_id':
                    return !empty($item[$column_name]) ? (int) $item[$column_name] : $this->unknown_symbol;

                case 'is_super_admin':
                    $super_admin_statuses = TemplateHelper::super_admin_statuses();
                    return $super_admin_statuses[$item[$column_name] ? 'yes' : 'no'];

                default:
                    if ($new_column_data) {
                        return $new_column_data;
                    }
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }
        
        
        /**
         * Method for name column
         * 
         * @access   public
         * @param array $item an array of DB data
         * @return string
         */
        function column_username($item) {
            if(empty($item['user_id']))
            {
                $title =  esc_html($item['username']);
            }
            else{
            $edit_link = get_edit_user_link($item['user_id']);
            $title = !empty($edit_link) ? "<a href='" .$edit_link. "'>" . esc_html($item['username']) . "</a>" : '<strong>' . esc_html($item['username']) . '</strong>';
            }
            
          $delete_nonce = wp_create_nonce($this->plugin_name . 'delete_row_by_' . $this->_args['singular']);
            $actions = array(
                'delete' => sprintf('<a href="?page=%s&action=%s&record_id=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), $this->plugin_name . '_admin_listing_table_delete_single_row', absint($item['id']), $delete_nonce),
            );
            return $title . $this->row_actions($actions);
        }

}
