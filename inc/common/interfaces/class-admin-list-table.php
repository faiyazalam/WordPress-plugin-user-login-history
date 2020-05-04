<?php

namespace User_Login_History\Inc\Common\Interfaces;

interface Admin_List_Table {

    public function prepare_where_query();

    public function get_rows();

    public function record_count();

    public function process_bulk_action();

    public function process_single_action();
}
