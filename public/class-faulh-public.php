<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/public
 * @author     Er Faiyaz Alam
 */
if (!class_exists('Faulh_Public')) {

    class Faulh_Public {

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
         * Initialize the class and set its properties.
         *
         * @param      string    $plugin_name       The name of the plugin.
         * @param      string    $version    The version of this plugin.
         */
        public function __construct($plugin_name, $version) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
        }



        /**
         * Register the stylesheets for the public-facing side of the site.
         *
         * @access public
         */
        public function enqueue_styles() {
            wp_register_style($this->plugin_name . '-jquery-ui.min.css', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
            }

        /**
         * Register the stylesheets for the public-facing side of the site.
         *
         */
        public function enqueue_scripts() {
            wp_register_script($this->plugin_name . '-jquery-ui.min.js', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array(), $this->version, 'all');
            wp_register_script($this->plugin_name . '-custom.js', plugin_dir_url(__FILE__) . 'js/custom.js', array(), $this->version, 'all');
            wp_localize_script($this->plugin_name . '-custom.js', 'custom_object', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'plugin_name' => $this->plugin_name,
            ));
        }

        /**
         * Shortcode to show listing table for frontend user.
         *
         */
        public function shortcode_user_table($attr) {
            if (!is_user_logged_in()) {
                return;
            }
            global $current_user;
            $Public_List_Table = new Faulh_Public_List_Table($this->plugin_name);
            $UserProfile = new Faulh_User_Profile($this->plugin_name, $this->version);
            $Public_List_Table->set_table_timezone($UserProfile->get_current_user_timezone());
            $timezone = $Public_List_Table->get_table_timezone();
            $default_args = array(
                'reset_link' => '',
                'limit' => 20,
                'date_format' => 'Y-m-d',
                'time_format' => 'H:i:s',
                'columns' => 'operating_system,ip_address,browser,time_login,time_logout');
            $attributes = shortcode_atts($default_args, $attr);

            if (!empty($attributes['columns'])) {
                $Public_List_Table->set_allowed_columns($attributes['columns']);
            }

            if (!empty($attributes['limit'])) {
                $Public_List_Table->set_limit($attributes['limit']);
            }

            if (!empty($attributes['date_format'])) {
                $Public_List_Table->set_table_date_format($attributes['date_format']);
            }

            if (!empty($attributes['time_format'])) {
                $Public_List_Table->set_table_time_format($attributes['time_format']);
            }

            $reset_URL = !empty($attributes['reset_link']) ? home_url($attributes['reset_link']) : FALSE;
            $_GET['user_id'] = $current_user->ID;
            
            wp_enqueue_style($this->plugin_name . '-jquery-ui.min.css');
            wp_enqueue_script($this->plugin_name . '-jquery-ui.min.js');
            wp_enqueue_script($this->plugin_name . '-custom.js');
            ob_start();
            require_once(plugin_dir_path(__FILE__) . 'partials/listing.php');
            return ob_get_clean();
        }

    }

}

