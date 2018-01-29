<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.0.0
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
 * @author     Er Faiyaz Alam <support@userloginhistory.com>
 */
class User_Login_History_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
        
        public function init() {
            $this->update_user_timezone();
        }

        /**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
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
    public function shortcode_user_table() {
        if(!is_user_logged_in()){
            return;
        }
        require_once plugin_dir_path(__FILE__) . 'partials/user-login-history-public-display.php';
        wp_enqueue_script($this->plugin_name . '-jquery-ui.min.js');
        wp_enqueue_style($this->plugin_name . '-jquery-ui.min.css');
        wp_enqueue_script($this->plugin_name . '-custom.js');
    }

    /**
     * Frontend user can update his timezone.
     *
     */
    public function update_user_timezone() {
        if(!isset($_POST[$this->plugin_name."_update_user_timezone"]))
        {
            return;
        }
        
        if(!wp_verify_nonce(!empty($_POST['_wpnonce'])?$_POST['_wpnonce']:"", $this->plugin_name."_update_user_timezone")){
            wp_die('Nonce error');
        }
            global $current_user;
            //Do not replace 'USER_LOGIN_HISTORY_USER_META_PREFIX' with '$plugin_name' here to avoid backward compatibility issue.
            update_user_meta($current_user->ID, USER_LOGIN_HISTORY_USER_META_PREFIX . "user_timezone", !empty($_POST[$this->plugin_name."-timezone"])?$_POST[$this->plugin_name."-timezone"]:"");
            wp_safe_redirect( esc_url_raw(add_query_arg()) );exit;
    }

}
