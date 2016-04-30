<?php

if(!isset($_GET['user_id']))
{
    echo 'Invalid User Id';exit;
}

$userId = $_GET['user_id'];
	global $wpdb;
	$table_name = $wpdb->prefix . "user_logins";
	$info=$_GET["info"];
	if(!empty($info)){
		if($info=="del")
		{
			$delid = sanitize_title_for_query($_GET["did"]);
			if(!empty($delid)){
				$wpdb->query( $wpdb->prepare( "delete from ".$table_name." where `id`= %s", $delid) );
				echo "<div style='clear:both;'></div><div class='updated' id='message'><p><strong>:".__('User Record Deleted.','aicontactform')."</strong>.</p></div>";
			}
			
		}
	}
	
        
        $timeLogout = '0000-00-00 00:00:00';
        $options = array();
$options['countQuery'] = "select count(id) from ".$table_name." where user_id = '$userId' and time_logout <> '$timeLogout' ";
$options['sqlQuery'] =  "SELECT * FROM ".$table_name." where user_id = '$userId' and time_logout <> '$timeLogout' order by id DESC";
$paginations = fa_pagination($options);
$page_links = $paginations['page_links'];
$userLogins = $paginations['rows'];
        ?>
<div class="wrap"> 
	<img src="<?php echo plugins_url();?>/responsive-contact-form/images/augustinfotech.jpg" class="icon32" />
	<h2><?php _e('User Login Hisory Records','aicontactform');?>
		<a class="button add-new-h2 dateshow" href="#"><?php _e('Export User Records','aicontactform');?></a>
	</h2>
	<form method="post" name="exportdate" id="exportdateform" action="<?php echo plugins_url();?>/responsive-contact-form/include/userlist_export.php" >	
         <div id="dateexport" style="display:none;width:100%;margin-bottom:10px;">
             <div class="form-wrap">
             <div style="float:left;">
           		  <label><?php _e('From Date','aicontactform');?></label><input type="text" name="start_date" id="startdate" class="input-txt" value=""/><br/><?php _e('(Format: MM-DD-YYYY)','aicontactform');?>
             </div>
			 <div style="float:left;margin-left:50px;">
	             <label><?php _e('To Date','aicontactform');?></label><input type="text" name="end_date" id="enddate" class="input-txt" value=""/><br/><?php _e('(Format: MM-DD-YYYY)','aicontactform');?>
             </div>
             <div style="float:left;margin-left:50px;margin-top:22px;">
	             <input type="submit" value="<?php _e('Go','aicontactform');?>" class="button add-new-h2 checkdate" id="submit" name="submit"/>
	             <a class="button add-new-h2 checkcancel" href="#"><?php _e('Cancel','aicontactform');?></a>
           	 </div>             
             </div>
         </div>
  	</form>
			<?php settings_fields( 'ai-fields' ); ?>	
			<table class="wp-list-table widefat fixed display" id="userlist">
			<caption style="color:#9CC;"><?php _e('Please click on column\'s title to sort the data according to specific column !!!','aicontactform');?> </caption>
				<thead style="cursor: pointer;">
					<tr>
						<th style="width:50px;text-align:left;"><u><?php _e('Sr. No','aicontactform');?></u></th>
						<th style="text-align:left;"><u><?php _e('Username','aicontactform');?></u></th> 
						<th><u><?php _e('Login at','aicontactform');?></u></th>                                  
						<th><u><?php _e('Logout at','aicontactform');?></u></th>                                  
						<th style="width:95px;text-align:left;"><u><?php  _e('Duration','aicontactform');?></u></th>                              
<!--						<th style="width:95px;text-align:left;"><u><?php //_e('IP','aicontactform');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php// _e('Browser','aicontactform');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Operating System','aicontactform');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Country Name','aicontactform');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Country Code','aicontactform');?></u></th>                              -->
						<th style="width:50px;text-align:center;"><?php _e('Action','aicontactform');?></th>
					</tr>
				</thead>
				<tbody>				     
					<?php
												
					$no = 1;
					if ( ! empty( $userLogins ) ) { ?>
						<?php
						foreach ( $userLogins as $userLogin ) {	 ?>
							<tr>
								<td style="width:40px;text-align:center;"><?php echo $no; ?></td>
                                                                <td nowrap><?php echo get_userdata($userLogin->user_id)->user_nicename;  ?></td>
								<td nowrap><?php echo $userLogin->time_login; ?></td> 
								<td nowrap><?php echo $userLogin->time_logout; ?></td> 
                                                                <td nowrap><?php echo date('H:i:s' ,strtotime($userLogin->time_logout) - strtotime($userLogin->time_login)); ?></td> 
<!--								<td nowrap><?php //echo $userLogin->ip_address; ?></td> 
								<td nowrap><?php //echo $userLogin->browser; ?></td> 
								<td nowrap><?php //echo $userLogin->operating_system; ?></td> 
								<td nowrap><?php //echo $userLogin->country_name; ?></td> 
								<td nowrap><?php //echo $userLogin->country_code; ?></td> -->
								<td style="width:40px;text-align:center;">								
							<a onclick="javascript:return confirm('Are you sure, want to delete record of <?php echo $username; ?>?')" href="admin.php?page=ai_user_lists&info=del&did=<?php echo $userLogin->id;?>">
							<img src="<?php echo plugins_url(); ?>/responsive-contact-form/images/delete.png" title="Delete" alt="Delete" style="height:18px;" />
							</a>
								</td>                
							</tr>
						<?php $no += 1;							
						}
					} else {
						echo __('No User Records Found !!! ','aicontactform');
					} ?>					
				</tbody>
			</table>
</div>	
 <?php
        if ( $page_links ) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
        ?>