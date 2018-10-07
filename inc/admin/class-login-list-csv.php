<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;

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
final class Login_List_Csv {

    private $list_table;
    private $unknown_symbol = '---';

    public function set_login_list_object(Admin_Csv_Interface $Admin_Csv_Interface) {
        $this->list_table = $Admin_Csv_Interface;
        $this->list_table->set_unknown_symbol($this->unknown_symbol);
    }

    private function set_headers() {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $this->get_suffix() . '.csv');
    }

    private function get_suffix() {
        return $suffix = "login_list_" . date('n-j-y_H-i');
    }

    public function init() {
        $this->set_headers();
        $this->export();
    }

    
     public function is_request_for_csv() {
        if (!empty($_GET[$this->list_table->get_csv_field_name()]) && 1 == $_GET[$this->list_table->get_csv_field_name()]) {
            if (check_admin_referer($this->list_table->get_csv_nonce_name())) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    
    /**
     * Exports CSV
     * 
     * @access public
     * @global type $current_user
     */
    private function export() {
        $timezone = $this->list_table->get_timezone();
        $data = $this->list_table->get_rows(0); // pass zero to get all the records
        //date string to suffix the file name: month - day - year - hour - minute

        if (!$data) {
            $this->list_table->no_items();
            exit;
        }

        $fp = fopen('php://output', 'w');
        $i = 0;
        $record = array();
        foreach ($data as $row) {

            $user_id = !empty($row['user_id']) ? $row['user_id'] : FALSE;
            if (!$user_id) {
                $time_last_seen_str = $time_logout_str = $current_role = $old_role = $this->list_table->get_unknown_symbol();
            } else {
                $time_last_seen_str = !empty($row['time_last_seen']) && strtotime($row['time_last_seen']) > 0 ? Date_Time_Helper::convert_format(Date_Time_Helper::convert_timezone($row['time_last_seen'], '', $timezone)) : $this->list_table->get_unknown_symbol();
                $time_logout_str = !empty($row['time_logout']) && strtotime($row['time_logout']) > 0 ? Date_Time_Helper::convert_format(Date_Time_Helper::convert_timezone($row['time_logout'], '', $timezone)) : $this->list_table->get_unknown_symbol();
                $current_role = $this->list_table->column_default($row, 'role');
                $old_role = $this->list_table->column_default($row, 'old_role');
            }

            $record[__('User ID', 'faulh')] = $user_id ? $user_id : $this->list_table->get_unknown_symbol();
            $record[__('Username', 'faulh')] = $this->list_table->column_default($row, 'username_csv');
            $record[__('Current Role', 'faulh')] = $current_role;
            $record[__('Old Role', 'faulh')] = $old_role;
            $record[__('IP Address', 'faulh')] = $this->list_table->column_default($row, 'ip_address');

            $record[__('Browser', 'faulh')] = $this->list_table->column_default($row, 'browser');
            $record[__('Operating System', 'faulh')] = $this->list_table->column_default($row, 'operating_system');
            $record[__('Country Name', 'faulh')] = $this->list_table->column_default($row, 'country_name');
            $record[__('Country Code', 'faulh')] = $this->list_table->column_default($row, 'country_code');
            $record[__('Timezone', 'faulh')] = $this->list_table->column_default($row, 'timezone');
            $record[__('Duration', 'faulh')] = $this->list_table->column_default($row, 'duration');
            $record[__('Last Seen', 'faulh')] = $time_last_seen_str;
            $record[__('Login', 'faulh')] = $this->list_table->column_default($row, 'time_login');
            $record[__('Logout', 'faulh')] = $time_logout_str;
            $record[__('Login Status', 'faulh')] = $this->list_table->column_default($row, 'login_status');
            $record[__('User Agent', 'faulh')] = $this->list_table->column_default($row, 'user_agent');
            
            if (is_network_admin()) {
                $record[__('Super Admin', 'faulh')] = $this->list_table->column_default($row, 'is_super_admin');
                $record[__('Blog ID', 'faulh')] = $this->list_table->column_default($row, 'blog_id');
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

}
