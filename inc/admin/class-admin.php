<?php

namespace User_Login_History\Inc\Admin;
use User_Login_History\Inc\Admin\LoginListTable;


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
class Admin {

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
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since       1.0.0
	 * @param       string $plugin_name        The name of this plugin.
	 * @param       string $version            The version of this plugin.
	 * @param       string $plugin_text_domain The text domain of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_text_domain = $plugin_text_domain;

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
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/faulh-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/*
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/faulh-admin.js', array( 'jquery' ), $this->version, false );

	}
        
        
         public function admin_menu() {
            $menu_slug = $this->plugin_name . "-login-listing";
            $hook = add_menu_page(
                    esc_html__('Login List', 'faulh'), 'User Login History', 'manage_options', $menu_slug, array($this, 'render_login_list'), plugin_dir_url(__FILE__) . 'images/icon.png', 30
            );
            add_submenu_page($menu_slug, esc_html__('Login List', $this->plugin_text_domain), esc_html__('Login List', 'faulh'), 'manage_options', $menu_slug, array($this, 'render_login_list'));
           // add_submenu_page($menu_slug, esc_html__('Login Lists', 'faulh'), esc_html__('Login Lists', 'faulh'), 'manage_options', $this->plugin_name . "-login-listings", array($this, 'render_login_lists'));
            add_action("load-$hook", array($this, 'screen_option'));
        }
        
        public function render_login_list() {
          require plugin_dir_path(dirname(__FILE__)) . 'admin/views/login-list-table.php';
        }
        public function render_login_lists() {
            echo "hellos";
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


 
                $this->list_table = new LoginListTable($this->plugin_name, $this->version, $this->plugin_text_domain);
            $this->list_table->prepare_items();
        }

}
