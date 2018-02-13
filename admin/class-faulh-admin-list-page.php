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
if(!class_exists('Faulh_Admin_List_Page'))
{
 class Faulh_Admin_List_Page extends Faulh_Abstract_List_Page {

    /**
     * Return the object of List Table class.
     * 
     * @return The object of listing table class.
     */
    function get_list_table_object() {
        $UserProfile = new Faulh_User_Profile($this->plugin_name, $this->version);
        return new Faulh_Admin_List_Table(NULL, $this->plugin_name, FAULH_TABLE_NAME, $UserProfile->get_current_user_timezone());
    }
    


}
   
}
