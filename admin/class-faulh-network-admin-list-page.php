<?php

/**
 * This is used to create network admin menu for listing page.
 * 
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 * @access private
 */
if(!class_exists('Faulh_Network_Admin_List_Page'))
{
class Faulh_Network_Admin_List_Page extends Faulh_Abstract_List_Page {

        /**
     * Loads the listing template file.
     * 
     *@access public
     */
    public function load_template() {
        require  plugin_dir_path(dirname(__FILE__)) . 'admin/partials/network-listing.php';
    }

        /**
     * Return the object of List Table class.
     * 
     * @return The object of listing table class.
     */
    function get_list_table_object() {
        $UserProfile = new Faulh_User_Profile($this->plugin_name, $this->version);
        return new Faulh_Network_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME, $UserProfile->get_current_user_timezone());
    }



}
    
}
