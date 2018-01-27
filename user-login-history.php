<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/faiyazalam
 * @since             1.0.0
 * @package           User_Login_History
 *
 * @wordpress-plugin
 * Plugin Name:       User Login History
 * Plugin URI:        www.userloginhistory.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           100.00
 * Author:            Er Faiyaz Alam
 * Author URI:        https://github.com/faiyazalam
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       user-login-history
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('USER_LOGIN_HISTORY_NAME', 'user-login-history'); //used as prefix with style and script
define('USER_LOGIN_HISTORY_VERSION', '1.0.0');
define('USER_LOGIN_HISTORY_TABLE_NAME', 'fa_user_logins');
define('USER_LOGIN_HISTORY_OPTION_PREFIX', 'fa_userloginhostory_'); // used as prefix with option name, usermeta name, nonce name etc.

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-user-login-history-activator.php
 */
function activate_user_login_history($network_wide) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-user-login-history-activator.php';
    User_Login_History_Activator::activate($network_wide);
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-user-login-history-deactivator.php
 */
//function deactivate_user_login_history() {
//	require_once plugin_dir_path( __FILE__ ) . 'includes/class-user-login-history-deactivator.php';
//	User_Login_History_Deactivator::deactivate();
//}

register_activation_hook(__FILE__, 'activate_user_login_history');
//register_deactivation_hook( __FILE__, 'deactivate_user_login_history' );

if (is_multisite()) {
    add_action('wpmu_new_blog', array('User_Login_History_Activator', 'on_create_blog'), 10, 6);
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-user-login-history.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_user_login_history() {

    $plugin = new User_Login_History();
    $plugin->run();
}

run_user_login_history();
?>