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
<div class="faulh_container">
	<form id="filter_form" method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>" />
		<input type="hidden" name="<?php echo $this->login_list_table->get_csv_field_name(); ?>" id="csv" value="">
		<input type="hidden" name="_wpnonce" id="csv_nonce" value="<?php echo wp_create_nonce( $this->login_list_table->get_csv_nonce_name() ); ?>">
		<div class="basic_search">
			<input class="date-input" readonly autocomplete="off" placeholder="<?php esc_html_e( 'From', 'faulh' ); ?>" id="date_from" name="date_from" value="<?php echo isset( $_GET['date_from'] ) ? esc_attr( $_GET['date_from'] ) : ''; ?>" >
			<input class="date-input" readonly autocomplete="off" placeholder="<?php esc_html_e( 'To', 'faulh' ); ?>" name="date_to" id="date_to" value="<?php echo isset( $_GET['date_to'] ) ? esc_attr( $_GET['date_to'] ) : ''; ?>" >
			<select  name="date_type" >
				<?php Template_Helper::dropdown_time_field_types( isset( $_GET['date_type'] ) ? $_GET['date_type'] : null ); ?>
			</select>
		</div>
		<div id="faulh_advanced_search">
			<div><label for="user_id"><?php esc_html_e( 'User ID', 'faulh' ); ?></label><input id="user_id" min="1" type="number" placeholder="<?php esc_html_e( 'Enter User ID', 'faulh' ); ?>" name="user_id" value="<?php echo isset( $_GET['user_id'] ) ? esc_attr( $_GET['user_id'] ) : ''; ?>" ></div>
			<div><label for="username"><?php esc_html_e( 'Username', 'faulh' ); ?></label><input id="username" placeholder="<?php esc_html_e( 'Enter Username', 'faulh' ); ?>" name="username" value="<?php echo isset( $_GET['username'] ) ? esc_attr( $_GET['username'] ) : ''; ?>" ></div>
			<div><label for="country" ><?php esc_html_e( 'Country', 'faulh' ); ?></label><input id="country" placeholder="<?php esc_html_e( 'Enter Country', 'faulh' ); ?>" name="country_name" value="<?php echo isset( $_GET['country_name'] ) ? esc_attr( $_GET['country_name'] ) : ''; ?>" ></div>
			<div><label for="browser" ><?php esc_html_e( 'Browser', 'faulh' ); ?></label><input id="browser" placeholder="<?php esc_html_e( 'Enter Browser', 'faulh' ); ?>" name="browser" value="<?php echo isset( $_GET['browser'] ) ? esc_attr( $_GET['browser'] ) : ''; ?>" ></div>
			<div><label for="operating_system" ><?php esc_html_e( 'Operating System', 'faulh' ); ?></label><input  id="operating_system" placeholder="<?php esc_html_e( 'Enter Operating System', 'faulh' ); ?>" name="operating_system" value="<?php echo isset( $_GET['operating_system'] ) ? esc_attr( $_GET['operating_system'] ) : ''; ?>" ></div>
			<div><label for="ip_address" ><?php esc_html_e( 'IP Address', 'faulh' ); ?></label><input id="ip_address" placeholder="<?php esc_html_e( 'Enter IP Address', 'faulh' ); ?>" name="ip_address" value="<?php echo isset( $_GET['ip_address'] ) ? esc_attr( $_GET['ip_address'] ) : ''; ?>" ></div>
			<?php if ( is_network_admin() ) { ?>
			<div><label for="blog_id" ><?php esc_html_e( 'Blog ID', 'faulh' ); ?></label><input id="blog_id" placeholder="<?php esc_html_e( 'Blog ID', 'faulh' ); ?>" name="blog_id" value="<?php echo isset( $_GET['blog_id'] ) ? esc_attr( $_GET['blog_id'] ) : ''; ?>" ></div>
				<?php
			}
			?>
			<div><label for="timezone" ><?php esc_html_e( 'Timezone', 'faulh' ); ?></label><select id="timezone"  name="timezone">
					<?php $selected_timezone = isset( $_GET['timezone'] ) ? $_GET['timezone'] : ''; ?>
					<option value=""><?php esc_html_e( 'Select Timezone', 'faulh' ); ?></option>
					<option value="unknown" <?php selected( $selected_timezone, 'unknown' ); ?> ><?php esc_html_e( 'Unknown', 'faulh' ); ?></option>
					<?php Template_Helper::dropdown_timezones( $selected_timezone ); ?>
				</select></div>
			<div><label for="old_role" ><?php esc_html_e( 'Old Role', 'faulh' ); ?></label><select id="old_role"  name="old_role">
					<option value=""><?php esc_html_e( 'Select Old Role', 'faulh' ); ?></option>
					<?php
					$selected_old_role = isset( $_GET['old_role'] ) ? $_GET['old_role'] : null;
					wp_dropdown_roles( $selected_old_role );
					?>
					<?php if ( is_network_admin() ) { ?>
					<option value="superadmin" <?php selected( $selected_old_role, 'superadmin' ); ?> ><?php esc_html_e( 'Super Administrator', 'faulh' ); ?></option>  <?php } ?>
				</select></div>
			<div><label for="login_status" ><?php esc_html_e( 'Login Status', 'faulh' ); ?></label><select id="login_status" name="login_status">
					<option value=""><?php esc_html_e( 'Select Login Status', 'faulh' ); ?></option>
					<?php $selected_login_status = isset( $_GET['login_status'] ) ? $_GET['login_status'] : ''; ?>
					<option value="unknown" <?php selected( $selected_login_status, 'unknown' ); ?> ><?php esc_html_e( 'Unknown', 'faulh' ); ?></option>
					<?php Template_Helper::dropdown_login_statuses( $selected_login_status ); ?>
				</select></div>
			<?php if ( is_network_admin() ) { ?>
					<div><label for="is_super_admin" ><?php esc_html_e( 'Super Admin', 'faulh' ); ?></label>
						<select id="is_super_admin" name="is_super_admin" >
							<option value=""><?php esc_html_e( 'Select Super Admin', 'faulh' ); ?></option>

							<?php
							Template_Helper::dropdown_is_super_admin( isset( $_GET['is_super_admin'] ) ? $_GET['is_super_admin'] : null );
							?>
						</select>
					</div>
				<?php } ?>

		</div>

		<fieldset>
			<a class="faulh-cancel-btn" href="<?php echo esc_url( 'admin.php?page=' . $_GET['page'] ); ?>"><?php esc_html_e( 'CANCEL', 'faulh' ); ?></a>
			<input id="submit" type="submit" name="submit" value="<?php esc_html_e( 'FILTER', 'faulh' ); ?>" />
			<a id="download_csv_link" href="javascript::void(0)"><?php esc_html_e( 'DOWNLOAD CSV', 'faulh' ); ?></a> 
			<a id="<?php echo $this->plugin_name; ?>_show_hide_advanced_search" href="#"><?php esc_html_e( 'Show Advanced Filters', 'faulh' ); ?></a>
		</fieldset>

	</form>
</div>
