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
?>
<div class="<?php echo $this->plugin_name . '-wrapper'; ?>">
	<?php do_action( 'faulh_public_before_search_form' ); ?>
	<?php echo ! empty( $attributes['title'] ) ? "<div class='" . $this->plugin_name . "-listing_title'>" . $attributes['title'] . '</div>' : ''; ?>
	<div><?php require plugin_dir_path( dirname( __FILE__ ) ) . 'views/forms/filter.php'; ?></div>
	<?php do_action( 'faulh_public_after_search_form' ); ?>
	<hr>
	<div>
		<?php
		if ( ! empty( $attributes['show_timezone_selector'] ) && 'true' == $attributes['show_timezone_selector'] ) {
			?>
		<div><?php require plugin_dir_path( dirname( __FILE__ ) ) . 'views/forms/timezone.php'; ?></div>
			<?php
		}
		?>
	</div>
	<?php do_action( 'faulh_public_before_listing_table' ); ?>
	<?php
	$this->get_list_table()->display();
	?>
	<?php do_action( 'faulh_public_after_listing_table' ); ?>
</div>
