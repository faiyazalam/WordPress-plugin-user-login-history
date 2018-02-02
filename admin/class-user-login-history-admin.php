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
    private $admin_notice_transient;

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
        $this->admin_notice_transient = USER_LOGIN_HISTORY_OPTION_PREFIX . 'admin_notice_transient';
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        global $pagenow;

        if ('admin.php' == $pagenow && isset($_GET['page']) && in_array($_GET['page'], array($this->plugin_name . '-admin-listing', $this->plugin_name . '-network-admin-listing'))) {
            wp_enqueue_style($this->plugin_name . '-admin-jquery-ui.min.css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name . '-admin.css', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        global $pagenow;

        if ('admin.php' == $pagenow && isset($_GET['page']) && in_array($_GET['page'], array($this->plugin_name . '-admin-listing', $this->plugin_name . '-network-admin-listing'))) {
            wp_enqueue_script($this->plugin_name . '-admin-jquery-ui.min.js', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array(), $this->version, 'all');
            wp_enqueue_script($this->plugin_name . '-admin-custom.js', plugin_dir_url(__FILE__) . 'js/custom.js', array(), $this->version, 'all');
            wp_localize_script($this->plugin_name . '-admin-custom.js', 'admin_custom_object', array(
                'delete_confirm_message' => __('Are your sure?', 'user-login-history'),
                'admin_url' => admin_url(),
                'plugin_name' => $this->plugin_name,
            ));
        }
    }

    public function session_start() {
        if ("" == session_id()) {
            session_start();
        }
    }

    public function process_bulk_action() {
        $Admin_List_Table = new User_Login_History_Admin_List_Table(null, $this->plugin_name);
        if ($Admin_List_Table->process_bulk_action()) {
            $this->add_admin_notice(__('Record(s) has been deleted.', 'user-login-history'));
            wp_safe_redirect(esc_url_raw(admin_url("admin.php?page=" . $_GET['page'])));
            exit;
        }
    }

    public function network_process_bulk_action() {
       
        $Network_Admin_List_Table = new User_Login_History_Network_Admin_List_Table(null, $this->plugin_name);
        if ($Network_Admin_List_Table->process_bulk_action()) {
            $this->add_admin_notice(__('Record(s) has been deleted.', 'user-login-history'));
            wp_safe_redirect(esc_url_raw(network_admin_url("admin.php?page=" . $_GET['page'])));
            exit;
        }
    }

    /**
     * Add admin notices
     *
     */
    public function add_admin_notice($message) {
        $notices = get_transient($this->admin_notice_transient);
        if ($notices === false) {
            $new_notices[] = $message;
            set_transient($this->admin_notice_transient, $new_notices, 120);
        } else {
            $notices[] = $message;
            set_transient($this->admin_notice_transient, $notices, 120);
        }
    }

    /**
     * Show admin notices
     */
    public function show_admin_notice() {
        $notices = get_transient($this->admin_notice_transient);

        if ($notices !== false) {
            foreach ($notices as $notice) {
                echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
<p><strong>' . $notice . '</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice', 'user-login-history') . '.</span></button></div>';
            }
            delete_transient($this->admin_notice_transient);
        }
    }

    public function admin_init() {
        if (current_user_can('administrator')) {
            $this->init_csv_export();
            $this->process_bulk_action();
        }
    }

    public function network_admin_init() {
        if (current_user_can('administrator')) {
            $this->network_init_csv_export();
            $this->network_process_bulk_action();
        }
    }

    private function init_csv_export() {
        //Check if download was initiated
        if (isset($_GET[$this->plugin_name . '_export_csv']) && "csv" == $_GET[$this->plugin_name . '_export_csv']) {
            if (check_admin_referer($this->plugin_name . '_export_csv', $this->plugin_name . '_export_nonce')) {
                $Admin_List_Table = new User_Login_History_Admin_List_Table(null, $this->plugin_name);
                $Admin_List_Table->export_to_CSV();
            } else {
                wp_die('Nonce error');
            }
        }
    }

    private function network_init_csv_export() {

        //Check if download was initiated
        if (isset($_GET[$this->plugin_name . '_export_csv']) && "csv" == $_GET[$this->plugin_name . '_export_csv']) {
            if (check_admin_referer($this->plugin_name . '_export_csv', $this->plugin_name . '_export_nonce')) {
                $Admin_List_Table = new User_Login_History_Network_Admin_List_Table(null, $this->plugin_name);
                $Admin_List_Table->export_to_CSV();
            } else {
                wp_die('Nonce error');
            }
        }
    }

}
