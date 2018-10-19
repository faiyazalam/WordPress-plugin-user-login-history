<?php

namespace User_Login_History\Inc\Common\Abstracts;

use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;
use User_Login_History\Inc\Common\Login_Tracker;
use User_Login_History\Inc\Common\Helpers\Validation as Validation_Helper;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 * @since      1.0.0
 *
 * @author    Er Faiyaz Alam
 */
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

abstract class List_Table extends \WP_List_Table {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    protected $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    protected $version;

    /**
     * The text domain of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_text_domain    The text domain of this plugin.
     */
    protected $plugin_text_domain;
    protected $timezone;
    protected $unknown_symbol = '<span aria-hidden="true">—</span>';
    protected $table;
    protected $message;
    protected $bulk_action_form;
    protected $delete_action;
    protected $delete_action_nonce;
    protected $bulk_action_nonce;
    protected $csv_field_name = 'csv';
    protected $csv_nonce_name = 'csv_nonce';

    public function __construct($plugin_name, $version, $plugin_text_domain, $args = array()) {
        parent::__construct($args);
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;

        $this->set_bulk_action_form($this->_args['singular'] . "_form");
        $this->set_delete_action_nonce($this->_args['singular'] . "_delete_none");
        $this->set_bulk_action_nonce('bulk-' . $this->_args['plural']);
    }

    protected function set_csv_field_name($name) {
        $this->csv_field_name = $name;
    }

    public function get_csv_field_name() {
        return $this->csv_field_name;
    }

    protected function set_csv_nonce_name($name) {
        $this->csv_nonce_name = $name;
    }

    public function get_csv_nonce_name() {
        return $this->csv_nonce_name;
    }

    public function set_unknown_symbol($unknown_symbol) {
        $this->unknown_symbol = $unknown_symbol;
    }

    public function get_unknown_symbol() {
        return $this->unknown_symbol;
    }

    /**
     * Sets the timezone to be used for table listing.
     * 
     * @access public
     * @param string $timezone
     */
    public function set_timezone($timezone = '') {
        $this->timezone = $timezone;
    }

    /**
     * Gets the timezone to be used for table listing.
     * 
     * @access public
     * @return string
     */
    public function get_timezone() {
        return $this->timezone;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     * 
     * @access public
     */
    public function prepare_items() {
        $this->_column_headers = $this->get_column_info();
        $per_page = $this->get_items_per_page($this->plugin_name . '_rows_per_page');

        $this->set_pagination_args(array(
            'total_items' => $this->record_count(),
            'per_page' => $per_page
        ));

        $this->items = $this->get_rows($per_page, $this->get_pagenum());
    }

    protected function is_empty($value = '') {
        return Validation_Helper::isEmpty($value);
    }

    /**
     * Time-zone edit link
     * 
     * @return string
     */
    public function timezone_edit_link($user_id = null) {
        return esc_html__('This table is showing time in the timezone', $this->plugin_text_domain) . " - <strong>" . $this->get_timezone($user_id) . "</strong>&nbsp;<span><a class='' href='" . get_edit_user_link() . "#" . $this->plugin_name . "'>" . esc_html__('Edit', 'faulh') . "</a></span>";
    }

    /**
     * Check form submission and then 
     * process the bulk operation.
     * 
     * @access   public 
     * @return boolean
     */
    public function process_action() {
        $nonce = '_wpnonce';
        if (empty($_REQUEST[$nonce])) {
            return;
        }

        if (isset($_POST[$this->get_bulk_action_form()]) && !empty($_POST[$nonce]) && wp_verify_nonce($_POST[$nonce], $this->get_bulk_action_nonce())) {
            return $this->process_bulk_action();
        }

        if (!empty($_GET[$nonce]) && wp_verify_nonce($_GET[$nonce], $this->get_delete_action_nonce())) {
            return $this->process_single_action();
        }
    }

    public function get_all_rows() {
        return $this->get_rows(0);
    }

    public function get_bulk_action_form() {
        return $this->bulk_action_form;
    }

    public function get_bulk_action_nonce() {
        return $this->bulk_action_nonce;
    }

    public function get_delete_action_nonce() {
        return $this->delete_action_nonce;
    }

    public function set_bulk_action_form($value) {
        $this->bulk_action_form = $value;
        return $this;
    }

    public function set_bulk_action_nonce($value) {
        $this->bulk_action_nonce = $value;
        return $this;
    }

    public function set_delete_action_nonce($value) {
        $this->delete_action_nonce = $value;
        return $this;
    }

    public function set_message($message = '') {
        $this->message = $message;
    }

    public function get_message() {
        return $this->message;
    }

}
