<?php

/**
 * This is used to create network admin listing table.
 * 
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 * @access private
 */
if(!class_exists('Faulh_Network_Admin_List_Table'))
{
  class Faulh_Network_Admin_List_Table extends Faulh_Abstract_List_Table {

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
            'singular' => $plugin_name.'_admin_user', //singular name of the listed records
            'plural' => $plugin_name.'_admin_users', //plural name of the listed records
        );
        parent::__construct(wp_parse_args($args, $defaults), $plugin_name, $table_name, $table_timezone);
    }

    /**
     * Get blog ids of the current network.
     * 
     * @access private
     * @global type $wpdb
     * @return array
     */
    private function get_current_network_blog_ids() {
        return Faulh_DB_Helper::get_blog_by_id_and_network_id(!empty($_GET['blog_id']) ? $_GET['blog_id'] : NULL, get_current_network_id());
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     * @access public
     * @return string
     */
    public function column_cb($item) {
        $blog_id = !empty($item['blog_id']) ? $item['blog_id'] : 0;
        return sprintf(
                '<input type="checkbox" name="bulk-delete[blog_id][%s][]" value="%s" />', $blog_id, $item['id']
        );
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
        $where_query = $this->prepare_where_query();
        $get_values = array();
        $table_usermeta = $wpdb->usermeta;
        $i = 0;
        $sql = "";
        $blog_ids = $this->get_current_network_blog_ids();

        foreach ($blog_ids as $blog_id) {
            $table = $wpdb->get_blog_prefix($blog_id) . $this->table_name;

            if (0 < $i) {
                $sql .= " UNION ALL";
            }

            $sql .= " SELECT"
                    . " FaUserLogin.*,"
                    . " UserMeta.meta_value, TIMESTAMPDIFF(SECOND,FaUserLogin.time_login,FaUserLogin.time_last_seen) as duration,"
                    . " $blog_id as blog_id"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $table_usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                    . " WHERE 1 ";

            if ($where_query) {
                $sql .= $where_query;
            }

            //   $sql .= ' GROUP BY FaUserLogin.id ';
            $i++;
        }

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
        $get_values = array();
        $where_query = $this->prepare_where_query();
        $table_usermeta = $wpdb->usermeta;
        $table_users = $wpdb->users;
        $i = 0;
        $sql = "";
        $blog_ids = $this->get_current_network_blog_ids();

        foreach ($blog_ids as $blog_id) {
            $table = $wpdb->get_blog_prefix($blog_id) . $this->table_name;

            if (0 < $i) {
                $sql .= " UNION ALL";
            }


            $sql .= " SELECT"
                    . " COUNT(FaUserLogin.id) AS count"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $table_usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                    . " WHERE 1 ";

            if ($where_query) {
                $sql .= $where_query;
            }
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
        $delete_nonce = wp_create_nonce($this->plugin_name . 'delete_row_by_' . $this->_args['singular']);
        $title = $item['user_id'] ? "<a href='" . get_edit_user_link($item['user_id']) . "'>" . esc_html($item['username']) . "</a>" : '<strong>' . $item['username'] . '</strong>';
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&action=%s&blog_id=%s&record_id=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), $this->plugin_name . '_network_admin_listing_table_delete_single_row', absint($blog_id), absint($item['id']), $delete_nonce),
        );
        return $title . $this->row_actions($actions);
    }

    public function process_bulk_action() {
        if (!isset($_POST[$this->plugin_name . '_network_admin_listing_table']) || empty($_POST['_wpnonce'])) {
            return FALSE;
        }

        $status = FALSE;
        $nonce = $_POST['_wpnonce'];
        $bulk_action = 'bulk-' . $this->_args['plural'];

        switch ($this->current_action()) {
            case 'bulk-delete':

                if (!empty($_POST['bulk-delete'])) {
                    if (!wp_verify_nonce($nonce, $bulk_action)) {
                        return FALSE;
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
                    return FALSE;
                }
                $blog_ids = $this->get_current_network_blog_ids();
                foreach ($blog_ids as $blog_id) {
                    switch_to_blog($blog_id);
                    $this->delete_all_rows();
                }
                restore_current_blog();
                $status = TRUE;
                break;
            default:
                $status = FALSE;
                break;
        }
        return $status;
    }

    public function delete_single_row() {
        if (empty($_GET['action']) || $this->plugin_name . '_network_admin_listing_table_delete_single_row' != $_GET['action'] || empty($_REQUEST['_wpnonce'])) {
            return FALSE;
        }
        $status = FALSE;
        $nonce = $_GET['_wpnonce'];


        if (!wp_verify_nonce($nonce, $this->plugin_name . 'delete_row_by_' . $this->_args['singular'])) {
            wp_die('invalid nonce');
        }
        //get the blog id of current network.
        $blog_ids = $this->get_current_network_blog_ids();
        $blog_id = !empty($blog_ids[0]) ? absint($blog_ids[0]) : NULL;

        if ($blog_id) {
            switch_to_blog($blog_id);
            $this->delete_rows($_GET['record_id']);
            restore_current_blog();
            $status = TRUE;
        }
        return $status;
    }

}  
}
