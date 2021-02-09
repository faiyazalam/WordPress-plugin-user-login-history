<?php
/**
 * Backend Functionality
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Admin;

use User_Login_History as NS;
use User_Login_History\Inc\Core\Activator;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;
use User_Login_History\Inc\Admin\Listing_Table_Csv;
use User_Login_History\Inc\Admin\Admin_Login_List_Table;
use User_Login_History\Inc\Admin\Network_Admin_Login_List_Table;
use User_Login_History\Inc\Admin\User_Profile;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv;
use User_Login_History\Inc\Common\Login_Tracker;
use User_Login_History\Inc\Admin\Settings as Admin_Settings;

/**
 * Backend Functionality.
 */
class Admin {

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
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Variables to store instance of other classes.
	 *
	 * @var User_Profile
	 */
	private $user_profile;

	/**
	 * The listing table csv object.
	 *
	 * @var Listing_Table_Csv
	 */
	private $listing_table_csv;

	/**
	 * The admin settings object.
	 *
	 * @var Admin_Settings
	 */
	private $admin_settings;

	/**
	 * The admin notice.
	 *
	 * @var Admin_Notice
	 */
	private $admin_notice;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param type                                       $plugin_name The name of this plugin.
	 * @param type                                       $version The version of this plugin.
	 * @param User_Profile                               $user_profile The user profile.
	 * @param Listing_Table_Csv                          $listing_table_csv The csv object.
	 * @param Admin_Settings                             $admin_settings The settings object.
	 * @param \User_Login_History\Inc\Admin\Admin_Notice $admin_notice The notice object.
	 */
	public function __construct(
			$plugin_name, $version, User_Profile $user_profile, Listing_Table_Csv $listing_table_csv, Admin_Settings $admin_settings, Admin_Notice $admin_notice
	) {

		$this->plugin_name       = $plugin_name;
		$this->version           = $version;
		$this->user_profile      = $user_profile;
		$this->listing_table_csv = $listing_table_csv;
		$this->admin_notice      = $admin_notice;
		$this->admin_settings    = $admin_settings;
	}

	/**
	 * Hooked with admin_init action.
	 */
	public function admin_init() {
		$this->csv_export_login_list();
	}

	/**
	 * Exports the login list in csv format
	 */
	private function csv_export_login_list() {
		if ( $this->is_plugin_login_list_page() ) {
			$login_list = $this->get_login_list_table();

			$this->listing_table_csv->set_listing_table( $login_list );

			if ( ! $this->listing_table_csv->is_request_for_csv() ) {
				return;
			}

			$login_list->set_timezone( $this->user_profile->get_user_timezone() );
			$this->listing_table_csv->init();
		}
	}

	/**
	 * Checks whether the current page is the login listing page.
	 *
	 * @return bool
	 */
	private function is_plugin_login_list_page() {
		global $pagenow, $plugin_page;
		return 'admin.php' == $pagenow && $this->plugin_name . '-login-listing' == $plugin_page;
	}

	/**
	 * Gets the slug of the login listing page.
	 *
	 * @return string
	 */
	private function get_plugin_login_list_page_slug() {
		return $this->plugin_name . '-login-listing';
	}

	/**
	 * Enqueue scripts for the login list page only.
	 */
	private function enqueue_scripts_for_plugin_login_list_page() {
		if ( $this->is_plugin_login_list_page() ) {
			wp_enqueue_script( $this->plugin_name . '-admin-jquery-ui.min', plugin_dir_url( __FILE__ ) . 'js/jquery-ui.min.js', array(), $this->version, 'all' );
			wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/admin.js', array(), $this->version, 'all' );
			wp_localize_script(
				$this->plugin_name . '-admin',
				'admin_custom_object',
				array(
					'delete_confirm_message'     => esc_html__( 'Are your sure?', 'faulh' ),
					'invalid_date_range_message' => esc_html__( 'Please provide a valid date range.', 'faulh' ),
					'admin_url'                  => admin_url(),
					'plugin_name'                => $this->plugin_name,
					'show_advanced_filters'      => esc_html__( 'Show Advanced Filters', 'faulh' ),
					'hide_advanced_filters'      => esc_html__( 'Hide Advanced Filters', 'faulh' ),
				)
			);
		}
	}

	/**
	 * Enqueue styles for the user profile page only.
	 */
	private function enqueue_styles_for_user_profile() {
		global $pagenow;

		if ( 'profile.php' == $pagenow ) {
			wp_enqueue_style( $this->plugin_name . '-user-profile.css', plugin_dir_url( __FILE__ ) . 'css/user-profile.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Enqueue styles for the login list page only.
	 */
	private function enqueue_styles_for_plugin_login_list_page() {

		global $pagenow, $plugin_page;

		if ( 'admin.php' == $pagenow && $this->plugin_name . '-pro' == $plugin_page ) {
			wp_enqueue_style( $this->plugin_name . '-admin-bt', '//maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-admin-fa', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-admin-gf', '//fonts.googleapis.com/css?family=Poppins&display=swap', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-admin-gp', plugin_dir_url( __FILE__ ) . 'css/go-pro.css', array(), $this->version, 'all' );
		}

		if ( $this->is_plugin_login_list_page() ) {
			wp_enqueue_style( $this->plugin_name . '-admin-jquery-ui.min', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		$this->enqueue_styles_for_plugin_login_list_page();
		$this->enqueue_styles_for_user_profile();
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		$this->enqueue_scripts_for_plugin_login_list_page();
	}

	/**
	 * Hooked with admin_menu action.
	 */
	public function admin_menu() {

		$menu_slug = $this->get_plugin_login_list_page_slug();
		$hook      = add_menu_page(
			esc_html__( 'Login List', 'faulh' ),
			NS\PLUGIN_NAME,
			'administrator',
			$menu_slug,
			array( $this, 'render_login_list' ),
			plugin_dir_url( __FILE__ ) . 'images/icon.png',
			30
		);
		add_submenu_page( $menu_slug, esc_html__( 'Login List', 'faulh' ), esc_html__( 'Login List', 'faulh' ), 'administrator', $menu_slug, array( $this, 'render_login_list' ) );
		add_submenu_page( $menu_slug, esc_html__( 'Pro Features', 'faulh' ), esc_html__( 'Pro Features', 'faulh' ), 'administrator', $this->plugin_name . '-pro', array( $this, 'render_pro' ) );

		add_action( "load-$hook", array( $this, 'screen_option' ) );
	}

	/**
	 * Render the login listing page
	 */
	public function render_login_list() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views/login-list-table.php';
	}

	/**
	 * Render the login listing page
	 */
	public function render_pro() {
		require plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views/pro.php';
	}

	/**
	 * Hooked with the filter - set-screen-option.
	 *
	 * @param mixed  $status The value to save instead of the option value.
	 * @param string $option The option name.
	 * @param int    $value The option value.
	 * @return int The value.
	 */
	public function set_screen( $status, $option, $value ) {
		return $value;
	}

	/**
	 * Hooked with load-$hook action
	 */
	public function screen_option() {
		$option = 'per_page';
		$args   = array(
			'label'   => __( 'Show Records Per Page', 'faulh' ),
			'default' => 20,
			'option'  => $this->plugin_name . '_rows_per_page',
		);

		add_screen_option( $option, $args );
		$this->login_list_table = $this->get_login_list_table();
		$this->login_list_table->process_bulk_action();
		$this->login_list_table->process_single_action();

		$online_duration = $this->admin_settings->get_online_duration();
		$this->login_list_table->set_online_duration( $online_duration['online'] );
		$this->login_list_table->set_idle_duration( $online_duration['idle'] );
		$this->login_list_table->set_timezone( $this->user_profile->get_user_timezone() );
		$this->login_list_table->prepare_items();
	}

	/**
	 * Get the instance of the login list table class.
	 *
	 * @return Login_List_Table
	 */
	private function get_login_list_table() {
		return is_network_admin() ? new Network_Admin_Login_List_Table( $this->plugin_name, $this->version, $this->admin_notice ) : new Admin_Login_List_Table( $this->plugin_name, $this->version, $this->admin_notice );
	}

	/**
	 * Check if update available.
	 * If yes, update DB.
	 */
	public function check_update_version() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}
		// Current version.
		$current_version = get_option( NS\PLUGIN_OPTION_NAME_VERSION );
		// If the version is older.
		if ( $current_version && version_compare( $current_version, $this->version, '<' ) ) {

			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active_for_network( NS\PLUGIN_BOOTSTRAP_FILE_PATH_FROM_PLUGIN_FOLDER ) ) {
				$blog_ids = Db_Helper::get_blog_ids_by_site_id();
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					Activator::create_table();
					Activator::update_options();
				}
				restore_current_blog();
			} else {
				Activator::create_table();
				Activator::update_options();
			}
		}
	}
 
 public function add_action_links($actions) {
        $links = array(
            sprintf('<a target="_blank" href="%s">%s</a>', NS\PLUGIN_GO_PRO_LINK, esc_html__('Buy Pro', 'faulh')),
        );
        return array_merge($actions, $links);
    }

}
