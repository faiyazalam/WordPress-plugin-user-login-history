<?php
//setup a page to show lisitng on network admin.

class User_Login_History_Network_Admin_List_Page extends User_Login_History_Abstract_List_Page {
//    const DEFAULT_OPTION_VALUE = 20;
//    public $admin_list_table;
//    public $plugin_name;
//    public $option_name;

//    // class constructor
//    public function __construct($plugin_name) {
//        $this->plugin_name = $plugin_name;
//         $this->option_name = 'rows_per_page';
//       
//    }
//
//    public function set_screen($status, $option, $value) {
//if($option == $this->option_name)
//{
//    return $value; 
//}
       
   // }

//    public function plugin_menu() {
//        $hook = add_menu_page(
//                'User Login History', 'User Login History', 'manage_options', 'user-login-history-admin-listing', [ $this, 'admin_lisitng']
//        );
//
//        add_action("load-$hook", [ $this, 'screen_option']);
//    }

    /**
     * Plugin settings page
     */
    public function load_template() {
        require  plugin_dir_path(dirname(__FILE__)) . 'admin/partials/network-listing.php';
    }

    /**
     * Screen options
     */
//    public function screen_option() {
//
//        $args = array(
//            'label' => __('Records per page', 'user-login-history'),
//            'default' => self::DEFAULT_OPTION_VALUE,
//            'option' => $this->option_name
//        );
//
//        add_screen_option('per_page', $args);
//        $this->set_list_table_object();
//       
//    }
    
    function get_list_table_object() {
        return new User_Login_History_Network_Admin_List_Table(null, $this->plugin_name);
    }



}
