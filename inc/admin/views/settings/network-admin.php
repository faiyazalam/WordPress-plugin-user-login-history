<?php
/**
 * Backend View Functionality
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
?>
<div class="wrap">
	<?php echo Template_Helper::head( esc_html__( 'Network Settings', 'faulh' ) ); ?>
	<form method="post">
		<input type="hidden" name="<?php echo $this->get_form_name(); ?>" >
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php esc_html_e( 'Block User', 'faulh' ); ?></th>
					<td>
						<label>
							<input name="block_user" type="checkbox" id="block_user" value="<?php echo esc_attr( $this->get_settings( 'block_user' ) ); ?>" <?php checked( $this->get_settings( 'block_user' ), 1 ); ?>>
							<?php esc_html_e( 'User will not be able to login on another blog on the network.', 'faulh' ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Message for Blocked User', 'faulh' ); ?></th>
					<td>
						<textarea name="block_user_message" id="block_user_message" cols="45" rows="5"><?php echo esc_attr( $this->get_settings( 'block_user_message' ) ); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
		<?php wp_nonce_field( $this->get_form_nonce_name(), $this->get_form_nonce_name() ); ?>
		<p class="submit">
			<?php submit_button(); ?>
		</p>	
	</form>
</div>
