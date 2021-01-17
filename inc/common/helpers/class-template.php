<?php
/**
 * Backend Functionality.
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Common\Helpers;

use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Login_Tracker;
use User_Login_History as NS;

/**
 * Backend Functionality.
 */
class Template {

	/**
	 * Print out option html elements for all the time field types.
	 *
	 * @param string $selected The selected value.
	 */
	public static function dropdown_time_field_types( $selected = '' ) {
		$r     = '';
		$types = self::time_field_types();
		foreach ( $types as $key => $type ) {
			$name = $type;
			if ( $selected == $key ) {
				$r .= "\n\t<option selected='selected' value='" . $key . "'>$name</option>";
			} else {
				$r .= "\n\t<option value='" . $key . "'>$name</option>";
			}
		}
		echo $r;
	}

	/**
	 * Print out option html elements for all the login statuses.
	 *
	 * @param string $selected The selected value.
	 */
	public static function dropdown_login_statuses( $selected = '' ) {
		$r     = '';
		$types = self::login_statuses();

		foreach ( $types as $key => $type ) {
			$name = $type;
			if ( $selected == $key ) {
				$r .= "\n\t<option selected='selected' value='" . $key . "'>$name</option>";
			} else {
				$r .= "\n\t<option value='" . $key . "'>$name</option>";
			}
		}
		echo $r;
	}

	/**
	 * Print out option html elements for all the timezones.
	 *
	 * @global object $wpdb The object.
	 * @param string $selected The selected value.
	 */
	public static function dropdown_timezones( $selected = '' ) {
		$r         = '';
		$timezones = Date_Time_Helper::get_timezone_list();
		foreach ( $timezones as $timezone ) {
			$key  = $timezone['zone'];
			$name = $timezone['zone'] . '(' . $timezone['diff_from_GMT'] . ')';
			if ( $selected == $key ) {
				$r .= "\n\t<option selected='selected' value='" . $key . "'>$name</option>";
			} else {
				$r .= "\n\t<option value='" . $key . "'>$name</option>";
			}
		}
		echo $r;
	}

	/**
	 * Print the head section.
	 *
	 * @param string $page The page title.
	 */
	public static function head( $page = '' ) {
		$author_urls = self::plugin_author_links();
		$h           = '<h1>' . NS\PLUGIN_NAME . ' ' . NS\PLUGIN_VERSION . ' ' . esc_html__( '(Basic Version)', 'faulh' ) . '</h1>';
		$h          .= '<div>';

			foreach ( $author_urls as $key => $author_url ) {
				if ( $key > 0 ) {
					$h .= ' | ';
				}
				$h .= "<a title='".$author_url['title']."' href='" . $author_url['url'] . "' target='_blank'> " . $author_url['label'] . '</a>';
			}
		

		$h .= '</div>';

		if ( ! empty( $page ) ) {
			$h .= "<h2>$page</h2>";
		}
		echo $h;
	}

	/**
	 * Print out option html elements for super admin.
	 *
	 * @param string $selected The selected value.
	 */
	public static function dropdown_is_super_admin( $selected = '' ) {
		$r     = '';
		$types = self::super_admin_statuses();
		foreach ( $types as $key => $type ) {
			$name = $type;
			if ( $selected == $key ) {
				$r .= "\n\t<option selected='selected' value='" . $key . "'>$name</option>";
			} else {
				$r .= "\n\t<option value='" . $key . "'>$name</option>";
			}
		}
		echo $r;
	}

	/**
	 * Return options for super admin statuses.
	 *
	 * @return array
	 */
	public static function super_admin_statuses() {
		return array(
			'yes' => esc_html__( 'Yes', 'faulh' ),
			'no'  => esc_html__( 'No', 'faulh' ),
		);
	}

	/**
	 * This is used on filter form to filter the record based on given time interval.
	 *
	 * @return array
	 */
	public static function time_field_types() {
		return array(
			'login'     => esc_html__( 'Login', 'faulh' ),
			'logout'    => esc_html__( 'Logout', 'faulh' ),
			'last_seen' => esc_html__( 'Last Seen', 'faulh' ),
		);
	}

	/**
	 * Return options for login statuses.
	 *
	 * @return array
	 */
	public static function login_statuses() {
		$types = array(
			Login_Tracker::LOGIN_STATUS_LOGIN  => esc_html__( 'Logged In', 'faulh' ),
			Login_Tracker::LOGIN_STATUS_LOGOUT => esc_html__( 'Logged Out', 'faulh' ),
			Login_Tracker::LOGIN_STATUS_FAIL   => esc_html__( 'Failed', 'faulh' ),
		);

		if ( is_multisite() ) {
			$types[ Login_Tracker::LOGIN_STATUS_BLOCK ] = esc_html__( 'Blocked', 'faulh' );
		}
		return $types;
	}

	/**
	 * Return options for author links.
	 *
	 * @return array
	 */
	public static function plugin_author_links() {
		return array(
			array(
				'key'   => 'userloginhistory',
				'url'   => 'http://userloginhistory.com/',
				'label' => esc_html__('Official Website', 'faulh'),
				'title' => esc_html__('Visit Plugin Official Website', 'faulh')
			),
			array(
				'key'   => 'paypal',
				'url'   => 'https://www.paypal.me/erfaiyazalam/',
				'label' => esc_html__( 'Donate', 'faulh' ),
				'title' => esc_html__( 'Donate and Support Us', 'faulh' ),
			),
			array(
				'key'   => 'pro',
				'url'   => 'https://www.userloginhistory.com/pricing/',
				'label' => esc_html__( 'BUY PRO VERSION', 'faulh' ),
				'title' => esc_html__( 'Buy pro version', 'faulh' )
			),
		);
	}

	/**
	 * Print html link in button style.
	 *
	 * @param type $url The url.
	 * @param type $label The label.
	 */
	public static function create_button( $url = '', $label = '' ) {
		echo "<a href='$url' class='button-secondary' target='_blank'>$label</a>";
	}

}
