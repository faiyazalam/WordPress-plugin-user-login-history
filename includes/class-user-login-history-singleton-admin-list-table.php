<?php

class User_Login_History_Singleton_Admin_List_Table {

    // class instance
    static $instance;
    // customer WP_List_Table object
    public $admin_list_table;
    public $plugin_name;

    // class constructor
    public function __construct($plugin_name) {
        add_filter('set-screen-option', [ __CLASS__, 'set_screen'], 10, 3);
        add_action('admin_menu', [ $this, 'plugin_menu']);
        $this->plugin_name = $plugin_name;
    }

    public static function set_screen($status, $option, $value) {

        return $value;
    }

    public function plugin_menu() {

        $hook = add_menu_page(
                'User Login History', 'User Login History', 'manage_options', 'user-login-history-admin-listing', [ $this, 'admin_lisitng']
        );

        add_action("load-$hook", [ $this, 'screen_option']);
    }

    /**
     * Plugin settings page
     */
    public function admin_lisitng() {
        require  plugin_dir_path(dirname(__FILE__)) . 'admin/partials/listing.php';
    }

    /**
     * Screen options
     */
    public function screen_option() {

        $option = 'per_page';
        $args = [
            'label' => 'Records per page',
            'default' => 5,
            'option' => 'rows_per_page'
        ];

        add_screen_option($option, $args);
        $this->admin_list_table = new User_Login_History_Admin_List_table();
    }

    /** Singleton instance */
    public static function get_instance($plugin_name) {
        if (!isset(self::$instance)) {
            self::$instance = new self($plugin_name);
        }
        return self::$instance;
    }

}
