<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/faiyazalam
 * @since             1.4.1
 * @package           User_Login_History
 *
 * @wordpress-plugin
 * Plugin Name:       User Login History
 * Plugin URI:        https://github.com/faiyazalam
 * Description:       Easily tracks user login with a set of multiple attributes like ip, login/logout/last-seen time, country, username, user role, browser, OS etc.
 * Version:           1.4.1
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
require_once plugin_dir_path(__FILE__) . 'includes/user-login-history-config.php';

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-user-login-history-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-user-login-history-deactivator.php';

/** This action is documented in includes/class-user-login-history-activator.php */
register_activation_hook(__FILE__, array('User_Login_History_Activator', 'activate'));

/** This action is documented in includes/class-user-login-history-deactivator.php */
register_deactivation_hook(__FILE__, array('User_Login_History_Deactivator', 'deactivate'));

/** The template tags accessible for public use */
//require_once plugin_dir_path( __FILE__ ) . 'public/user-login-history-functions.php';

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-user-login-history.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.4.1
 */
function run_User_Login_History() {
    $plugin = new User_Login_History();
    $plugin->run();
}
run_User_Login_History();