<?php

/**
 * User_Login_History_Table_Page
 * This singleton class is used for listing table in admin panel.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 */
?>
<?php
require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-user-login-history-list-table.php';
class User_Login_History_Table_Page {
    /**
     * Holds static instance of this plugin.
     *
     * @since    1.4.1
     * @access   public
     * @var      string    $instance    The static instance of this plugin.
     */
    static $instance;

    /**
     * Holds instance of the class-User_Login_History_List_Table.
     * It is used to show screen option on backend listing page.
     *
     * @since    1.4.1
     * @access   public
     * @var      string    $list_table_object
     */
    public $list_table_object;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.4.1
     */
    public function __construct() {
        add_filter('set-screen-option', array(__CLASS__, 'set_screen'), 10, 3);
        add_action('admin_menu', array($this, 'plugin_menu'));
    }

    /**
     * set screen.
     *
     * @since    1.4.1
     */
    public static function set_screen($status, $option, $value) {
        return $value;
    }

    /**
     * add menu page
     *
     * @since    1.4.1
     */
    public function plugin_menu() {

        $hook = add_menu_page(
                "User Login History",
                __("User Login History", 'user-login-history'), 
                'manage_options', 
                'user-login-history', 
                array($this, 'admin_listing_page'), 
                plugin_dir_url(__FILE__) . 'images/icon.png',
                30
        );

        add_action("load-$hook", array($this, 'screen_option'));
    }

    /**
     * admin_listing_page
     * Callback function for menu page
     */
    public function admin_listing_page() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/listing/listing.php';
    }

    /**
     * set screen option.
     */
    public function screen_option() {
        $option = 'per_page';
        $args = array(
            'label' => __('Show Records Per Page', 'user-login-history'),
            'default' => 20,
            'option' => 'rows_per_page'
        );

        add_screen_option($option, $args);

//create object to show screen option on backend listing page
        $this->list_table_object = new User_Login_History_List_Table();
    }

    /**
     * Singleton instance
     */
    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}