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

namespace User_Login_History\Inc\Core;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 */
class Internationalization_I18n {

	/**
	 * The text domain of the plugin.
	 *
	 * @access   protected
	 * @var      string    $text_domain    The text domain of the plugin.
	 */
	private $text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string $plugin_text_domain       The text domain of this plugin.
	 */
	public function __construct( $plugin_text_domain ) {

		$this->text_domain = $plugin_text_domain;
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->text_domain,
			false,
			dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/'
		);
	}

}
