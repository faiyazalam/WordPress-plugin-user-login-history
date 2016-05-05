<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="wrap"> 
	<img src="<?php echo plugins_url('images/fa.jpg', __FILE__);?>" class="icon32" />



		<table class="form-table" id="email-settings">
			<tbody>
			<tr>
					<td colspan="3" align="center"><p><?php _e('You can add <strong> [fa_userloginhistory] </strong> shortcode where you want to display <strong>User Login History</strong> in pages.','fauserloginhistory');?>
					<?php  _e(' <br/> OR  You can add <strong> &lt;&#63;php do_shortcode("[fa_userloginhistory]"); &#63;&gt;</strong> shortcode in any template.','fauserloginhistory');?>
					<?php  _e(' <br/> OR  You can add <strong> &lt;&#63;php echo do_shortcode("[fa_userloginhistory]"); &#63;&gt;</strong> shortcode in any template.','fauserloginhistory');?></p></td>
				</tr>
			</tbody>
		</table>
	
</div>
