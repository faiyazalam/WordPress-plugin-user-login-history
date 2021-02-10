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

use User_Login_History as NS;
?>
<?php
$all_features = array(
	array(
		'title'       => 'AUTO LOGOUT',
		'description' => 'Automatically logout idle users every x minute. You can also specify roles. This feature is built on WordPress Cron Job.',
		'icon'        => 'fa-sign-out',
	),
	array(
		'title'       => 'IP Address Control',
		'description' => "Allows you to control of hiding and saving of user's IP address.",
		'icon'        => 'fa-lock',
	),
	array(
		'title'       => 'EMAIL ALERT',
		'description' => 'Allows you to get notified via email for success/failed login. You can also specify roles and modify email templates.',
		'icon'        => 'fa-inbox',
	),
	array(
		'title'       => 'AUTO DELETE OLD RECORDS',
		'description' => 'Automatically delete the records older than x days. You can also specify the roles. This feature is built on WordPress Cron Job.',
		'icon'        => 'fa-trash',
	),
	array(
		'title'       => 'TRACK SPECIFIC ROLES',
		'description' => 'Allows you to track specific roles only.',
		'icon'        => 'fa-users',
	),
	array(
		'title'       => 'CSV SEPARATOR',
		'description' => 'Allows you to enter a CSV separator for CSV export.',
		'icon'        => 'fa-file',
	),
	array(
		'title'       => 'REPORT - TIMESHEET',
		'description' => 'Generate a timesheet report',
		'icon'        => 'fa-table',
	),
	array(
		'title'       => 'REPORT - NO LOGIN LIST',
		'description' => 'Generate a report of users who have not login for a given date range',
		'icon'        => 'fa-table',
	),
	array(
		'title'       => 'REPORT - LOGIN DEVICE',
		'description' => 'Generate report of login count based on IP address',
		'icon'        => 'fa-table',
	),
);

$chunked_features = array_chunk( $all_features, 3 );
?>
<div class="wrap">
	<h1><?php echo NS\PLUGIN_NAME; ?> (<?php echo esc_html__( 'Pro Version', 'faulh' ); ?>) <?php echo esc_html__( 'Features', 'faulh' ); ?></h1>
	<br>

	<div class="clearfix">
		<div class="container-fluid">
			<?php
			foreach ( $chunked_features as $features ) {
				echo '<div class="row">';
				foreach ( $features as $feature ) {
					?>
					<div class="col-lg-4">
						<div class="row">
							<div class="col-lg-12">
								<div class="box">  
									<div class="img"><div class="icon"><i aria-hidden="true" class="fa <?php echo $feature['icon']; ?>"></i></div></div>
									<div class="heading-1 pull-left"><?php echo strtoupper( $feature['title'] ); ?></div>
									<div class="clearfix"></div>
									<div style="height: 100px" class="text"><?php echo $feature['description']; ?></div>
								</div>  
							</div>
						</div>
					</div>
					<?php
				}
				echo '</div>';
			}
			?>
			<div class="row">
				<div class="text-center">
		<a href="<?php echo NS\PLUGIN_FEATURE_LINK ?>" target="_blank" class="btn btn-primary button-edit"><?php esc_html_e( 'View All Features', 'faulh' ); ?></a>
		<a href="<?php echo NS\PLUGIN_GO_PRO_LINK ?>" target="_blank" class="btn btn-primary button-edit"><?php esc_html_e( 'Buy Now', 'faulh' ); ?></a>
				</div>
			</div>  
		</div>
	</div>
</div>
