<?php

class User_Login_History_Network_Admin_List_Table extends User_Login_History_Abstract_List_Table {
    public function get_rows($per_page = 20, $page_number = 1) {
        global $wpdb;
        $where_query = $this->prepare_where_query();
        $get_values = array();
        $table_usermeta = $wpdb->usermeta;
        $i = 0;
        $sql = "";
        $blog_ids = $this->get_current_network_blog_ids();
        foreach ($blog_ids as $blog_id) {
            $table = User_Login_History_DB_Helper::get_table_name($blog_id);
            if (0 < $i) {
                $sql .= " UNION ";
            }

            $sql .= " SELECT"
                    . " FaUserLogin.*,"
                    . " UserMeta.meta_value,"
                    . " $blog_id as blog_id"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $table_usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                    . " WHERE 1 ";

            if ($where_query) {
                $sql .= $where_query;
            }

            $sql .= ' GROUP BY FaUserLogin.id ';
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
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }

    /**
     * 
     * @global type $wpdb
     * @return array
     */
    private function get_current_network_blog_ids() {
        global $wpdb;
        $where = "";

        if (!empty($_GET['blog_id'])) {
            $where .= " AND `blog_id` = " . esc_sql(absint($_GET['blog_id']));
        }

        $where .= " AND `site_id` = " . get_current_network_id();
        $sql = "SELECT blog_id FROM $wpdb->blogs WHERE 1 $where";

        $result = $wpdb->get_col($sql);
        if ($wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }



    /**
     * Returns the count of records in the table.
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

            $table = User_Login_History_DB_Helper::get_table_name($blog_id);

            if (0 < $i) {
                $sql .= " UNION ";
            }


            $sql .= "SELECT COUNT(*) as count FROM  (SELECT"
                    . " FaUserLogin.id"
                    . " FROM $table  AS FaUserLogin"
                    . " LEFT JOIN $table_usermeta AS UserMeta ON (UserMeta.user_id=FaUserLogin.user_id"
                    . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                    . " WHERE 1 ";

            if ($where_query) {
                $sql .= $where_query;
            }

            $sql .= ' GROUP BY FaUserLogin.id) AS FaUserLoginSubCount';
            $i++;
        }

        $sql_count = "SELECT SUM(count) as total FROM ($sql) AS FaUserLoginCount";
        return $wpdb->get_var($sql_count);
    }
    
       



}
