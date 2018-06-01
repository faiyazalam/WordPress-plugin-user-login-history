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
if (!class_exists('Faulh_Admin')) {

    class Faulh_Admin {

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
                    $UserProfile = new Faulh_User_Profile($this->plugin_name, $this->version);
                    $List_Table = is_network_admin() ? new Faulh_Network_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME, $UserProfile->get_current_user_timezone()) : new Faulh_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME, $UserProfile->get_current_user_timezone());
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

            if (in_array($pagenow, array('admin.php', 'settings.php')) && isset($_GET['page']) && in_array($_GET['page'], array($this->plugin_name . "-setting", $this->plugin_name . '-admin-listing', $this->plugin_name . '-help', $this->plugin_name . '-about'))
            ) {
                wp_enqueue_style($this->plugin_name . '-admin-jquery-ui.min.css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
                wp_enqueue_style($this->plugin_name . '-admin.css', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
            }

            if (in_array($pagenow, array('profile.php', 'user-edit.php'))) {
                wp_enqueue_style($this->plugin_name . '-user-profile.css', plugin_dir_url(__FILE__) . 'css/user-profile.css', array(), $this->version, 'all');
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
                    'delete_confirm_message' => esc_html__('Are your sure?', 'faulh'),
                    'invalid_date_range_message' => esc_html__('Please provide a valid date range.', 'faulh'),
                    'admin_url' => admin_url(),
                    'plugin_name' => $this->plugin_name,
                ));
            }
        }

        /**
         * Process the bulk operation for the listing tables.
         * 
         * @access public
         */
        public function process_bulk_action() {

            $status = FALSE;
            
            if(is_network_admin())
            {
           $List_Table = new Faulh_Network_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME);
           $url = network_admin_url();
            }
            else{
           $List_Table = new Faulh_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME);
   $url = admin_url();
            }

            if ($List_Table->process_bulk_action()) {
                $this->add_admin_notice(esc_html__('Record(s) deleted.', 'faulh'));
                $status = TRUE;
            }

            if (!$status) {
                if ($List_Table->delete_single_row()) {
                    $this->add_admin_notice(esc_html__('Record deleted.', 'faulh'));
                    $status = TRUE;
                }
            }


            if ($status) {
                wp_safe_redirect(esc_url($url . "admin.php?page=" . $_GET['page']));
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
                if (!empty($_GET['page']) && $this->plugin_name . '-admin-listing' == $_GET['page']) {
                    $this->init_csv_export();
                    $this->process_bulk_action();
                }
            }
        }

        /**
         * Update the network settings.
         * 
         * @access public
         */
        public function update_network_setting() {
            $obj = new Faulh_Network_Admin_Setting($this->plugin_name);
            if ($obj->update()) {
                $this->add_admin_notice(esc_html__('Settings updated successfully.', 'faulh'));
                wp_safe_redirect(network_admin_url("settings.php?page=" . $_GET['page']));
                exit;
            }
        }

        /**
         * Check if update available.
         * If yes, update DB.
         * 
         */
        public function check_update_version() {
            if (!is_admin()) {
                return;
            }
            if (!current_user_can('administrator')) {
                return;
            }
            // Current version
            $current_version = get_option(FAULH_OPTION_NAME_VERSION);
            //If the version is older
            if ($current_version && version_compare($current_version, $this->version, '<')) {
                require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-faulh-activator.php';
                $this->add_admin_notice(sprintf(esc_html__('Plugin Updated! We have done some major changes in the version 1.7.0. Please see the %1$sHelp%2$s page.', 'faulh'), "<a href='" . admin_url("admin.php?page={$this->plugin_name}-help") . "'>", "</a>"));

                if (version_compare($current_version, '1.7.0', '<')) {
                    Faulh_Activator::create_table();
                    delete_option('fa_userloginhostory_frontend_limit');
                    delete_option('fa_userloginhostory_frontend_fields');
                    Faulh_Activator::update_options();
                    return;
                }

                if (version_compare($current_version, '1.7.0', '>=')) {
                    if (is_plugin_active_for_network(FAULH_BOOTSTRAP_FILE_PATH)) {
                        $blog_ids = Faulh_DB_Helper::get_blog_by_id_and_network_id(null, get_current_network_id());
                        foreach ($blog_ids as $blog_id) {
                            switch_to_blog($blog_id);
                            Faulh_Activator::create_table();
                            Faulh_Activator::update_options();
                        }
                        restore_current_blog();
                    } else {
                        Faulh_Activator::create_table();
                        Faulh_Activator::update_options();
                    }
                    return;
                }
            }
        }

        /**
         * The callback function for the action hook - admin menu.
         */
        public function plugin_menu() {
            $menu_slug = $this->plugin_name . "-admin-listing";
            $hook = add_menu_page(
                    Faulh_Template_Helper::plugin_name(), Faulh_Template_Helper::plugin_name(), 'manage_options', $menu_slug, array($this, 'render_list_table'), plugin_dir_url(__FILE__) . 'images/icon.png', 30
            );
            add_submenu_page($menu_slug, esc_html__('Login List', 'faulh'), esc_html__('Login List', 'faulh'), 'manage_options', $menu_slug, array($this, 'render_list_table'));
            add_submenu_page($menu_slug, esc_html__('About', 'faulh'), esc_html__('About', 'faulh'), 'manage_options', $this->plugin_name . '-about', array($this, 'render_about_page'));
            add_submenu_page($menu_slug, esc_html__('Help', 'faulh'), esc_html__('Help', 'faulh'), 'manage_options', $this->plugin_name . '-help', array($this, 'render_help_page'));
            add_action("load-$hook", array($this, 'screen_option'));
        }

        /**
         * Callback function for the filter - set-screen-option
         */
        public function set_screen($status, $option, $value) {
            return $value;
        }

        /**
         * Callback function for the action - load-$hook
         */
        public function screen_option() {
            $option = 'per_page';
            $args = array(
                'label' => __('Show Records Per Page', 'user-login-history'),
                'default' => 20,
                'option' => $this->plugin_name . '_rows_per_page'
            );

            add_screen_option($option, $args);

            $UserProfile = new Faulh_User_Profile($this->plugin_name, $this->version);

            if (is_network_admin()) {
                $this->list_table = new Faulh_Network_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME, $UserProfile->get_current_user_timezone());
            } else {
                $this->list_table = new Faulh_Admin_List_Table(null, $this->plugin_name, FAULH_TABLE_NAME, $UserProfile->get_current_user_timezone());
            }
            $this->list_table->prepare_items();
        }

        /**
         * Callback function to render about page.
         */
        public function render_about_page() {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/about.php';
        }

        /**
         * Callback function to render help page.
         */
        public function render_help_page() {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/help.php';
        }

        /**
         * Callback function to render listing table.
         */
        public function render_list_table() {

            require plugin_dir_path(dirname(__FILE__)) . 'admin/partials/listing.php';
        }

    }

}

