<?php
	global $wpdb, $current_user;
	$table_name = $wpdb->prefix . "fa_user_logins";
	$currentUserId = $current_user->ID;

        $countQuery = "select count(id) from ".$table_name." where user_id = $currentUserId ";

        $sqlQuery = "SELECT * FROM ".$table_name." where user_id = $currentUserId ";
        $sqlQuery .= "order by id DESC";
        
        
$options = array();
$options['countQuery'] = $countQuery;
$options['sqlQuery'] =  $sqlQuery;
$paginations = ulh_pagination($options);
$page_links = $paginations['page_links'];
$userLogins = $paginations['rows'];
?>
<div class="wrap"> 
	<h2><?php _e('My Login Hisory Records','fauserloginhistory');?>
	</h2>
	
			
		<table class="wp-list-table widefat fixed display" id="userlist">
				<thead style="cursor: pointer;">
					<tr>
						<th style="width:50px;text-align:left;"><u><?php _e('Sr. No','fauserloginhistory');?></u></th>
						<th><u><?php _e('Login at','fauserloginhistory');?></u></th>                                  
						<th><u><?php _e('Logout at','fauserloginhistory');?></u></th>                                  
						<th style="width:95px;text-align:left;"><u><?php  _e('Duration','fauserloginhistory');?></u></th>                              
<!--						<th style="width:95px;text-align:left;"><u><?php //_e('IP','fauserloginhistory');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php// _e('Browser','fauserloginhistory');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Operating System','fauserloginhistory');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Country Name','fauserloginhistory');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php //_e('Country Code','fauserloginhistory');?></u></th>                              -->
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
						echo __('No User Records Found !!! ','fauserloginhistory');
					} ?>					
				</tbody>
			</table>
</div>			