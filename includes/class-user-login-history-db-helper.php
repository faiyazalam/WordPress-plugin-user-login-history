<?php

/**
 * User_Login_History_DB_Helper
 * 
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 */
class User_Login_History_DB_Helper {
//
// define('ULH_MESSAGE_INVALID_NONCE', __('Well tried.', 'user-login-history'));
//define('ULH_MESSAGE_RECORD_DELETED', __('The record(s) has been deleted.', 'user-login-history'));
//define('ULH_MESSAGE_NO_RECORD_FOUND', __('No records found.', 'user-login-history'));

    /**
     * 
     * @global type $wpdb
     * @param type $id
     * @return string
     */
    static public function get_blog_name_by_id($id = NULL) {
        if (!$id) {
            return '';
        }
        global $wpdb;
        return $wpdb->get_var("SELECT CONCAT(domain,path) AS blog_name FROM $wpdb->blogs WHERE blog_id = $id ");
    }
    
    static public function get_message($key = '') {
        $messages = array(
            'invalid_nonce'=>__('Well tried.', 'user-login-history'),
            'record_deleted'=>__('The record(s) has been deleted.', 'user-login-history'),
            'no_record_found'=>__('No records found.', 'user-login-history'),
            );
        
            return isset($messages[$key]) ? $messages[$key] : __('Message string is missing.', 'user-login-history');
    }

    

    
    static function get_table_name($blog_id = null) {
      global $wpdb;
    
      if($blog_id === NULL)
      {
          $blog_id = User_Login_History_Session_Helper::get_current_login_blog_id();
      }
      
             return $wpdb->get_blog_prefix($blog_id). USER_LOGIN_HISTORY_TABLE_NAME;
    }

    

    
    

}
