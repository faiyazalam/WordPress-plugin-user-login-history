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

use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
use User_Login_History\Inc\Common\Abstracts\User_Profile as User_Profile_Abstract;

/**
 * Add Timezone dropdown on user profile page in admin.
 */
class User_Profile extends User_Profile_Abstract {

	/**
	 * Hooked with show_user_profile action action.
	 *
	 * @param type $user The user.
	 */
	public function show_extra_profile_fields( $user ) {
		$this->set_user_id( $user->ID );
		?>
		<h3 id="<?php echo $this->plugin_name; ?>"><?php echo NS\PLUGIN_NAME; ?> (<?php esc_html_e( 'DEPRECATED! Will be removed in 3.0', 'faulh' ); ?>)</h3>

		<table class="faulh-form-table">
			<tr class="<?php echo 'user-' . $this->get_usermeta_key_timezone() . '-wrap'; ?>">
				<th><label for="<?php echo $this->get_usermeta_key_timezone(); ?>"><?php esc_html_e( 'Timezone', 'faulh' ); ?></label></th>
				<td>
					<select required="required" id="<?php echo $this->get_usermeta_key_timezone(); ?>" name="<?php echo $this->get_usermeta_key_timezone(); ?>">
						<option value=""><?php esc_html_e( 'Select Timezone', 'faulh' ); ?></option>
						<?php
						Template_Helper::dropdown_timezones( $this->get_user_timezone() );
						?>
					</select>
					<div><?php esc_html_e( 'This is used to convert date-time (e.g. login time, last seen time etc.) on the listing table.', 'faulh' ); ?></div>
				</td>
			</tr>

		</table>
		<?php
	}

	/**
	 * Hooked with edit_user_profile_update action.
	 *
	 * @param int $user_id The user id.
	 * @return boolean
	 */
	public function update_profile_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		$this->set_user_id( $user_id );

		$this->update_usermeta_key_timezone();
		$this->delete_old_usermeta_key_timezone();
	}

}
