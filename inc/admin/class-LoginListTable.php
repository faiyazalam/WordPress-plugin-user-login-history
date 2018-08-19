<?php

namespace User_Login_History\Inc\Admin;
use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\DbHelper;



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
