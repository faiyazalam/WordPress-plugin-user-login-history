

<div class="wrap"> 
	<img src="<?php echo plugins_url();?>/user-login-history/images/augustinfotech.jpg" class="icon32" />
	<h2><?php _e('FA User Login History Settings','fauserloginhistory'); ?></h2>
	<form method="post" action="options.php" name="FAGolbalSiteOptions">
		<?php settings_fields( 'fa-fields' ); ?>
		<h3 class="title"><?php _e('Settings','fauserloginhistory'); ?></h3>
		<table class="form-table" id="form-settings">
			<tbody>
				<tr>
					<th class="field-name" scope="row"><?php _e('<strong>Field Name</strong>','fauserloginhistory');?></th>
					<th class="field-status"><?php _e('<strong>Select to Show / Hide Fields on User List</strong>','fauserloginhistory');?></th>
				</tr>
				<tr>
					<th class="field-name" scope="row"><?php _e('Show Country:','fauserloginhistory');?></th>
					<td class="field-status"><input type="checkbox" name="fa_is_show_country"  id="fa_is_show_country" <?php if(esc_attr(get_option('fa_is_show_country'))=="1"){echo "checked";} ?>  /></td>
				</tr>
				
				
				
				
				
				
			
				
				<tr>
					<td colspan="3"></td>
				</tr>
			</tbody>
		</table>
		<h3 class="title"><?php _e('Mail Settings','fauserloginhistory'); ?></h3>
		<table class="form-table" id="email-settings">
			<tbody>
				<tr>
					<th scope="row"><label for="fa_email_address_setting">
					<?php _e('Email Address:','fauserloginhistory');?>
					</label></th>
					<td><input type="text" name="fa_email_address_setting" class="regular-text" value="<?php echo esc_attr(get_option('fa_email_address_setting'));?>"></td>
					<td><?php _e('<strong>Note:</strong> You can add multiple email addresses seperated by comma, to send email to multiple users.','fauserloginhistory');?></td>
				</tr>
				<tr>
					<th scope="row"><label for="fa_subject_text">
					<?php _e('Subject Text:','fauserloginhistory');?>
					</label></th>
					<td><input type="text" name="fa_subject_text" class="regular-text" value="<?php echo esc_attr(get_option('fa_subject_text'));?>"></td>
					<td><?php _e('<strong>Note:</strong> Default subject text " August Infotech " will be used.','fauserloginhistory');?></td>
				</tr>
				<tr>
					<th scope="row"><label for="fa_reply_user_message">
					<?php _e('Reply Message for User:','fauserloginhistory');?>
					</label></th>
					<td><?php /* <input type="text" name="fa_reply_user_message" class="regular-text" value="<?php echo esc_attr(get_option('fa_reply_user_message'));?>"> */?>
					<textarea name="fa_reply_user_message" rows="5" cols="49" class="regular-text"><?php echo esc_attr(get_option('fa_reply_user_message'));?></textarea></td>
					<td><?php _e('<strong>Note:</strong> Default Reply Message " Thank you for contacting us...We will get back to you soon... " will be used.','fauserloginhistory');?></td>
				</tr>
				<tr>
					<th scope="row"><label for="fa_custom_css">
					<?php _e('Custom Css:','fauserloginhistory');?>
					</label></th>
					<td><textarea name="fa_custom_css" rows="5" cols="49" class="regular-text"><?php echo esc_attr(get_option('fa_custom_css'));?></textarea></td>
					<td><?php _e('<strong>Note:</strong> this CSS overrite to our contact plugin CSS And Also please use <strong>!important</strong> with your CSS property.','fauserloginhistory');?></td>
				</tr>
				<tr>
					<td colspan="3"></td>
				</tr>
				<tr>
					<td colspan="3"><input class="button-primary" type="submit" value="<?php _e('Save All Changes','fauserloginhistory');?>"></td>
				</tr>
				<tr>
					<td colspan="3" align="center"><p><?php _e('<strong>Note:</strong> You can add <strong> [fa_contact_form] </strong> shortcode where you want to display contact form in pages.','fauserloginhistory');?>
					<?php  _e(' <br/> OR  You can add <strong> &lt;&#63;php do_shortcode("[fa_contact_form]"); &#63;&gt;</strong> shortcode in any template.','fauserloginhistory');?>
					<?php  _e(' <br/> OR  You can add <strong> &lt;&#63;php echo do_shortcode("[fa_contact_form]"); &#63;&gt;</strong> shortcode in any template.','fauserloginhistory');?></p></td>
				</tr>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
