<?php

/**
 * This is used to create admin menu for listing page.
 * 
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 * @access private
 */
class User_Login_History_Admin_List_Page extends User_Login_History_Abstract_List_Page {

    /**
     * Return the object of List Table class.
     * 
     * @return The object of listing table class.
     */
    function get_list_table_object() {
        $UserProfile = new User_Login_History_User_Profile($this->plugin_name);
        return new User_Login_History_Admin_List_Table(NULL, $this->plugin_name, USER_LOGIN_HISTORY_TABLE_NAME, $UserProfile->get_current_user_timezone());
    }

}
