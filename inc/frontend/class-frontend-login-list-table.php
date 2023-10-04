<?php
/**
 * Frontend Functionality
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Frontend;

use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\Date_Time as DateTimeHelper;
use User_Login_History\Inc\Common\Helpers\Db as DbHelper;
use User_Login_History\Inc\Common\Helpers;
use User_Login_History\Inc\Common\Helpers\Error_Log as ErrorLogHelper;

/**
 * The frontend shortcode-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 *
 * @author    Er Faiyaz Alam
 */
class Frontend_Login_List_Table {

	/**
	 * Default number of rows to be fetched
	 */
	const DEFALUT_LIMIT = 20;

	/**
	 * Default page number
	 */
	const DEFALUT_PAGE_NUMBER = 1;

	/**
	 * Default query name for page number
	 */
	const DEFALUT_QUERY_ARG_PAGE_NUMBER = 'pagenum';

	/**
	 * Default timezone to be used for listing table.
	 */
	const DEFAULT_TABLE_TIMEZONE = 'UTC';

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $plugin_version
	 */
	private $version;

	/**
	 * Number of records to be fetched from table.
	 *
	 * @var      string|int $limit
	 */
	private $limit;

	/**
	 * Page number
	 *
	 * @var      string|int $page_number
	 */
	private $page_number;

	/**
	 * Holds the link of pagination.
	 *
	 * @var      string $pagination_links
	 */
	private $pagination_links;

	/**
	 * Holds the records.
	 *
	 * @var      mixed $items
	 */
	private $items;

	/**
	 * Holds the main table name.
	 *
	 * @var      string $table
	 */
	private $table;

	/**
	 * Holds the list of printable column names.
	 *
	 * @var      array $allowed_columns
	 */
	private $allowed_columns;

	/**
	 * Holds the timezone to be used in table.
	 *
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $table_timezone;

	/**
	 * Holds the date format to be used in table.
	 *
	 * @var      string    $table_date_format
	 */
	private $table_date_format;

	/**
	 * Holds the time format to be used in table.
	 *
	 * @var      string    $table_time_format
	 */
	private $table_time_format;

	/**
	 * Holds the capabilities string to be used in where clause.
	 *
	 * @var type
	 */
	private $capability_string;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param       string $plugin_name        The name of this plugin.
	 * @param       string $version            The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->page_number = ! empty( $_REQUEST[ self::DEFALUT_QUERY_ARG_PAGE_NUMBER ] ) ? absint( $_REQUEST[ self::DEFALUT_QUERY_ARG_PAGE_NUMBER ] ) : self::DEFALUT_PAGE_NUMBER;
		$this->set_table_name();
	}

	/**
	 * Get date-time format.
	 *
	 * @return string
	 */
	public function get_table_date_time_format() {
		return $this->get_table_date_format() . ' ' . $this->get_table_time_format();
	}

	/**
	 * Set date format.
	 *
	 * @param string $format The date format.
	 */
	public function set_table_date_format( $format = '' ) {
		$this->table_date_format = $format;
	}

	/**
	 * Get date format.
	 *
	 * @return string
	 */
	public function get_table_date_format() {
		return ! empty( $this->table_date_format ) ? $this->table_date_format : '';
	}

	/**
	 * Set time format.
	 *
	 * @param type $format The time format.
	 */
	public function set_table_time_format( $format = '' ) {
		$this->table_time_format = $format;
	}

	/**
	 * Get time format.
	 *
	 * @return string
	 */
	public function get_table_time_format() {
		return ! empty( $this->table_time_format ) ? $this->table_time_format : '';
	}

	/**
	 * Set table name.
	 */
	private function set_table_name() {
		global $wpdb;
		$this->table = $wpdb->prefix . NS\PLUGIN_TABLE_FA_USER_LOGINS;
	}

	/**
	 * Set limit
	 *
	 * @param int $limit The limit.
	 */
	public function set_limit( $limit ) {
		$this->limit = $limit ? absint( $limit ) : self::DEFALUT_LIMIT;
	}

	/**
	 * Prepare the items.
	 */
	public function prepare_items() {
		$this->items = $this->get_rows();

		$this->pagination_links = paginate_links(
			array(
				'base'      => add_query_arg( self::DEFALUT_QUERY_ARG_PAGE_NUMBER, '%#%' ),
				'format'    => '',
				'prev_text' => esc_html__( '&laquo;', 'faulh' ),
				'next_text' => esc_html__( '&raquo;', 'faulh' ),
				'total'     => ceil( $this->record_count() / $this->limit ),
				'current'   => $this->page_number,
			)
		);
	}

	/**
	 * Prepare the where query.
	 *
	 * @return string
	 */
	public function prepare_where_query() {
		$where_query = '';

		$fields = array(
			'user_id',
		);

		foreach ( $fields as $field ) {
			if ( ! empty( $_GET[ $field ] ) ) {
				$where_query .= " AND `FaUserLogin`.`$field` = '" . esc_sql( trim( $_GET[ $field ] ) ) . "'";
			}
		}

		if ( ! empty( $_GET['date_type'] ) ) {
			$input_timezone = $this->get_table_timezone();
			$date_type      = $_GET['date_type'];
			if ( in_array( $date_type, array( 'login', 'logout', 'last_seen' ) ) ) {

				if ( ! empty( $_GET['date_from'] ) && ! empty( $_GET['date_to'] ) ) {
					$date_type    = esc_sql( $date_type );
					$date_from    = DateTimeHelper::convert_timezone( $_GET['date_from'] . ' 00:00:00', $input_timezone );
					$date_to      = DateTimeHelper::convert_timezone( $_GET['date_to'] . ' 23:59:59', $input_timezone );
					$where_query .= " AND `FaUserLogin`.`time_$date_type` >= '" . esc_sql( $date_from ) . "'";
					$where_query .= " AND `FaUserLogin`.`time_$date_type` <= '" . esc_sql( $date_to ) . "'";
				} else {
					unset( $_GET['date_from'] );
					unset( $_GET['date_to'] );
				}
			}
		}

		$where_query = apply_filters( 'faulh_public_prepare_where_query', $where_query );
		return $where_query;
	}

	/**
	 * Retrieve rows
	 *
	 * @global \User_Login_History\Inc\Frontend\type $wpdb
	 * @return type
	 */
	public function get_rows() {
		global $wpdb;
		$sql = ' SELECT'
				. ' FaUserLogin.*, '
				. ' TIMESTAMPDIFF(SECOND,FaUserLogin.time_login,FaUserLogin.time_last_seen) as duration'
				. ' FROM ' . $this->table . '  AS FaUserLogin'
				. ' WHERE 1 ';

		$where_query = $this->prepare_where_query();

		if ( $where_query ) {
			$sql .= $where_query;
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$direction            = ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : ' ASC';
			$sanitize_sql_orderby = sanitize_sql_orderby( $_REQUEST['orderby'] . ' ' . $direction );
			if ( $sanitize_sql_orderby ) {
				$sql .= ' ORDER BY ' . $sanitize_sql_orderby;
			}
		} else {
			$sql .= ' ORDER BY FaUserLogin.time_login DESC';
		}

		if ( $this->limit > 0 ) {
			$sql .= " LIMIT $this->limit";
			$sql .= ' OFFSET   ' . ( $this->page_number - 1 ) * $this->limit;
		}
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		if ( '' != $wpdb->last_error ) {

			ErrorLogHelper::error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query, __LINE__, __FILE__ );
		}

		return $result;
	}

	/**
	 * Count the records.
	 *
	 * @global \User_Login_History\Inc\Frontend\type $wpdb
	 * @return type
	 */
	public function record_count() {
		global $wpdb;
		$sql = ' SELECT'
				. ' COUNT(FaUserLogin.id) as total '
				. ' FROM ' . $this->table . '  AS FaUserLogin'
				. ' WHERE 1 ';

		$where_query = $this->prepare_where_query();
		if ( $where_query ) {
			$sql .= $where_query;
		}
		$result = $wpdb->get_var( $sql );
		if ( '' != $wpdb->last_error ) {
			ErrorLogHelper::error_log( 'last error:' . $wpdb->last_error . ' last query:' . $wpdb->last_query, __LINE__, __FILE__ );
		}
		return $result;
	}

	/**
	 * Associative array of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'user_id'          => esc_html__( 'User ID', 'faulh' ),
			'username'         => esc_html__( 'Username', 'faulh' ),
			'role'             => esc_html__( 'Role', 'faulh' ),
			'old_role'         => esc_html__( 'Old Role', 'faulh' ),
			'browser'          => esc_html__( 'Browser', 'faulh' ),
			'operating_system' => esc_html__( 'Operating System', 'faulh' ),
			'ip_address'       => esc_html__( 'IP Address', 'faulh' ),
			'timezone'         => esc_html__( 'Timezone', 'faulh' ),
			'country_name'     => esc_html__( 'Country', 'faulh' ),
			'user_agent'       => esc_html__( 'User Agent', 'faulh' ),
			'duration'         => esc_html__( 'Duration', 'faulh' ),
			'time_last_seen'   => esc_html__( 'Last Seen', 'faulh' ),
			'time_login'       => esc_html__( 'Login', 'faulh' ),
			'time_logout'      => esc_html__( 'Logout', 'faulh' ),
			'login_status'     => esc_html__( 'Login Status', 'faulh' ),
		);
  
		return apply_filters( 'faulh_public_get_columns', $columns );
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'user_id'          => array( 'user_id', true ),
			'username'         => array( 'username', true ),
			'old_role'         => array( 'old_role', true ),
			'time_login'       => array( 'time_login', false ),
			'time_logout'      => array( 'time_logout', false ),
			'browser'          => array( 'browser', true ),
			'operating_system' => array( 'operating_system', false ),
			'country_name'     => array( 'country_name', false ),
			'time_last_seen'   => array( 'time_last_seen', false ),
			'timezone'         => array( 'timezone', false ),
			'user_agent'       => array( 'user_agent', false ),
			'login_status'     => array( 'login_status', false ),
			'duration'         => array( 'duration', false ),
		);

		return apply_filters( 'faulh_public_get_sortable_columns', $sortable_columns );
	}

	/**
	 * Display the pagination link.
	 */
	private function display_pagination() {
		echo $this->pagination_links;
	}

	/**
	 * Get the list of printable columns.
	 *
	 * @return bool|array
	 */
	public function get_allowed_columns() {
		return empty( $this->allowed_columns ) ? false : $this->allowed_columns;
	}

	/**
	 * Set the columns to print on table.
	 *
	 * @param array $columns The columns.
	 */
	public function set_allowed_columns( $columns = array() ) {
		$columns     = is_array( $columns ) ? $columns : ( is_string( $columns ) ? explode( ',', $columns ) : array() );
		$all_columns = $this->get_columns();

		foreach ( $columns as $column ) {
			$column = trim( $column );
			if ( isset( $all_columns[ $column ] ) ) {
				$this->allowed_columns[] = $column;
			}
		}
	}

	/**
	 * Print the column headers.
	 *
	 * @access public
	 */
	public function print_column_headers() {
		$allowed_columns  = $this->get_allowed_columns();
		$columns          = $this->get_columns();
		$sortable_columns = $this->get_sortable_columns();

		$requested_order    = ! empty( $_GET['order'] ) ? $_GET['order'] : '';
		$page_number_string = ! empty( $_GET[ self::DEFALUT_QUERY_ARG_PAGE_NUMBER ] ) ? '&' . self::DEFALUT_QUERY_ARG_PAGE_NUMBER . '=' . $this->page_number : '';
		// print only allowed column headers.
		foreach ( $allowed_columns as $allowed_column ) {
			$direction = $hover = '';
			// add sorting link to sortable column only.
			if ( isset( $sortable_columns[ $allowed_column ] ) ) {
				// defaul order by field.
				$orderby = isset( $sortable_columns[ $allowed_column ][0] ) ? $sortable_columns[ $allowed_column ][0] : $allowed_column;
				// defaul order direction.

				if ( isset( $_GET['orderby'] ) && isset( $sortable_columns[ $allowed_column ][0] ) && $sortable_columns[ $allowed_column ][0] == $_GET['orderby'] ) {
					if ( $requested_order ) {
						// direction based on URL.
						$direction = 'asc' == $requested_order ? '&uarr;' : '&darr;';
						// reverse the order.
						$order = 'asc' == $requested_order ? 'desc' : 'asc';
					}
				} else {
					$order = isset( $sortable_columns[ $allowed_column ][1] ) && $sortable_columns[ $allowed_column ][1] ? 'desc' : 'asc';
				}
				$hover         = 'asc' == $order ? '&uarr;' : '&darr;';
				$column_header = "<a class='sorting_link' href='?orderby=$orderby&order={$order}{$page_number_string}'>$columns[$allowed_column]<span class='sorting_hover'>$hover</span><span class='sorting'>$direction</span></a>";
			} else {
				$column_header = $columns[ $allowed_column ];
			}
			echo "<th>$column_header</th>";
		}
	}

	/**
	 * Display the listing table.
	 *
	 * @access public
	 */
	public function display() {
		$allowed_columns = $this->get_allowed_columns();
		if ( empty( $allowed_columns ) ) {
			esc_html_e( 'No columns is selected to display.', 'faulh' );
			return;
		}
		?>
		<table>
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>
			<tbody>
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		</table>
		<?php
		$this->display_pagination();
	}

	/**
	 * Display rows or placeholder
	 *
	 * @access public
	 */
	public function display_rows_or_placeholder() {
		if ( $this->has_items() ) {
			$this->display_rows();
		} else {
			echo '<tr><td>';
			$this->no_items();
			echo '</td></tr>';
		}
	}

	/**
	 * Generate the table rows
	 *
	 * @access public
	 */
	public function display_rows() {
		foreach ( $this->items as $item ) {
			$this->single_row( $item );
		}
	}

	/**
	 * Checks if record found.
	 *
	 * @return bool
	 */
	public function has_items() {
		return ! empty( $this->items );
	}

	/**
	 * Message to be displayed when there are no items
	 */
	public function no_items() {
		esc_html_e( 'No items found.', 'faulh' );
	}

	/**
	 * Prints single row.
	 *
	 * @param array $item The record.
	 */
	public function single_row( $item ) {
		echo '<tr>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Prints the column value.
	 *
	 * @param array $item The record.
	 */
	public function single_row_columns( $item ) {
		$allowed_columns = $this->get_allowed_columns();
		foreach ( $allowed_columns as $column_name ) {
			echo '<td>' . $this->column_default( $item, $column_name ) . '</td>';
		}
	}

	/**
	 * Set the timezone to be used for table.
	 *
	 * @param string $timezone The timezone.
	 */
	public function set_table_timezone( $timezone = '' ) {
		$this->table_timezone = $timezone;
	}

	/**
	 * Get the timezone to be used for table.
	 *
	 * @return string The timezone.
	 */
	public function get_table_timezone() {
		return $this->table_timezone ? $this->table_timezone : self::DEFAULT_TABLE_TIMEZONE;
	}

	/**
	 * Default columns.
	 *
	 * @param type $item The record.
	 * @param type $column_name The column name.
	 * @return string The value.
	 */
	public function column_default( $item, $column_name ) {
		$timezone         = $this->get_table_timezone();
		$date_time_format = $this->get_table_date_time_format();
		$unknown          = 'unknown';
		$unknown_symbol   = '----';
		$new_column_data  = apply_filters( 'manage_faulh_public_custom_column', '', $item, $column_name );
		$country_code     = in_array( strtolower( $item['country_code'] ), array( '', $unknown ) ) ? $unknown : $item['country_code'];

		switch ( $column_name ) {

			case 'user_id':
				if ( empty( $item[ $column_name ] ) ) {
					return $unknown_symbol;
				}
				return (int) $item[ $column_name ];

			case 'username':
				if ( empty( $item['user_id'] ) ) {
					return esc_html( $item[ $column_name ] );
				}
				$profile_link = get_edit_user_link( $item['user_id'] );

				return $profile_link ? "<a href= '$profile_link'>" . esc_html( $item[ $column_name ] ) . '</a>' : esc_html( $item[ $column_name ] );

			case 'role':
				if ( empty( $item['user_id'] ) ) {
					return $unknown_symbol;
				}
				$user_data = get_userdata( $item['user_id'] );
				return ! empty( $user_data->roles ) ? implode( ',', $user_data->roles ) : $unknown_symbol;

			case 'old_role':
				return ! empty( $item[ $column_name ] ) ? esc_html( $item[ $column_name ] ) : $unknown_symbol;

			case 'browser':
				if ( in_array( strtolower( $item[ $column_name ] ), array( '', $unknown ) ) ) {
					return $unknown;
				}

				if ( empty( $item['browser_version'] ) ) {
					return esc_html( $item[ $column_name ] );
				}

				return esc_html( $item[ $column_name ] . ' (' . $item['browser_version'] . ')' );

			case 'time_login':
				if ( ! ( strtotime( $item[ $column_name ] ) > 0 ) ) {
					return $unknown_symbol;
				}
				$time_login = DateTimeHelper::convert_format( DateTimeHelper::convert_timezone( $item[ $column_name ], '', $timezone ), $date_time_format );
				return $time_login ? $time_login : $unknown_symbol;

			case 'time_logout':
				if ( empty( $item['user_id'] ) || ! ( strtotime( $item[ $column_name ] ) > 0 ) ) {
					return $unknown_symbol;
				}
				$time_logout = DateTimeHelper::convert_format( DateTimeHelper::convert_timezone( $item[ $column_name ], '', $timezone ), $date_time_format );
				return $time_logout ? $time_logout : $unknown_symbol;

			case 'ip_address':
				return $item[ $column_name ] ? esc_html( $item[ $column_name ] ) : $unknown;

			case 'timezone':
				return in_array( strtolower( $item[ $column_name ] ), array( '', $unknown ) ) ? $unknown : esc_html( $item[ $column_name ] );

			case 'operating_system':
				return in_array( strtolower( $item[ $column_name ] ), array( '', $unknown ) ) ? $unknown : esc_html( $item[ $column_name ] );

			case 'country_name':
				return in_array( strtolower( $item[ $column_name ] ), array( '', $unknown ) ) ? $unknown : esc_html( $item[ $column_name ] . '(' . $country_code . ')' );

			case 'country_code':
				return esc_html( $country_code );

			case 'time_last_seen':
				$time_last_seen_unix = strtotime( $item[ $column_name ] );
				if ( empty( $item['user_id'] ) || ! ( $time_last_seen_unix > 0 ) ) {
					return $unknown_symbol;
				}

				$time_last_seen = DateTimeHelper::convert_format( DateTimeHelper::convert_timezone( $item[ $column_name ], '', $timezone ), $date_time_format );
				if ( ! $time_last_seen ) {
					return $unknown_symbol;
				}
				$human_time_diff = human_time_diff( $time_last_seen_unix );
				return "<div title = '$time_last_seen'>" . $human_time_diff . ' ' . esc_html__( 'ago', 'faulh' ) . '</div>';

			case 'duration':
				return human_time_diff( strtotime( $item['time_login'] ), strtotime( $item['time_last_seen'] ) );

			case 'login_status':
				$login_statuses = Helpers\Template::login_statuses();
				return ! empty( $login_statuses[ $item[ $column_name ] ] ) ? $login_statuses[ $item[ $column_name ] ] : $unknown;

			case 'user_agent':
				return ! empty( $item[ $column_name ] ) ? esc_html( $item[ $column_name ] ) : $unknown_symbol;

			default:
				if ( $new_column_data ) {
					return $new_column_data;
				}
				return print_r( $item, true );
		}
	}

}
