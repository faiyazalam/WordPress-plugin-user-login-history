<?php

namespace User_Login_History\Inc\Common\Interfaces;

interface IAdminCsv {
    public function get_rows($per_page, $page_number);
    public function no_items();
    public function get_timezone();
}
