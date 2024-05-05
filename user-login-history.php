<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://userloginhistory.com
 * @package           User_Login_History
 *
 * @wordpress-plugin
 * Plugin Name:       User Login History
 * Plugin URI:        http://userloginhistory.com/home/
 * Description:       Helps you to know your website's visitors by tracking their login related information like login/logout time, country, browser and many more.
 * Version:           2.1.4
 * Author:            Er Faiyaz Alam
 * Author URI:        http://userloginhistory.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       faulh
 * Domain Path:       /languages
 */

namespace User_Login_History;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( defined( 'User_Login_History_Pro\NS' ) ) {
	return;
}


/**
 * Plugin Constants
 */
define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );


define( NS . 'USER_LOGIN_HISTORY', 'faulh' );

define( NS . 'PLUGIN_VERSION', '2.1.4' );

define( NS . 'USER_LOGIN_HISTORY_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'USER_LOGIN_HISTORY_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( NS . 'PLUGIN_NAME', 'User Login History' );
define( NS . 'PLUGIN_TEXT_DOMAIN', 'faulh' );
define( NS . 'PLUGIN_OPTION_NAME_VERSION', 'fa_userloginhostory_version' );
define( NS . 'PLUGIN_TABLE_FA_USER_LOGINS', 'fa_user_logins' );
define( NS . 'DEFAULT_IS_STATUS_ONLINE_MIN', '2' );
define( NS . 'DEFAULT_IS_STATUS_IDLE_MIN', '30' );
define( NS . 'PLUGIN_BOOTSTRAP_FILE_PATH_FROM_PLUGIN_FOLDER', basename( __DIR__ ) . '/' . basename( __FILE__ ) );
define( NS . 'PLUGIN_GO_PRO_LINK', "https://www.userloginhistory.com/pricing?ref=plugin" );
define( NS . 'PLUGIN_FEATURE_LINK', "https://www.userloginhistory.com/features?ref=plugin" );

/**
 * Autoload Classes
 */
require_once USER_LOGIN_HISTORY_DIR . 'inc/libraries/autoloader.php';
require_once USER_LOGIN_HISTORY_DIR.'inc/common/vendors/csv/autoload.php';

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */
register_activation_hook( __FILE__, array( NS . 'Inc\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */
register_deactivation_hook( __FILE__, array( NS . 'Inc\Core\Deactivator', 'deactivate' ) );

/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 */
class User_Login_History {

	/**
	 * The instance of the plugin.
	 *
	 * @var      Init $init Instance of the plugin.
	 */
	private static $init;

	/**
	 * Loads the plugin
	 *
	 * @access    public
	 */
	public static function init() {

		if ( null === self::$init ) {
			self::$init = new Inc\Core\Init();
			self::$init->run();
		}

		return self::$init;
	}

}

/**
 * Begins execution of the plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 * */
function wp_user_login_history_init() {
	return User_Login_History::init();
}

$min_php = '7.4';

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
	wp_user_login_history_init();
}

 
