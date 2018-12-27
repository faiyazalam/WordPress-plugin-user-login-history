<?php

namespace User_Login_History\Inc\Frontend;

use User_Login_History\Inc\Common\Abstracts\User_Profile as UserProfileAbstract;
use User_Login_History\Inc\Frontend\Frontend_Login_List_Table as List_Table;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 *
 * @author    Er Faiyaz Alam
 */
class Frontend {

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
    private $User_Profile;
    private $List_Table;

    /**
     * Initialize the class and set its properties.
     *
     * @param       string $plugin_name        The name of this plugin.
     * @param       string $version            The version of this plugin.
     */
    public function __construct($plugin_name, $version, List_Table $List_Table, UserProfileAbstract $User_Profile) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->List_Table = $List_Table;
        $this->User_Profile = $User_Profile;
    }

    public function get_User_Profile() {
        return $this->User_Profile;
    }

    public function get_List_Table() {
        return $this->List_Table;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     */
    public function enqueue_styles() {
        wp_register_style($this->plugin_name . '-public-jquery-ui.min', plugin_dir_url(__FILE__) . 'css/jquery-ui.min.css', array(), $this->version, 'all');
        wp_register_style($this->plugin_name . '-public-custom', plugin_dir_url(__FILE__) . 'css/custom.css');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     */
    public function enqueue_scripts() {
        wp_register_script($this->plugin_name . '-public-jquery-ui.min.js', plugin_dir_url(__FILE__) . 'js/jquery-ui.min.js', array(), $this->version, 'all');
        wp_register_script($this->plugin_name . '-public-custom.js', plugin_dir_url(__FILE__) . 'js/custom.js', array(), $this->version, 'all');
        wp_localize_script($this->plugin_name . '-public-custom.js', 'public_custom_object', array(
            'invalid_date_range_message' => esc_html__('Please provide a valid date range.', 'faulh'),
        ));
    }

    /**
     * Shortcode to show listing table for frontend user.
     *
     * @access public
     */
    public function shortcode_user_table($attr) {
        if (!is_user_logged_in()) {
            return;
        }
        global $current_user;

        $this->get_List_Table()->set_table_timezone($this->get_User_Profile()->get_user_timezone());

        $default_args = array(
            'title' => '',
            'reset_link' => '',
            'limit' => 20,
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            'show_timezone_selector' => 'true',
            'columns' => 'operating_system,browser,time_login,time_logout');
        $attributes = shortcode_atts($default_args, $attr);

        $attributes = array_map('trim', $attributes);

        if (!empty($attributes['columns'])) {
            $this->get_List_Table()->set_allowed_columns($attributes['columns']);
        }

        if (!empty($attributes['limit'])) {
            $this->get_List_Table()->set_limit($attributes['limit']);
        }

        if (!empty($attributes['date_format'])) {
            $this->get_List_Table()->set_table_date_format($attributes['date_format']);
        }

        if (!empty($attributes['time_format'])) {
            $this->get_List_Table()->set_table_time_format($attributes['time_format']);
        }

        $reset_URL = !empty($attributes['reset_link']) ? home_url($attributes['reset_link']) : get_permalink();
        $_GET['user_id'] = $current_user->ID; //to fetch records of current logged in user only.

        wp_enqueue_style($this->plugin_name . '-public-jquery-ui.min');
        wp_enqueue_script($this->plugin_name . '-public-jquery-ui.min.js');
        wp_enqueue_script($this->plugin_name . '-public-custom.js');
        wp_enqueue_style($this->plugin_name . '-public-custom');
        ob_start();
        $this->get_List_Table()->prepare_items();
        require_once(plugin_dir_path(__FILE__) . 'views/listing.php');
        return ob_get_clean();
    }

}
