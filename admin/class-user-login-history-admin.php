<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/faiyazalam
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
 * @author     Er Faiyaz Alam
 * @access private
 */
class User_Login_History_Admin {

    /**
     * The ID of this plugin.
     *
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The transient for admin notice purpose.
     *
     * @access   private
     * @var      string    $admin_notice_transient
     */
    private $admin_notice_transient;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->admin_notice_transient = $this->plugin_name . 'admin_notice_transient';
    }

    /**
     * Initialize the csv export.
     * 
     * @access public
     */
    private function init_csv_export() {
        //Check if download was initiated
        if (isset($_GET[$this->plugin_name . '_export_csv']) && "csv" == $_GET[$this->plugin_name . '_export_csv']) {
            if (check_admin_referer($this->plugin_name . '_export_csv', $this->plugin_name . '_export_nonce')) {
                $List_Table = is_network_admin() ? new User_Login_History_Network_Admin_List_Table(null, $this->plugin_name, USER_LOGIN_HISTORY_TABLE_NAME) : new User_Login_History_Admin_List_Table(null, $this->plugin_name, USER_LOGIN_HISTORY_TABLE_NAME);
                $List_Table->export_to_CSV();
            }
        }
    }

    /**
     * Register the stylesheets for the admin area.
     * 
     * @access public
     */
    public function enqueue_styles() {
        global $pagenow;

        if ('admin.php' == $pagenow && isset($_GET['page']) && $_GET['page'] == $this->plugin_name . '-admin-listing') {
            wp_enqueue_style($this->plugin_name . '-admin-jquery-ui.min.css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name . '-admin.css', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     * 
     * @access public
     */
    public function enqueue_scripts() {
        global $pagenow;

        if ('admin.php' == $pagenow && isset($_GET['page']) && $_GET['page'] == $this->plugin_name . '-admin-listing') {
            wp_enqueue_script($this->plugin_name . '-admin-jquery-ui.min.js', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array(), $this->version, 'all');
            wp_enqueue_script($this->plugin_name . '-admin-custom.js', plugin_dir_url(__FILE__) . 'js/custom.js', array(), $this->version, 'all');
            wp_localize_script($this->plugin_name . '-admin-custom.js', 'admin_custom_object', array(
                'delete_confirm_message' => __('Are your sure?', 'user-login-history'),
                'admin_url' => admin_url(),
                'plugin_name' => $this->plugin_name,
            ));
        }
    }

    /**
     * Just to start session.
     * 
     * @access public
     */
    public function session_start() {
        if ("" == session_id()) {
            session_start();
        }
    }

    /**
     * Process the bulk operation for the listing tables.
     * 
     * @access public
     */
    public function process_bulk_action() {
        $status = FALSE;
        $List_Table = is_network_admin() ? new User_Login_History_Network_Admin_List_Table(null, $this->plugin_name, USER_LOGIN_HISTORY_TABLE_NAME) : new User_Login_History_Admin_List_Table(null, $this->plugin_name, USER_LOGIN_HISTORY_TABLE_NAME);

        if ($List_Table->process_bulk_action()) {
            $this->add_admin_notice(__('Record(s) has been deleted.', 'user-login-history'));
            $status = TRUE;
        }

        if ($List_Table->delete_single_row()) {
            $this->add_admin_notice(__('Record has been deleted.', 'user-login-history'));
            $status = TRUE;
        }

        if ($status) {
            //redirect to current url
            wp_safe_redirect(esc_url(add_query_arg(NULL, NULL)));
            exit;
        }
    }

    /**
     * Add admin notices
     * @access public
     */
    public function add_admin_notice($message, $type = 'success') {
        $notices = get_transient($this->admin_notice_transient);
        if ($notices === false) {
            $new_notices[] = array($message, $type);
            set_transient($this->admin_notice_transient, $new_notices, 120);
        } else {
            $notices[] = array($message, $type);
            set_transient($this->admin_notice_transient, $notices, 120);
        }
    }

    /**
     * Show admin notices
     * 
     * @access public
     */
    public function show_admin_notice() {
        $notices = get_transient($this->admin_notice_transient);

        if ($notices !== false) {
            foreach ($notices as $notice) {
                echo '<div class="notice notice-' . $notice[1] . ' is-dismissible"><p>' . $notice[0] . '</p></div>';
            }
            delete_transient($this->admin_notice_transient);
        }
    }

    /**
     * The callback function for the action hook - admin_init
     */
    public function admin_init() {
        if (!current_user_can('administrator')) {
            return;
        }

        global $pagenow;
        if ($pagenow == 'admin.php') {
            if (!empty($_GET['page'])) {

                if ($this->plugin_name . '-admin-listing' == $_GET['page']) {
                    $this->init_csv_export();
                    $this->process_bulk_action();
                }
            }
        }
    }

    /**
     * Update the network settings.
     * 
     * @access public
     */
    public function update_network_setting() {
        $obj = new User_Login_History_Network_Admin_Setting($this->plugin_name);
        if ($obj->update()) {
            $this->add_admin_notice(__('Settings updated successfully.', 'user-login-history'));
            wp_safe_redirect(network_admin_url("settings.php?page=" . $_GET['page']));
            exit;
        }
    }

}
