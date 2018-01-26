<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

abstract class User_Login_History_Abstract_List_table extends WP_List_Table {
    
    private $table;
    /** Class constructor */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Customer', 'user-login-history' ), //singular name of the listed records
			'plural'   => __( 'Customers', 'user-login-history' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		) );

                $this->table = User_Login_History_DB_Helper::get_table_name();
	}
        
        

    public function no_items() {
		_e( 'No records avaliable.', 'user-login-history' );
	}
        
        /**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public  function get_rows( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM $this->table";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
 else {
     $sql .= " ORDER BY id DESC";
 }

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public  function delete_rows( $ids = array() ) {
            
		global $wpdb;
if(!empty($ids))
{
    if(!is_array($ids))
    {
        $ids = array($ids);
    }
  $ids = implode( ',', array_map( 'absint', $ids ) );
   
 $status =  $wpdb->query( "DELETE FROM $this->table WHERE id IN($ids)" );  
 if($wpdb->last_error)
 {
     User_Login_History_Error_Handler::error_log($wpdb->last_error." ".$wpdb->last_query, __LINE__, __FILE__);
 }
 return $status;

}
return FALSE;
	}
        
        public function delete_all_rows() {
            global $wpdb;
          $status =    $wpdb->query( "TRUNCATE $this->table" );  
               if($wpdb->last_error)
 {
     User_Login_History_Error_Handler::error_log($wpdb->last_error." ".$wpdb->last_query, __LINE__, __FILE__);
 }
 return $status;
        }


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM $this->table";

		return $wpdb->get_var( $sql );
	}




	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
            
			
		switch ( $column_name ) {
			case 'user_id':
                            return $item[$column_name];
			case 'username':
                             return $item[$column_name];
			case 'old_role':
                             return $item[$column_name];
			case 'ip_address':
                             return $item[$column_name];
			case 'browser':
                             return $item[$column_name];
			case 'operating_system':
                             return $item[$column_name];
			case 'country_name':
                             return $item[$column_name];
			case 'country_code':
                             return $item[$column_name];
			case 'timezone':
                             return $item[$column_name];
			case 'user_agent':
                             return $item[$column_name];
			case 'time_login':
                             return $item[$column_name];
			case 'time_last_seen':
                             return $item[$column_name];
			case 'time_logout':
                             return $item[$column_name];
			case 'is_super_admin':
                             return $item[$column_name];
			case 'login_status':
                             return $item[$column_name];
				
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_username( $item ) {

		$delete_nonce = wp_create_nonce( USER_LOGIN_HISTORY_NONCE_PREFIX.'delete_row' );

		$title = '<strong>' . $item['username'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
		];

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'      => '<input type="checkbox" />',
			'user_id'    => __( 'User ID', 'user-login-history' ),
			'username'    => __( 'Username', 'user-login-history' ),
                     // 'role' => __( 'Current Role', 'user-login-history' ),
                        'old_role' => __( 'Old Role', 'user-login-history' ),
			'ip_address'    => __( 'IP Address', 'user-login-history' ),
			'browser'    => __( 'Browser', 'user-login-history' ),
			'operating_system' => __( 'Platform', 'user-login-history' ),
			'country_name'    => __( 'Country Name', 'user-login-history' ),
			'country_code'    => __( 'Country Code', 'user-login-history' ),
			'timezone'    => __( 'Timezone', 'user-login-history' ),
			'user_agent'    => __( 'User Agent', 'user-login-history' ),
			'time_login'    => __( 'Login', 'user-login-history' ),
			'time_last_seen'    => __( 'Last Seen', 'user-login-history' ),
			'time_logout'    => __( 'Logout', 'user-login-history' ),
			'is_super_admin'    => __( 'Super Admin', 'user-login-history' ),
			'login_status'    => __( 'Status', 'user-login-history' ),
			
		);
                

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'user_id' => array( 'user_id', true ),
			'username' => array( 'username', true ),
			'old_role' => array( 'old_role', false ),
			'ip_address' => array( 'ip_address', false ),
			'browser' => array( 'browser', false ),
			'operating_system' => array( 'operating_system', false ),
			'country_name' => array( 'country_name', false ),
			'country_code' => array( 'country_code', false ),
			'timezone' => array( 'timezone', false ),
			'user_agent' => array( 'user_agent', false ),
			'time_login' => array( 'time_login', false ),
			'time_last_seen' => array( 'time_last_seen', false ),
			'time_logout' => array( 'time_logout', false ),
			'is_super_admin' => array( 'is_super_admin', false ),
			'login_status' => array( 'login_status', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete Selected Records',
			'bulk-delete-all-admin' => 'Delete All Records',
		);

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'customers_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = $this->record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		) );

		$this->items = $this->get_rows( $per_page, $current_page );
	}

	public function process_bulk_action() {
            $status = FALSE;
            $action = FALSE;
            
            if(!empty($_POST['action']))
            {
                $action =  $_POST['action'];
            }
            
            $current_action = $this->current_action();
            if ($current_action) {
            $action =  $current_action;
        }
           
            if($action)
            {
                
                switch ($action) {
                    case 'bulk-delete':
                       if(!empty($_POST['bulk-delete']))
                       {
                        $this->delete_rows(esc_sql($_POST['bulk-delete']) );
                        $status = TRUE;   
                       }
			
                        break;
                    case 'bulk-delete-all-admin':
			$this->delete_all_rows();
                        $status = TRUE;
                        break;
                    
                    case 'delete':
			$this->delete_rows( absint( $_GET['customer'] ) );
                        $status = TRUE;
                        break;

                    default:
                        $status = FALSE;
                        break;
                }
                
            }
                return $status;
	}
 




}