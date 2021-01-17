<?php
/**
 * Backend Functionality.
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Common\Helpers;

if ( ! class_exists( 'Browser' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendors/Browser/Browser.php';
}

/**
 * Backend Functionality.
 */
class Browser extends \Browser {

}
