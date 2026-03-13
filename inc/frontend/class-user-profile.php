<?php
/**
 * Frontend Functionality
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Frontend;

use User_Login_History\Inc\Common\Abstracts\User_Profile as User_Profile_Abstract;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend Functionality
 */
class User_Profile extends User_Profile_Abstract {

	/**
	 * Check form submission and nonce and then update user timezone.
	 * This is used to handle request from front-end.
	 * After processing request, it redirects to current page.
	 *
	 * @access public
	 */
	public function update_user_timezone() {

		$key   = $this->plugin_name . '_update_user_timezone';
		$nonce = '_wpnonce';

		if ( ! isset( $_POST[ $key ] ) ) {
			return;
		}

		if ( empty( $_POST[ $nonce ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce ] ) ), $key ) ) {
			return;
		}

		if ( ! $this->get_user_id() ) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $this->get_user_id() ) ) {
			return;
		}

		$this->update_usermeta_key_timezone();
		$this->delete_old_usermeta_key_timezone( $this->get_user_id() );

		wp_safe_redirect( esc_url_raw( add_query_arg() ) );
		exit;
	}
}
