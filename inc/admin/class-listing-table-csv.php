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

namespace User_Login_History\Inc\Admin;

use User_Login_History\Inc\Common\Helpers\Date_Time as Date_Time_Helper;
use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;

/**
 * CSV Export Functionality
 *
 * @link       http://userloginhistory.com
 *
 * @author    Er Faiyaz Alam
 */
final class Listing_Table_Csv {

	/**
	 * Holds the instance of the class which is implemented with Admin_CSV.
	 *
	 * @var Admin_Csv
	 */
	private $listing_table;

	/**
	 * Holds a symbol to be used to render unknown record.
	 *
	 * @var string
	 */
	private $unknown_symbol = '---';

	/**
	 * Set the listing table.
	 *
	 * @param Admin_Csv_Interface $admin_csv_interface The csv interface.
	 */
	public function set_listing_table( Admin_Csv_Interface $admin_csv_interface ) {
		$this->listing_table = $admin_csv_interface;
	}

	/**
	 * Set content type in header.
	 */
	private function set_headers() {
		header( 'Content-Type: text/csv' );
		header( 'Content-Disposition: attachment;filename=' . $this->get_suffix() . '.csv' );
	}

	/**
	 * Get suffix to be used for csv file name.
	 *
	 * @return string
	 */
	private function get_suffix() {
		return 'login_list_' . date( 'n-j-y_H-i' );
	}

	/**
	 * Initializes csv export
	 */
	public function init() {
		$this->set_headers();
		$this->export();
	}

	/**
	 * Validate http request.
	 * TODO: make this function private - low priority
	 *
	 * @return bool
	 */
	public function is_request_for_csv() {
		return ! empty( $_GET[ $this->listing_table->get_csv_field_name() ] ) && 1 == $_GET[ $this->listing_table->get_csv_field_name() ] && check_admin_referer( $this->listing_table->get_csv_nonce_name() );
	}

	/**
	 * Exports CSV.
	 */
	private function export() {
		$data = $this->listing_table->get_all_rows();

		if ( ! $data ) {
			$this->listing_table->no_items();
			exit;
		}

		$this->listing_table->set_unknown_symbol( $this->unknown_symbol );
		$columns = $this->listing_table->get_columns();

		$fp     = fopen( 'php://output', 'w' );
		$i      = 0;
		$record = array();
		foreach ( $data as $row ) {

			foreach ( $columns as $field_name => $field_label ) {
				if ( ! key_exists( $field_name, $row ) ) {
					continue;
				}

				$record[ $field_name ] = $this->listing_table->column_default( $row, $field_name );
			}

			if ( 0 == $i ) {
				fputcsv( $fp, array_keys( $record ) );
			}

			fputcsv( $fp, $record );
			$i++;
		}

		fclose( $fp );
		die();
	}

}
