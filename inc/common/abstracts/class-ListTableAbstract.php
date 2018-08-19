<?php

namespace User_Login_History\Inc\Common\Abstracts;
use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\TemplateHelper;
use User_Login_History\Inc\Common\Helpers\DateTimeHelper;
use User_Login_History\Inc\Admin\LoginTracker;
use User_Login_History\Inc\Common\Helpers\ValidationHelper;


/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 * @since      1.0.0
 *
 * @author    Er Faiyaz Alam
 */

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

abstract class ListTableAbstract extends \WP_List_Table {

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	protected $plugin_text_domain;
	protected $table_timezone;
	protected $unknown_symbol = '<span aria-hidden="true">—</span>';
        
	public function __construct($plugin_name, $version,$plugin_text_domain, $args = array(), $timezone = '') {
            parent::__construct($args);
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->plugin_text_domain = $plugin_text_domain;
            $this->set_timezone($timezone);
        }
        
        
         /**
         * Sets the timezone to be used for table listing.
         * 
         * @access public
         * @param string $timezone
         */
        public function set_timezone($timezone = '') {
            $this->timezone = $timezone ? $timezone : 'UTC';
        }

        /**
         * Gets the timezone to be used for table listing.
         * 
         * @access public
         * @return string
         */
        public function get_timezone() {
            return $this->timezone;
        }
        
                /**
         * Handles data query and filter, sorting, and pagination.
         * 
         * @access public
         */
        public function prepare_items() {
            $this->_column_headers = $this->get_column_info();
            $per_page = $this->get_items_per_page($this->plugin_name . '_rows_per_page');

            $this->set_pagination_args(array(
                'total_items' => $this->record_count(),
                'per_page' => $per_page
            ));

            $this->items = $this->get_rows($per_page, $this->get_pagenum());
        }
        
        
        abstract public function get_rows();
        abstract public function record_count();
        
        
            public function column_default($item, $column_name) {
            $timezone = $this->get_table_timezone();
         
           
            $new_column_data = apply_filters('manage_faulh_admin_custom_column', '', $item, $column_name);
            $country_code = in_array(strtolower($item['country_code']), array("", $this->unknown_symbol)) ? $this->unknown_symbol : $item['country_code'];

            switch ($column_name) {

                case 'user_id':
                   return $this->is_empty($item[$column_name])? $this->unknown_symbol: absint($item[$column_name]);

                case 'username_csv':
                    return $this->is_empty($item['username'])? $this->unknown_symbol:esc_html($item['username']);

                case 'role':
                    if ($this->is_empty($item['user_id'])) {
                        return $this->unknown_symbol;
                    }
                     $user_data = get_userdata($item['user_id']);

                    return $this->is_empty($user_data->roles) ? $this->unknown_symbol : esc_html(implode(',', $user_data->roles));

                case 'old_role':
                    return $this->is_empty($item[$column_name]) ?$this->unknown_symbol : esc_html($item[$column_name]) ;

                case 'browser':
                    
                    if ($this->is_empty($item[$column_name])) {
                        return $this->unknown_symbol;
                    }

                    if (empty($item['browser_version'])) {
                        return esc_html($item[$column_name]);
                    }

                    return esc_html($item[$column_name] . " (" . $item['browser_version'] . ")");

               case 'ip_address':
                    return $this->is_empty($item[$column_name]) ?$this->unknown_symbol : esc_html($item[$column_name]) ;

                case 'timezone':
                    return $this->is_empty($item[$column_name]) ?$this->unknown_symbol : esc_html($item[$column_name]) ;


                case 'country_name':
                    
                  if ($this->is_empty($item[$column_name])) {
                        return $this->unknown_symbol;
                    }

                    if (empty($item['country_code'])) {
                        return esc_html($item[$column_name]);
                    }

                    return esc_html($item[$column_name] . " (" . $item['country_code'] . ")");


                case 'country_code':
                    return $this->is_empty($item[$column_name])? $this->unknown_symbol:esc_html($item[$column_name]);

                case 'operating_system':
 return $this->is_empty($item[$column_name])? $this->unknown_symbol:esc_html($item[$column_name]);
                    
                    
                        case 'time_login':
                    if (!(strtotime($item[$column_name]) > 0)) {
                        return $this->unknown_symbol;
                    }
                    $time_login = DateTimeHelper::convert_format(DateTimeHelper::convert_timezone($item[$column_name], '', $timezone));
                    return $time_login ? $time_login : $this->unknown_symbol;

                case 'time_logout':
                    if ($this->is_empty($item['user_id']) || !(strtotime($item[$column_name]) > 0)) {
                        return $this->unknown_symbol;
                    }
                    $time_logout = DateTimeHelper::convert_format(DateTimeHelper::convert_timezone($item[$column_name], '', $timezone));
                    return $time_logout ? $time_logout : $this->unknown_symbol;
             
                    
                    
                case 'time_last_seen':

                    $time_last_seen_unix = strtotime($item[$column_name]);
                    if ($this->is_empty($item['user_id']) || !($time_last_seen_unix > 0)) {
                        return $this->unknown_symbol;
                    }
                    $time_last_seen = DateTimeHelper::convert_format(DateTimeHelper::convert_timezone($item[$column_name], '', $timezone));
                      

                    if (!$time_last_seen) {
                        return $this->unknown_symbol;
                    }

                    $human_time_diff = human_time_diff($time_last_seen_unix);
                    $is_online_str = 'offline';

                    if (in_array($item['login_status'], array("", LoginTracker::LOGIN_STATUS_LOGIN))) {
                        $minutes = ((time() - $time_last_seen_unix) / 60);
                        $settings = get_option($this->plugin_name . "_basics");
                        $minute_online = !empty($settings['is_status_online']) ? absint($settings['is_status_online']) : NS\DEFAULT_IS_STATUS_ONLINE_MIN;
                        $minute_idle = !empty($settings['is_status_idle']) ? absint($settings['is_status_idle']) : NS\DEFAULT_IS_STATUS_IDLE_MIN;
                        if ($minutes <= $minute_online) {
                            $is_online_str = 'online';
                        } elseif ($minutes <= $minute_idle) {
                            $is_online_str = 'idle';
                        }
                    }


                    return "<div class='is_status_$is_online_str' title = '$time_last_seen'>" . $human_time_diff . " " . esc_html__('ago', 'faulh') . '</div>';

                case 'user_agent':
             return $this->is_empty($item[$column_name])? $this->unknown_symbol:esc_html($item[$column_name]);

                case 'duration':
                    return human_time_diff(strtotime($item['time_login']), strtotime($item['time_last_seen']));

                case 'login_status':
                    $login_statuses = TemplateHelper::login_statuses();
                    return !empty($login_statuses[$item[$column_name]]) ? $login_statuses[$item[$column_name]] : $this->unknown_symbol;

                case 'blog_id':
                    return !empty($item[$column_name]) ? (int) $item[$column_name] : $this->unknown_symbol;

                case 'is_super_admin':
                    $super_admin_statuses = TemplateHelper::super_admin_statuses();
                    return $super_admin_statuses[$item[$column_name] ? 'yes' : 'no'];

                default:
                    if ($new_column_data) {
                        return $new_column_data;
                    }
                    return print_r($item, true); //Show the whole array for troubleshooting purposes
            }
        }
        
        protected function is_empty($value = '') {
            return ValidationHelper::isEmpty($value);
        }
}
