<?php
//setup a page to show lisitng on network admin.

class User_Login_History_Network_Admin_List_Page extends User_Login_History_Abstract_List_Page {

    public function load_template() {
        require  plugin_dir_path(dirname(__FILE__)) . 'admin/partials/network-listing.php';
    }

    function get_list_table_object() {
        return new User_Login_History_Network_Admin_List_Table(null, $this->plugin_name, USER_LOGIN_HISTORY_TABLE_NAME);
    }



}
