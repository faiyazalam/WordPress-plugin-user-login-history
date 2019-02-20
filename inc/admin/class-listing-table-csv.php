<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;

/**
 * CSV Export Functionality
 *
 * @link       http://userloginhistory.com
 *
 * @author    Er Faiyaz Alam
 */
final class Listing_Table_Csv {

    /**
     * Holds the instance of the class which is implemented with Admin_Csv
     * @var Admin_Csv 
     */
    private $Listing_Table;

    /**
     * Holds a symbol to be used to render unknown record.
     * @var string 
     */
    private $unknown_symbol = '---';

    public function set_Listing_Table(Admin_Csv_Interface $Admin_Csv_Interface) {
        $this->Listing_Table = $Admin_Csv_Interface;
    }

    /**
     * Set content type in header.
     */
    private function set_headers() {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $this->get_suffix() . '.csv');
    }

    /**
     * get suffix to be used for csv file name.
     * @return string
     */
    private function get_suffix() {
        return $suffix = "login_list_" . date('n-j-y_H-i');
    }

    /**
     * Initializes csv export
     * @access public
     */
    public function init() {
        $this->set_headers();
        $this->export();
    }

    /**
     * Validate http request
     * TODO: make this function private - low priority
     * @return bool
     */
    public function is_request_for_csv() {
        return !empty($_GET[$this->Listing_Table->get_csv_field_name()]) && 1 == $_GET[$this->Listing_Table->get_csv_field_name()] && check_admin_referer($this->Listing_Table->get_csv_nonce_name());
    }

    /**
     * Exports CSV
     * 
     * @access private
     */
    private function export() {
        $data = $this->Listing_Table->get_all_rows();

        if (!$data) {
            $this->Listing_Table->no_items();
            exit;
        }

        $this->Listing_Table->set_unknown_symbol($this->unknown_symbol);
        $columns = $this->Listing_Table->get_columns();


        $fp = fopen('php://output', 'w');
        $i = 0;
        $record = array();
        foreach ($data as $row) {

            foreach ($columns as $fieldName => $fieldLabel) {
                if (!key_exists($fieldName, $row)) {
                    continue;
                }

                $record[$fieldName] = $this->Listing_Table->column_default($row, $fieldName);
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
