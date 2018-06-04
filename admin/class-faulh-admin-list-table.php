<?php

/**
 * This is used to create admin listing table.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 * @access private
 */
if (!class_exists('Faulh_Admin_List_Table')) {

    class Faulh_Admin_List_Table extends Faulh_Abstract_List_Table {

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
            $defaults = array(
                'singular' => $plugin_name . '_admin_user', //singular name of the listed records
                'plural' => $plugin_name . '_admin_users', //plural name of the listed records
            );
            parent::__construct(wp_parse_args($args, $defaults), $plugin_name, $table_name, $table_timezone);
            $this->set_capability_string_by_blog_id();
        }

        /**
         * Render the bulk edit checkbox
         * 
         * @access   public
         * @param array $item
         * @return string
         */
        public function column_cb($item) {
            return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']);
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
           
            $table = $wpdb->prefix . $this->table_name;
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

       
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            if ("" != $wpdb->last_error) {
                Faulh_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            }
            return $result;
        }

        /**
         * Returns the count of records in the database.
         * 
         * @access   public
         * @return null|string
         */
        public function record_count() {
            global $wpdb;
            $table = $wpdb->prefix . $this->table_name;
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
 
            $result = $wpdb->get_var($sql);
            if ("" != $wpdb->last_error) {
                Faulh_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
            }
            return $result;
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

        /**
         * Check form submission and then 
         * process the bulk operation.
         * 
         * @access   public 
         * @return boolean
         */
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

        /**
         * Check action and nonce and then 
         * delete a single record from table.
         * 
         * @access   public
         * @return boolean
         */
        public function delete_single_row() {
            if (empty($_GET['action']) || $this->plugin_name . '_admin_listing_table_delete_single_row' != $_GET['action'] || empty($_REQUEST['_wpnonce'])) {
                return FALSE;
            }

            $nonce = $_GET['_wpnonce'];

            if (!wp_verify_nonce($nonce, $this->plugin_name . 'delete_row_by_' . $this->_args['singular'])) {
                return FALSE;
            }

            return $this->delete_rows($_GET['record_id']);
        }

    }

}

