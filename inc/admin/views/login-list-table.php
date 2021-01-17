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

?>
<div class="wrap">
	<?php \User_Login_History\Inc\Common\Helpers\Template::head( esc_html__( 'Login List', 'faulh' ) ); ?>
	<hr>
	<div><?php require plugin_dir_path( dirname( __FILE__ ) ) . 'views/forms/filter.php'; ?></div>
	<hr>
	<div><?php echo $this->login_list_table->timezone_edit_link(); ?></div>
	<hr>
	<div class="faulh_admin_table">
		<form method="post">
			<input type="hidden" name="<?php echo $this->login_list_table->get_bulk_action_form(); ?>" value="">
			<div class="wrapper1">
				<div class="content1"></div>
			</div>

			<div class="wrapper2">
				<div class="content1" style="width: 1145px;">
					<?php $this->login_list_table->display(); ?>
				</div>
			</div>   

		</form>
	</div>
</div>
