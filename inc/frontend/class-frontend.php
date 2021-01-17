<?php
/**
 * Frontend Functionality
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Frontend;

use User_Login_History\Inc\Common\Abstracts\User_Profile as UserProfileAbstract;
use User_Login_History\Inc\Frontend\Frontend_Login_List_Table as List_Table;

/**
 * Frontend Functionality.
 */
class Frontend {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $plugin_version
	 */
	private $version;

	/**
	 * The user profile.
	 *
	 * @var UserProfileAbstract
	 */
	private $user_profile;

	/**
	 * The listing table.
	 *
	 * @var List_Table
	 */
	private $list_table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string              $plugin_name The name of this plugin.
	 * @param string              $version     The version of this plugin.
	 * @param List_Table          $list_table The listing table.
	 * @param UserProfileAbstract $user_profile The user profile.
	 */
	public function __construct( $plugin_name, $version, List_Table $list_table, UserProfileAbstract $user_profile ) {

		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->list_table   = $list_table;
		$this->user_profile = $user_profile;
	}

	/**
	 * Get the user profile.
	 *
	 * @return UserProfileAbstract
	 */
	public function get_user_profile() {
		return $this->user_profile;
	}

	/**
	 * Get the listing table.
	 *
	 * @return List_Table
	 */
	public function get_list_table() {
		return $this->list_table;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		wp_register_style( $this->plugin_name . '-public-jquery-ui.min', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version, 'all' );
		wp_register_style( $this->plugin_name . '-public-custom', plugin_dir_url( __FILE__ ) . 'css/custom.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->plugin_name . '-public-jquery-ui.min.js', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.min.js', array(), $this->version, 'all' );
		wp_register_script( $this->plugin_name . '-public-custom.js', plugin_dir_url( __FILE__ ) . 'js/custom.js', array(), $this->version, 'all' );
		wp_localize_script(
			$this->plugin_name . '-public-custom.js',
			'public_custom_object',
			array(
				'invalid_date_range_message' => esc_html__( 'Please provide a valid date range.', 'faulh' ),
			)
		);
	}

	/**
	 * Shortcode to show listing table for frontend user.
	 *
	 * @global type $current_user
	 * @param array $attr The attributes.
	 * @return string
	 */
	public function shortcode_user_table( $attr ) {
		if ( ! is_user_logged_in() ) {
			return;
		}
		global $current_user;

		$this->get_list_table()->set_table_timezone( $this->get_user_profile()->get_user_timezone() );

		$default_args = array(
			'title'                  => '',
			'reset_link'             => '',
			'limit'                  => 20,
			'date_format'            => 'Y-m-d',
			'time_format'            => 'H:i:s',
			'show_timezone_selector' => 'true',
			'columns'                => 'operating_system,browser,time_login,time_logout',
		);
		$attributes   = shortcode_atts( $default_args, $attr );

		$attributes = array_map( 'trim', $attributes );

		if ( ! empty( $attributes['columns'] ) ) {
			$this->get_list_table()->set_allowed_columns( $attributes['columns'] );
		}

		if ( ! empty( $attributes['limit'] ) ) {
			$this->get_list_table()->set_limit( $attributes['limit'] );
		}

		if ( ! empty( $attributes['date_format'] ) ) {
			$this->get_list_table()->set_table_date_format( $attributes['date_format'] );
		}

		if ( ! empty( $attributes['time_format'] ) ) {
			$this->get_list_table()->set_table_time_format( $attributes['time_format'] );
		}

		$reset_url       = ! empty( $attributes['reset_link'] ) ? home_url( $attributes['reset_link'] ) : get_permalink();
		$_GET['user_id'] = $current_user->ID; // to fetch records of current logged in user only.

		wp_enqueue_style( $this->plugin_name . '-public-jquery-ui.min' );
		wp_enqueue_script( $this->plugin_name . '-public-jquery-ui.min.js' );
		wp_enqueue_script( $this->plugin_name . '-public-custom.js' );
		wp_enqueue_style( $this->plugin_name . '-public-custom' );
		ob_start();
		$this->get_list_table()->prepare_items();
		require_once plugin_dir_path( __FILE__ ) . 'views/listing.php';
		return ob_get_clean();
	}

}
