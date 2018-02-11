<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    Faulh
 * @subpackage Faulh/includes
 * @access private
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @package    Faulh
 * @subpackage Faulh/includes
 * @author     Er Faiyaz Alam
 */
class Faulh_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'faulh',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
