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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- no need of nonce to prefill the form.
$faulh_the_get = $_GET;
?>
<form class="faulh-filter" name="<?php echo esc_attr( $this->plugin_name . '-search-form' ); ?>" method="get" action="" id="<?php echo esc_attr( $this->plugin_name . '-search-form' ); ?>">
	<fieldset>
		<div class="faulh-field faulh-date-field">
			<label class="faulh-label" for="date_from"><?php esc_html_e( 'From', 'user-login-history' ); ?></label>
			<input class="faulh-date-inputs" readonly type="text" autocomplete="off" placeholder="<?php echo esc_attr( __( 'From', 'user-login-history' ) ); ?>" id="date_from" name="date_from" value="<?php echo isset( $faulh_the_get['date_from'] ) ? esc_attr( sanitize_text_field( $faulh_the_get['date_from'] ) ) : ''; ?>">
		</div>
		<div class="faulh-field faulh-date-field">
			<label class="faulh-label" for="date_to"><?php esc_html_e( 'To', 'user-login-history' ); ?></label>
			<input class="faulh-date-inputs" readonly type="text" autocomplete="off" placeholder="<?php echo esc_attr( __( 'To', 'user-login-history' ) ); ?>" id="date_to" name="date_to" value="<?php echo isset( $faulh_the_get['date_to'] ) ? esc_attr( sanitize_text_field( $faulh_the_get['date_to'] ) ) : ''; ?>">
		</div>
		<div class="faulh-field">
			<label class="faulh-label" for="date_type"><?php esc_html_e( 'Date Type', 'user-login-history' ); ?></label>
			<select id="date_type" name="date_type">
				<?php
				Template_Helper::dropdown_time_field_types( isset( $faulh_the_get['date_type'] ) ? $faulh_the_get['date_type'] : null );
				?>
			</select>
		</div>
		<div class="faulh-filter-actions">
			<?php if ( $reset_url ) { ?>
				<a class="faulh-link" href="<?php echo esc_url( $reset_url ); ?>"><?php esc_html_e( 'RESET', 'user-login-history' ); ?></a>
			<?php } ?>
			<input class="faulh-button" id="submit" type="submit" name="submit" value="<?php esc_html_e( 'FILTER', 'user-login-history' ); ?>" />
		</div>
	</fieldset>
	<?php do_action( 'faulh_public_listing_search_form' ); ?>
</form>
