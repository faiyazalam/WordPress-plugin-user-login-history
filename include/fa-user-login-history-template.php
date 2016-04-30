<?php
	global $wpdb, $current_user;
	$table_name = $wpdb->prefix . "user_logins";
	
	$currentUserId = $current_user->ID;
 $timeLogout = '0000-00-00 00:00:00';
        $userLogins = $wpdb->get_results( "select * from ".$table_name." where time_logout <> '$timeLogout' order by id DESC" );
        
?>
<div class="wrap"> 
	<h2><?php _e('My Login Hisory Records','aicontactform');?>
	</h2>
	
			
		<table class="wp-list-table widefat fixed display" id="userlist">
				<thead style="cursor: pointer;">
					<tr>
						<th style="width:50px;text-align:left;"><u><?php _e('Sr. No','aicontactform');?></u></th>
						<th><u><?php _e('Login at','aicontactform');?></u></th>                                  
						<th><u><?php _e('Logout at','aicontactform');?></u></th>                                  
						<th style="width:95px;text-align:left;"><u><?php  _e('Duration','aicontactform');?></u></th>                              
<!--						<th style="width:95px;text-align:left;"><u><?php //_e('IP','aicontactform');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php// _e('Browser','aicontactform');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Operating System','aicontactform');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Country Name','aicontactform');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Country Code','aicontactform');?></u></th>                              -->
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
								<td nowrap><?php echo $userLogin->time_login; ?></td> 
								<td nowrap><?php echo $userLogin->time_logout; ?></td> 
                                                                <td nowrap><?php echo date('H:i:s' ,strtotime($userLogin->time_logout) - strtotime($userLogin->time_login)); ?></td> 
<!--								<td nowrap><?php //echo $userLogin->ip_address; ?></td> 
								<td nowrap><?php //echo $userLogin->browser; ?></td> 
								<td nowrap><?php //echo $userLogin->operating_system; ?></td> 
								<td nowrap><?php //echo $userLogin->country_name; ?></td> 
								<td nowrap><?php //echo $userLogin->country_code; ?></td> -->
						           
							</tr>
						<?php $no += 1;							
						}
					} else {
						echo __('No User Records Found !!! ','aicontactform');
					} ?>					
				</tbody>
			</table>
</div>			