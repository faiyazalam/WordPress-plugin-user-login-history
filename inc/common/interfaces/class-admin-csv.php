<?php

namespace User_Login_History\Inc\Common\Interfaces;

interface Admin_Csv {
    public function get_all_rows();

    public function no_items();
    
    public function get_timezone();

    public function get_csv_field_name();

    public function get_csv_nonce_name();
}
