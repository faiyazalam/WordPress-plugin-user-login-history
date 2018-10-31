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
        
        return !empty($_GET[$this->list_table->get_csv_field_name()]) && 1 == $_GET[$this->list_table->get_csv_field_name()] && check_admin_referer($this->list_table->get_csv_nonce_name());
    }

    /**
     * Exports CSV
     * 
     * @access public
     * @global type $current_user
     */
    private function export() {
        $timezone = $this->list_table->get_timezone();
        $data = $this->list_table->get_all_rows();

        if (!$data) {
            $this->list_table->no_items();
            exit;
        }
        
        $columns = $this->list_table->get_columns();
       

        $fp = fopen('php://output', 'w');
        $i = 0;
        $record = array();
        foreach ($data as $row) {
            
        foreach ($columns as $fieldName => $fieldLabel) {
            if(!key_exists($fieldName, $row))
            {
                continue;
            }
            
            $record[$fieldLabel] = $this->list_table->column_default($row, $fieldName);
        }
        
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
