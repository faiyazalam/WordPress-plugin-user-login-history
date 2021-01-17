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
interface Admin_List_Table {

	/**
	 * Prepares the where query.
	 */
	public function prepare_where_query();

	/**
	 * Get rows.
	 */
	public function get_rows();

	/**
	 * Get record count.
	 */
	public function record_count();

	/**
	 * Do bulk action.
	 */
	public function process_bulk_action();

	/**
	 * Do single action.
	 */
	public function process_single_action();
}
