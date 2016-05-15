<?php



	global $wpdb;
	$table_name = $wpdb->prefix . "fa_user_logins";
	$info=$_GET["info"];
	if(!empty($info)){
		if($info=="del")
		{
			$delid = sanitize_title_for_query($_GET["did"]);
			if(!empty($delid)){
                            //echo "delete from ".$table_name." where `user_id`= $delid";exit;
				$wpdb->query( $wpdb->prepare( "delete from ".$table_name." where `id`= %s", $delid) );
				echo "<div style='clear:both;'></div><div class='updated' id='message'><p><strong>".__('User Record Deleted.','fauserloginhistory')."</strong>.</p></div>";
			}
			
		}
	}
	
        


        $countQuery = "select count(id) from ".$table_name." where 1 ";
      
        
        $sqlQuery = "SELECT * FROM ".$table_name." where 1 ";
        if(isset($_GET['user_id']))
        {
            $userId = intval($_GET['user_id']);
            $sqlQuery .= " AND user_id = $userId ";
            $countQuery .= " AND user_id = $userId ";
        }
        
        $sqlQuery .= "order by id DESC";
$optionsPagination = array();
$optionsPagination['countQuery'] = $countQuery;
$optionsPagination['sqlQuery'] =  $sqlQuery;
$paginations = ulh_pagination($optionsPagination);
$page_links = $paginations['page_links'];
$userLogins = $paginations['rows'];

?>
<div class="wrap"> 



			<?php    $options = get_option( 'ulh_settings' ); 
                           if(isset( $options['ulh_is_show_plugin_preferred_timezone']) && "" !=  $options['ulh_is_show_plugin_preferred_timezone'])
                           {
                              $timeZone =  $options['ulh_is_show_plugin_preferred_timezone'];   
                           }
                                                      
                      
                        ?>
    <caption>This table showing the Date-Time in Timezone - <?php echo   $timeZone?$timeZone:ULH_SERVER_DEFAULT_TIMEZONE  ?></caption>
			<table class="wp-list-table widefat fixed display" id="userlist">
                    
				<thead style="cursor: pointer;">
					<tr>
						<th style="width:50px;text-align:left;"><u><?php _e('Sr. No','fauserloginhistory');?></u></th>
						<th style="text-align:left;"><u><?php _e('Username','fauserloginhistory');?></u></th> 
						<th><u><?php _e('Login at','fauserloginhistory');?></u></th>                                  
						<th><u><?php _e('Logout at','fauserloginhistory');?></u></th>                                  
						<th style="width:95px;text-align:left;"><u><?php  _e('Duration','fauserloginhistory');?></u></th>                              
						<th style="width:95px;text-align:left;"><u><?php _e('IP','fauserloginhistory');?></u></th>  
                                                <?php
                                               
                                              
                                                     if(isset($options['ulh_is_show_browser'])){
                                                         echo "<th style='width:95px;text-align:left;'><u>Browser</u></th>  ";
                                                     }
                                                     
                                                       if( isset($options['ulh_is_show_operating_system'])){
                                                         echo "<th style='width:95px;text-align:left;'><u>Operating System</u></th>  ";
                                                     }
                                                       if( isset($options['ulh_is_show_country_name'])){
                                                         echo "<th style='width:95px;text-align:left;'><u>Country Name</u></th>  ";
                                                     }
                                                       if( isset($options['ulh_is_show_country_code'])){
                                                         echo "<th style='width:95px;text-align:left;'><u>Country Code</u></th>  ";
                                                     }
                                                     
                                                ?>
						                            
					
						<th style="width:50px;text-align:center;"><?php _e('Action','fauserloginhistory');?></th>
					</tr>
				</thead>
				<tbody>				     
					<?php
												
					$no = 1;
					if ( ! empty( $userLogins ) ) { ?>
						<?php
						foreach ( $userLogins as $userLogin ) {
                                                 
                                                  
                                                    $loginAt = ulh_convertToUserTime($userLogin->time_login, '', $timeZone);
                                                  
                                                   if($userLogin->time_logout == '0000-00-00 00:00:00')
                                                       
                                                   {
                                                     $logoutAt =   FALSE;
                                                   }
 else {
       $logoutAt = ulh_convertToUserTime($userLogin->time_logout, '', $timeZone); 
 }
                                                   
                                                   
                                                    ?>
							<tr>
								<td style="width:40px;text-align:center;"><?php echo $no; ?></td>
                                                                <td nowrap><a href="user-edit.php?user_id=<?php echo $userLogin->user_id ?>"><?php echo get_userdata($userLogin->user_id)->user_nicename;  ?></a></td>
								<td nowrap><?php echo $loginAt; ?></td> 
								<td nowrap><?php echo $logoutAt == FALSE?'Loggen In':$loginAt ?></td> 
                                                                <td nowrap><?php echo $logoutAt !== FALSE?date('H:i:s' ,strtotime($logoutAt) - strtotime($loginAt)):'Logged In'; ?></td> 
								<td nowrap><?php echo $userLogin->ip_address; ?></td> 
                                                                
                                                                <?php
                              
                                                                  if( isset($options['ulh_is_show_browser'])){
                                                                      echo "<td nowrap> $userLogin->browser</td> ";
                                                                  }
                                                                  if( isset($options['ulh_is_show_operating_system'])){
                                                                      echo "<td nowrap> $userLogin->operating_system</td> ";
                                                                  }
                                                                  if( isset($options['ulh_is_show_country_name'])){
                                                                      echo "<td nowrap> $userLogin->country_name</td> ";
                                                                  }
                                                                  if(isset( $options['ulh_is_show_country_code'])){
                                                                      echo "<td nowrap> $userLogin->country_code</td> ";
                                                                  }
                                                                  ?>
								
								
								<td style="width:40px;text-align:center;">								
							<a onclick="javascript:return confirm('Are you sure, want to delete record of <?php echo $username; ?>?')" href="admin.php?page=ulh_user_lists&info=del&did=<?php echo $userLogin->id;?>">
							<img src="<?php echo plugins_url('images/delete.png',__FILE__); ?>" title="Delete" alt="Delete" style="height:18px;" />
							</a>
								</td>                
							</tr>
						<?php $no += 1;							
						}
					} else {
						echo __('No User Login History Found !!! ','fauserloginhistory');
					} ?>					
				</tbody>
			</table>
       
</div>	

 <?php
        if ( $page_links ) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
        ?>