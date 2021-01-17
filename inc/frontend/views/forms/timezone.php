<?php
/**
 * Frontend View Functionality
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
?>
<form method="post" id="<?php echo $this->plugin_name; ?>_update_user_timezone">
	<fieldset>
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( $this->plugin_name . '_update_user_timezone' ); ?>">
		<p><?php esc_html_e( 'This table is showing time in the timezone', 'faulh' ); ?> - <strong><?php echo $this->get_list_table()->get_table_timezone(); ?></strong></p>
		<select style="width:78%" required="required"  id="select_timezone" name="<?php echo $this->get_user_profile()->get_usermeta_key_timezone(); ?>">
			<option value=""><?php esc_html_e( 'Select Timezone', 'faulh' ); ?></option>
			<?php
			Template_Helper::dropdown_timezones( $this->get_list_table()->get_table_timezone() );
			?>
		</select>
		<input type="submit" name="<?php echo $this->plugin_name . '_update_user_timezone'; ?>" value="<?php echo esc_html__( 'Apply', 'faulh' ); ?>">

	</fieldset>
</form>
