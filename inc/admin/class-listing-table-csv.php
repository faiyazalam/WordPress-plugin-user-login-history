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

use User_Login_History\Inc\Common\Interfaces\Admin_Csv as Admin_Csv_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	private $unknown_symbol = '';

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
		$filename = sanitize_file_name( $this->get_suffix() . '.csv' );
		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	}

	/**
	 * Get suffix to be used for csv file name.
	 *
	 * @return string
	 */
	private function get_suffix() {
		return 'login_list_' . wp_date( 'n-j-y_H-i' );
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
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- WP_Filesystem is actually not ideal for CSV export.
		$handle = fopen( 'php://output', 'w' );
		if ( false === $handle ) {
			return;
		}
		$this->listing_table->set_unknown_symbol( $this->unknown_symbol );
		$columns = $this->listing_table->get_columns();

		$i      = 0;
		$record = array();
		$page   = 1;
		$limit  = 100;

		while ( true ) {
			$batch = $this->listing_table->get_rows( $limit, $page );

			if ( empty( $batch ) ) {
				if ( 0 === $i ) {
					fputcsv( $handle, array( __( 'No records found', 'user-login-history' ) ), ',', '"', '\\' );
					exit;
				}
				break;
			}

			foreach ( $batch as $row ) {
				$record = array();
				foreach ( $columns as $field_name => $field_label ) {
					if ( ! key_exists( $field_name, $row ) ) {
						continue;
					}

					$record[ $field_name ] = $this->listing_table->column_default( $row, $field_name );
				}

				if ( 0 == $i ) {
					fputcsv( $handle, $this->escape_csv_record( array_keys( $record ) ), ',', '"', '\\' );
				}

				fputcsv( $handle, $this->escape_csv_record( $record ), ',', '"', '\\' );

				++$i;
			}

			++$page;
			wp_cache_flush_runtime();
		}
		wp_cache_flush_runtime();
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- WP_Filesystem is actually not ideal for CSV export.
		fclose( $handle );
		die();
	}

	/**
	 * Escape CSV formula injection characters.
	 *
	 * @param array $record CSV record.
	 * @return array
	 */
	private function escape_csv_record( array $record ) {
		foreach ( $record as $key => $value ) {
			$record[ $key ] = $this->escape_csv_field( $value );
		}

		return $record;
	}

	/**
	 * Escape a single CSV field if it looks like a formula.
	 *
	 * @param mixed $value CSV field value.
	 * @return mixed
	 */
	private function escape_csv_field( $value ) {
		if ( is_string( $value ) ) {
			$str = $value;
		} elseif ( is_object( $value ) && method_exists( $value, '__toString' ) ) {
			$str = (string) $value;
		} else {
			return $value;
		}

		// Decode HTML entities from DB storage
		$str = html_entity_decode( $str, ENT_QUOTES, 'UTF-8' );

		// Strip HTML tags
		$str = wp_strip_all_tags( $str );

		// Strip NULL bytes
		$str = str_replace( "\0", '', $str );

		if ( '' === $str ) {
			return $str;
		}

		// Trim ALL unicode whitespace variants before checking first char
		$trimmed = preg_replace( '/^[\pZ\pC]+/u', '', $str );

		if ( '' === $trimmed ) {
			return $str;
		}

		$dangerous_chars = array( '=', '-', '+', '@', '|', "\t", "\r", "\n" );

		if ( in_array( $trimmed[0], $dangerous_chars, true ) ) {
			return "'\t" . $str; // \t after quote breaks formula parsing in Excel/Sheets
		}

		return $str;
	}
}
