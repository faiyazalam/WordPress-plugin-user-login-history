<?php

//base class to setupt listing page on backend.

abstract class User_Login_History_Abstract_List_Page {

    const DEFAULT_OPTION_VALUE = 20;

    public $admin_list_table;
    public $plugin_name;
    private $option_name;

    public function __construct($plugin_name) {

        $this->plugin_name = $plugin_name;
        $this->option_name = 'rows_per_page';
    }

    public function set_screen($status, $option, $value) {
        if ($option == $this->option_name) {
            return $value;
        }
    }

    public function plugin_menu() {
        $hook = add_menu_page(
                'User Login History', 'User Login History', 'manage_options', 'user-login-history-admin-listing', [ $this, 'load_template']
        );
        add_action("load-$hook", [ $this, 'screen_option']);
    }


    public function screen_option() {
        $args = array(
            'label' => __('Records per page', 'user-login-history'),
            'default' => self::DEFAULT_OPTION_VALUE,
            'option' => $this->option_name
        );

        add_screen_option('per_page', $args);
        $this->admin_list_table = $this->get_list_table_object();
    }
    
        /**
     * This function will return template file path.
     */
    abstract public function load_template();
 /**
     * This function will return object of list table class e.g. new User_Login_History_Admin_List_Table().
     */
    abstract function get_list_table_object();
}
