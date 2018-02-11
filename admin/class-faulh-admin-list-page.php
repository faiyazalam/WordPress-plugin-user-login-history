<?php

/**
 * This is used to create admin menu for listing page.
 * 
 * @link       https://github.com/faiyazalam
 *
 * @package    Faulh
 * @subpackage Faulh/admin
 * @author     Er Faiyaz Alam
 * @access private
 */
class Faulh_Admin_List_Page extends Faulh_Abstract_List_Page {

    /**
     * Return the object of List Table class.
     * 
     * @return The object of listing table class.
     */
    function get_list_table_object() {
        $UserProfile = new Faulh_User_Profile($this->plugin_name, $this->version);
        return new Faulh_Admin_List_Table(NULL, $this->plugin_name, USER_LOGIN_HISTORY_TABLE_NAME, $UserProfile->get_current_user_timezone());
    }

}
