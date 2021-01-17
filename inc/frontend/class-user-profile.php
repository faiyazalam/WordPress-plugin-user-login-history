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

		if ( ! $this->is_valid_request_for_update() ) {
			return false;
		}

		$this->update_usermeta_key_timezone();
		$this->delete_old_usermeta_key_timezone( $this->get_user_id() );

		wp_safe_redirect( esc_url_raw( add_query_arg() ) );
		exit;
	}

	/**
	 * Validate the request for timezone update
	 *
	 * @access private
	 */
	private function is_valid_request_for_update() {
		$key   = $this->plugin_name . '_update_user_timezone';
		$nonce = '_wpnonce';
		return isset( $_POST[ $key ] ) && ! empty( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], $key ) && $this->get_user_id() && current_user_can( 'edit_user', $this->get_user_id() );
	}

}
