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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- no need of nonce to prefill the form.
$faulh_the_get = $_GET;
?>
<div class="faulh_container">
	<form id="filter_form" method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $faulh_the_get['page'] ) ) ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $this->login_list_table->get_csv_field_name() ); ?>" id="csv" value="">
		<input type="hidden" name="_wpnonce" id="csv_nonce" value="<?php echo esc_attr( wp_create_nonce( $this->login_list_table->get_csv_nonce_name() ) ); ?>">
		<div class="basic_search">
			<input class="date-input" readonly autocomplete="off" placeholder="<?php esc_html_e( 'From', 'user-login-history' ); ?>" id="date_from" name="date_from" value="<?php echo isset( $faulh_the_get['date_from'] ) ? esc_attr( $faulh_the_get['date_from'] ) : ''; ?>" >
			<input class="date-input" readonly autocomplete="off" placeholder="<?php esc_html_e( 'To', 'user-login-history' ); ?>" name="date_to" id="date_to" value="<?php echo isset( $faulh_the_get['date_to'] ) ? esc_attr( $faulh_the_get['date_to'] ) : ''; ?>" >
			<select  name="date_type" >
				<?php Template_Helper::dropdown_time_field_types( isset( $faulh_the_get['date_type'] ) ? $faulh_the_get['date_type'] : null ); ?>
			</select>
		</div>
		<div id="faulh_advanced_search">
			<div><label for="user_id"><?php esc_html_e( 'User ID', 'user-login-history' ); ?></label><input id="user_id" min="1" type="number" placeholder="<?php esc_html_e( 'Enter User ID', 'user-login-history' ); ?>" name="user_id" value="<?php echo isset( $faulh_the_get['user_id'] ) ? esc_attr( $faulh_the_get['user_id'] ) : ''; ?>" ></div>
			<div><label for="username"><?php esc_html_e( 'Username', 'user-login-history' ); ?></label><input id="username" placeholder="<?php esc_html_e( 'Enter Username', 'user-login-history' ); ?>" name="username" value="<?php echo isset( $faulh_the_get['username'] ) ? esc_attr( $faulh_the_get['username'] ) : ''; ?>" ></div>
			<div><label for="country" ><?php esc_html_e( 'Country', 'user-login-history' ); ?></label><input id="country" placeholder="<?php esc_html_e( 'Enter Country', 'user-login-history' ); ?>" name="country_name" value="<?php echo isset( $faulh_the_get['country_name'] ) ? esc_attr( $faulh_the_get['country_name'] ) : ''; ?>" ></div>
			<div><label for="browser" ><?php esc_html_e( 'Browser', 'user-login-history' ); ?></label><input id="browser" placeholder="<?php esc_html_e( 'Enter Browser', 'user-login-history' ); ?>" name="browser" value="<?php echo isset( $faulh_the_get['browser'] ) ? esc_attr( $faulh_the_get['browser'] ) : ''; ?>" ></div>
			<div><label for="operating_system" ><?php esc_html_e( 'Operating System', 'user-login-history' ); ?></label><input  id="operating_system" placeholder="<?php esc_html_e( 'Enter Operating System', 'user-login-history' ); ?>" name="operating_system" value="<?php echo isset( $faulh_the_get['operating_system'] ) ? esc_attr( $faulh_the_get['operating_system'] ) : ''; ?>" ></div>
			<div><label for="ip_address" ><?php esc_html_e( 'IP Address', 'user-login-history' ); ?></label><input id="ip_address" placeholder="<?php esc_html_e( 'Enter IP Address', 'user-login-history' ); ?>" name="ip_address" value="<?php echo isset( $faulh_the_get['ip_address'] ) ? esc_attr( $faulh_the_get['ip_address'] ) : ''; ?>" ></div>
			<?php if ( is_network_admin() ) { ?>
			<div><label for="blog_id" ><?php esc_html_e( 'Blog ID', 'user-login-history' ); ?></label><input id="blog_id" placeholder="<?php esc_html_e( 'Blog ID', 'user-login-history' ); ?>" name="blog_id" value="<?php echo isset( $faulh_the_get['blog_id'] ) ? esc_attr( $faulh_the_get['blog_id'] ) : ''; ?>" ></div>
				<?php
			}
			?>
			<div><label for="timezone" ><?php esc_html_e( 'Timezone', 'user-login-history' ); ?></label><select id="timezone"  name="timezone">
					<?php $faulh_selected_timezone = isset( $faulh_the_get['timezone'] ) ? $faulh_the_get['timezone'] : ''; ?>
					<option value=""><?php esc_html_e( 'Select Timezone', 'user-login-history' ); ?></option>
					<option value="unknown" <?php selected( $faulh_selected_timezone, 'unknown' ); ?> ><?php esc_html_e( 'Unknown', 'user-login-history' ); ?></option>
					<?php Template_Helper::dropdown_timezones( $faulh_selected_timezone ); ?>
				</select></div>
			<div><label for="old_role" ><?php esc_html_e( 'Old Role', 'user-login-history' ); ?></label><select id="old_role"  name="old_role">
					<option value=""><?php esc_html_e( 'Select Old Role', 'user-login-history' ); ?></option>
					<?php
					$faulh_selected_old_role = isset( $faulh_the_get['old_role'] ) ? $faulh_the_get['old_role'] : null;
					wp_dropdown_roles( $faulh_selected_old_role );
					?>
					<?php if ( is_network_admin() ) { ?>
					<option value="superadmin" <?php selected( $faulh_selected_old_role, 'superadmin' ); ?> ><?php esc_html_e( 'Super Administrator', 'user-login-history' ); ?></option>  <?php } ?>
				</select></div>
			<div><label for="login_status" ><?php esc_html_e( 'Login Status', 'user-login-history' ); ?></label><select id="login_status" name="login_status">
					<option value=""><?php esc_html_e( 'Select Login Status', 'user-login-history' ); ?></option>
					<?php $faulh_selected_login_status = isset( $faulh_the_get['login_status'] ) ? $faulh_the_get['login_status'] : ''; ?>
					<option value="unknown" <?php selected( $faulh_selected_login_status, 'unknown' ); ?> ><?php esc_html_e( 'Unknown', 'user-login-history' ); ?></option>
					<?php Template_Helper::dropdown_login_statuses( $faulh_selected_login_status ); ?>
				</select></div>
			<?php if ( is_network_admin() ) { ?>
					<div><label for="is_super_admin" ><?php esc_html_e( 'Super Admin', 'user-login-history' ); ?></label>
						<select id="is_super_admin" name="is_super_admin" >
							<option value=""><?php esc_html_e( 'Select Super Admin', 'user-login-history' ); ?></option>

							<?php
							Template_Helper::dropdown_is_super_admin( isset( $faulh_the_get['is_super_admin'] ) ? $faulh_the_get['is_super_admin'] : null );
							?>
						</select>
					</div>
				<?php } ?>

		</div>

		<fieldset>
			<a class="faulh-cancel-btn" href="<?php echo esc_url( 'admin.php?page=' . $faulh_the_get['page'] ); ?>"><?php esc_html_e( 'CANCEL', 'user-login-history' ); ?></a>
			<input id="submit" type="submit" name="submit" value="<?php esc_html_e( 'FILTER', 'user-login-history' ); ?>" />
			<a id="download_csv_link" href="javascript::void(0)"><?php esc_html_e( 'DOWNLOAD CSV', 'user-login-history' ); ?></a> 
			<a id="<?php echo esc_attr( $this->plugin_name ); ?>_show_hide_advanced_search" href="#"><?php esc_html_e( 'Show Advanced Filters', 'user-login-history' ); ?></a>
		</fieldset>

	</form>
</div>
