<?php

class User_Login_History_Public_List_Table {
    
     
    const  DEFALUT_LIMIT = 10;
    const  DEFALUT_PAGE_NUMBER = 10;
    const  QUERY_ARG_PAGE_NUMBER = 'pagenum';
    const  DEFALUT_QUERY_ARG_LIMIT = 'limit';


    private $limit;
    private $page_number;
    private $pagination_links;
    private $items;
    
    public function __construct() {
        $this->limit = !empty([$_GET['limit']])  ? intval($_GET['limit']) : self::DEFALUT_LIMIT;
        $this->page_number = !empty([$_GET[QUERY_ARG_PAGE_NUMBER]])  ? intval($_GET[QUERY_ARG_PAGE_NUMBER]) : self::DEFALUT_PAGE_NUMBER;
    }




    public function prepare_items() {
        $this->items = $this->get_rows();

        $this->pagination_links = paginate_links(array(
            'base' => add_query_arg(self::QUERY_ARG_PAGE_NUMBER, '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;', 'user-login-history'),
            'next_text' => __('&raquo;', 'user-login-history'),
            'total' => ceil($this->record_count() / $this->limit), //total pages
            'current' => $this->page_number
        ));
    }
    

    
      public function prepare_where_query() {
        $where_query = '';

        $fields = array(
            'user_id',
            'username',
            'country_name',
            'browser',
            'operating_system',
            'ip_address',
            'timezone',
            'country_name',
            'browser',
            'operating_system',
            'login_status',
        );

        foreach ($fields as $field) {
            if (isset($_GET[$field]) && "" != $_GET[$field]) {
                $where_query .= " AND `FaUserLogin`.`$field` = '" . esc_sql($_GET[$field]) . "'";
            }
        }

        if (isset($_GET['role']) && "" != $_GET['role']) {

            if ('superadmin' == $_GET['role']) {
                $site_admins = get_super_admins();
                $site_admins_str = implode("', '", $site_admins);
                $where_query .= " AND `FaUserLogin`.`username` IN ('$site_admins_str')";
            } else {
                $where_query .= " AND `UserMeta`.`meta_value` LIKE '%" . esc_sql($_GET['role']) . "%'";
            }
        }

        if (isset($_GET['old_role']) && "" != $_GET['old_role']) {
            if ('superadmin' == $_GET['old_role']) {
                $where_query .= " AND `FaUserLogin`.`is_super_admin` LIKE '1'";
            } else {
                $where_query .= " AND `FaUserLogin`.`old_role` LIKE '%" . esc_sql($_GET['old_role']) . "%'";
            }
        }

        if (!empty($_GET['date_type'])) {
            $date_type = esc_sql($_GET['date_type']);

            if (in_array($date_type, array('login', 'logout', 'last_seen'))) {
                if (isset($_GET['date_from']) && "" != $_GET['date_from']) {
                    $where_query .= " AND `FaUserLogin`.`time_$date_type` >= '" . esc_sql($_GET['date_from']) . " 00:00:00'";
                }

                if (isset($_GET['date_to']) && "" != $_GET['date_to']) {
                    $where_query .= " AND `FaUserLogin`.`time_$date_type` <= '" . esc_sql($_GET['date_to']) . " 23:59:59'";
                }
            }
        }
        $where_query = apply_filters('user_login_history_admin_prepare_where_query', $where_query);
        return $where_query;
    }

    /**
     * Retrieve rows
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @access   public
     * @return mixed
     */
    public function get_rows() {
        global $wpdb;

        $sql = " SELECT"
                . " FaUserLogin.*, "
                . " UserMeta.meta_value "
                . " FROM " . $this->table . "  AS FaUserLogin"
                . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                . " WHERE 1 ";

        $where_query = $this->prepare_where_query();
        if ($where_query) {
            $sql .= $where_query;
        }
        $sql .= ' GROUP BY FaUserLogin.id';
        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql .= ' ORDER BY id DESC';
        }

        if ($this->limit > 0) {
            $sql .= " LIMIT $this->limit";
            $sql .= ' OFFSET   ' . ( $this->page_number - 1 ) * $this->limit;;
        }
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        if ("" != $wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }
    
       public function record_count() {
        global $wpdb;
        $sql = " SELECT"
                . " FaUserLogin.id"
                . " FROM " . $this->table . " AS FaUserLogin"
                . " LEFT JOIN $wpdb->users AS User ON User.ID = FaUserLogin.user_id"
                . " LEFT JOIN $wpdb->usermeta AS UserMeta ON ( UserMeta.user_id=FaUserLogin.user_id"
                . " AND UserMeta.meta_key REGEXP '^wp([_0-9]*)capabilities$' )"
                . " WHERE 1 ";

        $where_query = $this->prepare_where_query();

        if ($where_query) {
            $sql .= $where_query;
        }
        $sql .= ' GROUP BY FaUserLogin.id';
        $sql_count = "SELECT COUNT(*) as total FROM ($sql) AS FaUserLoginCount ";

        $result = $wpdb->get_var($sql_count);
        if ("" != $wpdb->last_error) {
            User_Login_History_Error_Handler::error_log("last error:" . $wpdb->last_error . " last query:" . $wpdb->last_query, __LINE__, __FILE__);
        }
        return $result;
    }
    
    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'user_id' => __('User Id', 'user-login-history'),
            'username' => __('Username', 'user-login-history'),
            'role' => __('Current Role', 'user-login-history'),
            'old_role' => __('<span title="Role while user gets loggedin">Old Role(?)</span>', 'user-login-history'),
            'ip_address' => __('IP', 'user-login-history'),
            'browser' => __('Browser', 'user-login-history'),
            'operating_system' => __('OS', 'user-login-history'),
            'country_name' => __('Country', 'user-login-history'),
            'duration' => __('Duration', 'user-login-history'),
            'time_last_seen' => __('<span title="Last seen time in the session">Last Seen(?)</span>', 'user-login-history'),
            'timezone' => __('Timezone', 'user-login-history'),
            'time_login' => __('Login', 'user-login-history'),
            'time_logout' => __('Logout', 'user-login-history'),
            'user_agent' => __('User Agent', 'user-login-history'),
            'login_status' => __('Login Status', 'user-login-history'),
        );

        if (is_multisite()) {
            $columns['is_super_admin'] = __('Super Admin', 'user-login-history');
        }
        $columns = apply_filters('user_login_history_admin_get_columns', $columns);
        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'user_id' => array('user_id', true),
            'username' => array('username', true),
            'old_role' => array('old_role', true),
            'time_login' => array('time_login', false),
            'time_logout' => array('time_logout', false),
            'ip_address' => array('ip_address', false),
            'browser' => array('browser', false),
            'operating_system' => array('operating_system', false),
            'country_name' => array('country_name', false),
            'time_last_seen' => array('time_last_seen', false),
            'timezone' => array('timezone', false),
            'user_agent' => array('user_agent', false),
            'login_status' => array('login_status', false),
            'is_super_admin' => array('is_super_admin', false),
        );
        $sortable_columns = apply_filters('user_login_history_admin_get_sortable_columns', $sortable_columns);
        return $sortable_columns;
    }
    
    private function display_pagination() {
        echo $this->pagination_links;
        }
    public function display() {
?>
<table>
	<thead>
	<tr>
		<?php $this->display_column_headers(); ?>
	</tr>
	</thead>
	<tbody>
		<?php $this->display_rows_or_placeholder(); ?>
	</tbody>
</table>
<?php
		$this->display_pagination();
	}
        
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
	 * @since 3.1.0
	 */
	public function display_rows() {
		foreach ( $this->items as $item )
			$this->single_row( $item );
	}
        
        public function has_items() {
		return !empty( $this->items );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 3.1.0
	 */
	public function no_items() {
		_e( 'No items found.' );
	}
        
        	public function single_row( $item ) {
		echo '<tr>';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
}

