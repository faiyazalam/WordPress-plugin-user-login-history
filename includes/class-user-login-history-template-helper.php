<?php
/**
 * User_Login_History_Template_Helper
 */
class User_Login_History_Template_Helper {

 static public function dropdown_blogs( $selected = '' ) {
	global $wpdb;
	$r = '';
         $site_id = get_current_network_id();
 $blogs = $wpdb->get_results("SELECT blog_id, domain, path FROM $wpdb->blogs where site_id = $site_id", 'ARRAY_A');
	foreach ( $blogs as  $blog ) {
		$name = $blog['domain'].$blog['path'];
		if ( $selected == $blog['blog_id'] ) {
			$r .= "\n\t<option selected='selected' value='" . esc_attr( $blog['blog_id'] ) . "'>$name</option>";
		} else {
			$r .= "\n\t<option value='" . esc_attr( $blog['blog_id'] ) . "'>$name</option>";
		}
	}
	echo $r;
}
 static public function dropdown_sites( $selected = '' ) {
	global $wpdb;
	$r = '';
 $sites = $wpdb->get_results("SELECT id, domain, path FROM $wpdb->site", 'ARRAY_A');
	foreach ( $sites as  $site ) {
		$name = $site['domain'].$site['path'];
		if ( $selected == $site['id'] ) {
			$r .= "\n\t<option selected='selected' value='" . esc_attr( $site['id'] ) . "'>$name</option>";
		} else {
			$r .= "\n\t<option value='" . esc_attr( $site['id'] ) . "'>$name</option>";
		}
	}
	echo $r;
}


static public function dropdown_time_field_types( $selected = '' ) {
	$r = '';
  $types = array(
      'login' => __("Login", "user-login-history"), 
      'logout' => __("Logout", "user-login-history"),
      'last_seen' => __("Last Seen", "user-login-history"),
      );
	foreach ( $types as  $key => $type ) {
		$name = $type;
		if ( $selected == $key ) {
			$r .= "\n\t<option selected='selected' value='".$key."'>$name</option>";
		} else {
			$r .= "\n\t<option value='".$key."'>$name</option>";
		}
	}
	echo $r;
}
static public function dropdown_timezone( $selected = '' ) {
	$r = '';
 $timezones = User_Login_History_Date_Time_Helper::get_timezone_list();
	foreach ( $timezones as $timezone) {
		$key = $timezone['zone'];
		$name = $timezone['zone'] . "(" . $timezone['diff_from_GMT'] . ")";
		if ( $selected == $key ) {
			$r .= "\n\t<option selected='selected' value='".$key."'>$name</option>";
		} else {
			$r .= "\n\t<option value='".$key."'>$name</option>";
		}
	}
	echo $r;
}
}