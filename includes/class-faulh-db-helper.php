<?php

/**
 * Faulh_DB_Helper.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if(!class_exists('Faulh_DB_Helper'))
{
  class Faulh_DB_Helper {

    /**
     * Retrieve the blog name by blog id.
     * 
     * @global type $wpdb
     * @param int|string $id The id of the blog.
     * @return string|null If success, the name of the blog otherwise null.
     */
    static public function get_blog_name_by_id($id = NULL) {
        if (!$id) {
            return '';
        }
        global $wpdb;
        return $wpdb->get_var("SELECT CONCAT(domain,path) AS blog_name FROM $wpdb->blogs WHERE blog_id = $id ");
    }

    /**
     * Get blog by blog id and network id.
     * If only blog id is passed, it will return only that blog id from db.
     * If only network id is passed, it will return all the blog ids belong to the passed network id.
     * If both ids are passed, it will return only one blog id belong to the passed network id.
     * 
     * @global type $wpdb
     * @param int $blog_id 
     * @param int $network_id
     * @return array Database query result. Array indexed from 0 by SQL result row number.
     */
    static public function get_blog_by_id_and_network_id($blog_id, $network_id) {
        global $wpdb;
        $where = '';
        if ($blog_id) {
            $where .= " AND `blog_id` = " . absint($blog_id);
        }

        if ($network_id) {
            $where .= " AND `site_id` = " . absint($network_id);
        }

        $sql = "SELECT blog_id FROM $wpdb->blogs WHERE 1 $where";
        $result = $wpdb->get_col($sql);
        if ($wpdb->last_error) {
            Faulh_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }
    
    static public function all_columns(){
        $columns = array(
                'user_id' => esc_html__('User Id', 'faulh'),
                'username' => esc_html__('Username', 'faulh'),
                'role' => esc_html__('Current Role', 'faulh'),
                'old_role' => "<span title='" . esc_attr__('Role while user gets logged in', 'faulh') . "'>" . esc_html__('Old Role (?)', 'faulh') . "</span>",
                'ip_address' => esc_html__('IP Address', 'faulh'),
                'country_name' => "<span title='" . esc_attr__('To track country name, "Geo Tracker" setting must be enabled.', 'faulh') . "'>" . esc_html__('Country (?)', 'faulh') . "</span>",
                'browser' => esc_html__('Browser', 'faulh'),
                'operating_system' => esc_html__('Operating System', 'faulh'),
                'timezone' => "<span title='" . esc_attr__('To track timezone, "Geo Tracker" setting must be enabled.', 'faulh') . "'>" . esc_html__('Timezone (?)', 'faulh') . "</span>",
                'user_agent' => esc_html__('User Agent', 'faulh'),
                'duration' => esc_html__('Duration', 'faulh'),
                'time_last_seen' => "<span title='" . esc_attr__('Last seen time in the session', 'faulh') . "'>" . esc_html__('Last Seen (?)', 'faulh') . "</span>",
                'time_login' => esc_html__('Login', 'faulh'),
                'time_logout' => esc_html__('Logout', 'faulh'),
                'login_status' => "<span title='" . esc_attr__('It may show "Logged in", if user does not logout properly.', 'faulh') . "'>" . esc_html__('Login Status (?)', 'faulh') . "</span>",

            );
            if (is_network_admin()) {
                $columns['blog_id'] = esc_html__('Blog ID', 'faulh');
                $columns['is_super_admin'] = esc_html__('Super Admin', 'faulh');
            }
            return $columns;
    }
}  
}

