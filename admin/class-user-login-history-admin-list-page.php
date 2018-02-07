<?php
class User_Login_History_Admin_List_Page extends User_Login_History_Abstract_List_Page {
    
     public function __construct($plugin_name = '') {
         parent::__construct($plugin_name);
    }
    
    function get_list_table_object() {
        return new User_Login_History_Admin_List_Table(NULL, $this->plugin_name, USER_LOGIN_HISTORY_TABLE_NAME, User_Login_History_DB_Helper::get_current_user_timezone());
    }
}