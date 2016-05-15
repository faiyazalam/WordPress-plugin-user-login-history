<?php



	global $wpdb, $current_user;
        $userId = $current_user->ID;
	$table_name = $wpdb->prefix . "fa_user_logins";
	
	
	
        


        $countQuery = "select count(id) from ".$table_name." where user_id = $userId ";
      
        
        $sqlQuery = "SELECT * FROM ".$table_name." where user_id = $userId ";
       
      
        $sqlQuery .= "order by id DESC";
$optionsPagination = array();
$optionsPagination['countQuery'] = $countQuery;
$optionsPagination['sqlQuery'] =  $sqlQuery;
$paginations = ulh_pagination($optionsPagination);
$page_links = $paginations['page_links'];
$userLogins = $paginations['rows'];

?>
<div class="wrap"> 



			<?php   $options = get_option( 'ulh_settings' ); ?>	
            <div><?php _e('<strong>Note</strong>: The date-time is in UTC Timezone.','fauserloginhistory');?></div>
			<table class="wp-list-table widefat fixed display" id="userlist">
				<thead style="cursor: pointer;">
					<tr>
						<th><u><?php _e('Sr. No','fauserloginhistory');?></u></th>
						<th><u><?php _e('Username','fauserloginhistory');?></u></th> 
						<th><u><?php _e('Login at','fauserloginhistory');?></u></th>                                  
						<th><u><?php _e('Logout at','fauserloginhistory');?></u></th>                                  
						<th style="width:95px;text-align:left;"><u><?php  _e('Duration','fauserloginhistory');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php _e('IP','fauserloginhistory');?></u></th>  
                                                <?php
                                              
                                                     if(isset($options['ulh_is_show_browser'])){
                                                         echo "<th><u>Browser</u></th>  ";
                                                     }
                                                     
                                                       if( isset($options['ulh_is_show_operating_system'])){
                                                         echo "<th><u>Operating System</u></th>  ";
                                                     }
                                                       if( isset($options['ulh_is_show_country_name'])){
                                                         echo "<th><u>Country Name</u></th>  ";
                                                     }
                                                       if( isset($options['ulh_is_show_country_code'])){
                                                         echo "<th><u>Country Code</u></th>  ";
                                                     }
                                                     
                                                ?>
						                            
					
						
					</tr>
				</thead>
				<tbody>				     
					<?php
												
					$no = 1;
					if ( ! empty( $userLogins ) ) { ?>
						<?php
						foreach ( $userLogins as $userLogin ) {	 ?>
							<tr>
								<td><?php echo $no; ?></td>
                                                                <td><a href="user-edit.php?user_id=<?php echo $userLogin->user_id ?>"><?php echo get_userdata($userLogin->user_id)->user_nicename;  ?></a></td>
								<td><?php echo $userLogin->time_login; ?></td> 
								<td><?php echo $userLogin->time_logout == '0000-00-00 00:00:00'?'Logged In':$userLogin->time_logout; ?></td> 
                                                                <td><?php echo $userLogin->time_logout != '0000-00-00 00:00:00'?date('H:i:s' ,strtotime($userLogin->time_logout) - strtotime($userLogin->time_login)):'Logged In'; ?></td> 
								<td><?php echo $userLogin->ip_address; ?></td> 
                                                                
                                                                <?php
                              
                                                                  if( isset($options['ulh_is_show_browser'])){
                                                                      echo "<td> $userLogin->browser</td> ";
                                                                  }
                                                                  if( isset($options['ulh_is_show_operating_system'])){
                                                                      echo "<td> $userLogin->operating_system</td> ";
                                                                  }
                                                                  if( isset($options['ulh_is_show_country_name'])){
                                                                      echo "<td> $userLogin->country_name</td> ";
                                                                  }
                                                                  if(isset( $options['ulh_is_show_country_code'])){
                                                                      echo "<td> $userLogin->country_code</td> ";
                                                                  }
                                                                  ?>
								
								
								             
							</tr>
						<?php $no += 1;							
						}
					} else {
						echo __('No Login History Found !!! ','fauserloginhistory');
					} ?>					
				</tbody>
			</table>
       
</div>	

 <?php
        if ( $page_links ) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
        ?>