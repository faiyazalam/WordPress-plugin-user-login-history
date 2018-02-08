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
 * @package    User_Login_History
 *
 * @wordpress-plugin
 * Plugin Name:       User Login History
 * Plugin URI:        www.userloginhistory.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.7
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
 * Plugin Constants.
 */
define('USER_LOGIN_HISTORY_NAME', 'user-login-history'); //used as prefix with style and script
define('USER_LOGIN_HISTORY_VERSION', '1.7.0');
define('USER_LOGIN_HISTORY_TABLE_NAME', 'fa_user_logins');
define('USER_LOGIN_HISTORY_OPTION_PREFIX', 'fa_userloginhostory_');
define('USER_LOGIN_HISTORY_USERMETA_PREFIX', 'fa_userloginhostory_'); 

/**
 * The code that runs during plugin activation.
 */
function activate_user_login_history($network_wide) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-user-login-history-activator.php';
    User_Login_History_Activator::activate($network_wide);
}

register_activation_hook(__FILE__, 'activate_user_login_history');

if (is_multisite() && is_network_admin()) {
    add_action('wpmu_new_blog', 'on_create_blog', 10, 6);
}

function on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-user-login-history-activator.php';
    User_Login_History_Activator::on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta);
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
 */
function run_user_login_history() {
    $plugin = new User_Login_History();
    $plugin->run();
}

run_user_login_history();
?>