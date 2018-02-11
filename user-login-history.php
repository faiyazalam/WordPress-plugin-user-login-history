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
 * @package    Faulh
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
 * Text Domain:       faulh
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * Plugin Constants.
 */
define('USER_LOGIN_HISTORY_VERSION', '1.7.0');
define('USER_LOGIN_HISTORY_TABLE_NAME', 'fa_user_logins');
define('USER_LOGIN_HISTORY_OPTION_NAME_VERSION', 'fa_userloginhostory_version');

/**
 * The code that runs during plugin activation.
 */
function activate_faulh($network_wide) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-faulh-activator.php';
    Faulh_Activator::activate($network_wide);
}
register_activation_hook(__FILE__, 'activate_faulh');

if (is_multisite() && is_network_admin()) {
    add_action('wpmu_new_blog', 'on_create_blog', 10, 6);
}

function on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-faulh-activator.php';
    Faulh_Activator::on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta);
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-faulh.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_faulh() {
    $plugin = new Faulh();
    $plugin->run();
}

run_faulh();
?>