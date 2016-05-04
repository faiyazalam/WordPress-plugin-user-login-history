<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );
global $wpdb;
$table_name = $wpdb->prefix . "ai_contact";
$act=$_REQUEST["act"];	
	$startdate=$_POST['start_date'];
	$enddate=$_POST['end_date'];	
	$stdateold=explode("/",$startdate);
	$stdatenew=$stdateold[2]."-".$stdateold[0]."-".$stdateold[1];
	$endateold=explode("/",$enddate);
	$endatenew=$endateold[2]."-".$endateold[0]."-".$endateold[1];
	$export_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE contact_date >= %s and contact_date <= %s order by user_id ASC", $stdatenew, $endatenew ) );
	$rs =' 
		<table border="1" cellspacing="1" cellpadding="0">
			<tr>
				<th align="center" valign="middle" colspan="4">'.__('List Of Users Contacted Site','fauserloginhistory').'</th>				
			</tr>
			<tr>
				<th align="left" valign="middle">'.__('User ID','fauserloginhistory').'</th>
				<th align="left" valign="middle">'.__('Username','fauserloginhistory').'</th>
				<th align="left" valign="middle">'.__('Email Address','fauserloginhistory').'</th>
				<th align="left" valign="middle">'.__('Message','fauserloginhistory').'</th>
				<th align="left" valign="middle">'.__('Contact Date','fauserloginhistory').'</th>
			</tr>';
			foreach($export_result as $row)
			{ 								  
				$rs.= '<tr>
					<td align="left" valign="middle">'.$row->user_id.'</td>
					<td align="left" valign="middle">'.$row->username.'</td>
					<td align="left" valign="middle">'.$row->email_id.'</td>
					<td align="left" valign="middle">'.$row->message.'</td>
					<td align="left" valign="middle">'.$row->contact_date.'</td>
				</tr>';
			}
		$rs.= '</table>';
	$filename = "User-list" . date('Y-m-d g-i');
	header("Content-type: application/vnd.ms-excel");
	header("Content-disposition: xls" . date("Y-m-d") . ".xls");
	header( "Content-disposition: filename=".$filename.".xls");
	echo $rs;
	exit;	
?>