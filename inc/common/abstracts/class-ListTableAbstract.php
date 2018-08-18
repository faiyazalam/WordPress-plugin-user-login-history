<?php

namespace User_Login_History\Inc\Common\Abstracts;

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

abstract class ListTableAbstract extends \WP_List_Table {

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
        
	public function __construct($plugin_name, $version,$plugin_text_domain, $args = array()) {
            parent::__construct($args);
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->plugin_text_domain = $plugin_text_domain;
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
        
        
        abstract public function get_rows();
        abstract public function record_count();
}
