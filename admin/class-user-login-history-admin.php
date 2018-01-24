<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.0.0
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam <support@userloginhistory.com>
 */
class User_Login_History_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    private function UserTracker() {
        return User_Login_History_User_Tracker::get_instance();
    }

    private function Admin_List_table() {
        return new User_Login_History_Admin_List_table();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in User_Login_History_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The User_Login_History_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/user-login-history-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in User_Login_History_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The User_Login_History_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/user-login-history-admin.js', array('jquery'), $this->version, false);
    }

    public function user_login_failed($user_login) {
        $this->UserTracker()->user_login_failed($user_login);
    }

    public function user_logout() {
        $this->UserTracker()->user_logout();
    }

    public function user_login($user_login, $user) {
        $this->UserTracker()->user_login($user_login, $user);
    }

    public function set_user_session_token($logged_in_cookie, $expire, $expiration, $user_id, $logged_in_text, $token) {
        $this->UserTracker()->set_session_token($token);
    }

    public function update_user_time_last_seen() {
        $this->UserTracker()->update_time_last_seen();
    }

    public function session_start() {
        if ("" == session_id()) {
            session_start();
        }
    }

    public function plugins_loaded() {

        User_Login_History_Singleton_Admin_List_Table::get_instance();
    }

    public function process_bulk_action() {
        if ($this->Admin_List_table()->process_bulk_action()) {
            $this->add_admin_notice(__('Record(s) has been deleted.'));
            wp_safe_redirect(esc_url_raw(admin_url("admin.php?page=".$_GET['page'])));
            exit;
        }
    }

    /**
     * Add admin notices
     *
     */
    public function add_admin_notice($message) {
        $notices = get_transient('user_login_history_admin_notice_transient');
        if ($notices === false) {
            $new_notices[] = $message;
            set_transient('user_login_history_admin_notice_transient', $new_notices, 120);
        } else {
            $notices[] = $message;
            set_transient('user_login_history_admin_notice_transient', $notices, 120);
        }
    }

    /**
     * Show admin notices
     */
    public function show_admin_notice() {
        $notices = get_transient('user_login_history_admin_notice_transient');
        $str = __('Dismiss this notice', 'user-login-history');

        if ($notices !== false) {
            foreach ($notices as $notice) {
                echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
<p><strong>' . $notice . '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . $str . '.</span></button></div>';
            }
            delete_transient('user_login_history_admin_notice_transient');
        }
    }

}
