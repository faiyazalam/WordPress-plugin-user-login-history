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

/**
 * Handle admin notice functionality.
 *
 * @author    Er Faiyaz Alam
 */
class Admin_Notice {

	/**
	 * Holds the suffix name of the transient.
	 */
	const TRANSIENT_SUFFIX = '_admin_notice_transient';

	/**
	 * Holds the interval of the transient.
	 */
	const TRANSIENT_INTERVAL = 120;

	/**
	 * Holds the name of transient.
	 *
	 * @var string
	 */
	private $transient_name;

	/**
	 * The ID of this plugin.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param       string $plugin_name        The name of this plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->transient_name = $this->plugin_name . self::TRANSIENT_SUFFIX;
	}

	/**
	 * Add notice to the transient.
	 *
	 * @param string $message The message.
	 * @param string $type Any message type string. This will be used as a class attribute. Default is success.
	 */
	public function add_notice( $message, $type = 'success' ) {
		$notices = get_transient( $this->transient_name );
		if ( false === $notices ) {
			$new_notices[] = array( $message, $type );
			set_transient( $this->transient_name, $new_notices, self::TRANSIENT_INTERVAL );
		} else {
			$notices[] = array( $message, $type );
			set_transient( $this->transient_name, $notices, self::TRANSIENT_INTERVAL );
		}
	}

	/**
	 * Hooked with admin_notices and network_admin_notices actions
	 */
	public function show_notice() {
		$notices = get_transient( $this->transient_name );
		if ( false !== $notices ) {
			foreach ( $notices as $notice ) {
				if ( ! empty( $notice[1] && ! empty( $notice[0] ) ) ) {
					echo '<div class="notice notice-' . $notice[1] . ' is-dismissible"><p>' . $notice[0] . '</p></div>';
				}
			}
			delete_transient( $this->transient_name );
		}
	}

}
