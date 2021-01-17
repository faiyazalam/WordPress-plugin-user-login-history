<?php
/**
 * Backend Functionality
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Common\Interfaces;

/**
 * Backend Functionality
 */
interface Admin_Csv {

	/**
	 * Get all rows.
	 */
	public function get_all_rows();

	/**
	 * Get message if not items found.
	 */
	public function no_items();

	/**
	 * Get timezone.
	 */
	public function get_timezone();

	/**
	 * Get csv field name.
	 */
	public function get_csv_field_name();

	/**
	 * Get csv nonce name.
	 */
	public function get_csv_nonce_name();
}
