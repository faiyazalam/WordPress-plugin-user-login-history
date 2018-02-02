<?php
class User_Login_History_Session_Helper {

    const LAST_INSERT_ID_KEY = 'last_insert_id';
    const CURRENT_LOGIN_BLOG_ID_KEY = 'current_login_blog_id';

    static public function set_last_insert_id($id = NULL) {
        $_SESSION[USER_LOGIN_HISTORY_NAME][self::LAST_INSERT_ID_KEY] = $id ? $id : FALSE;
    }

   static public function set_current_login_blog_id($id = NULL) {
        $_SESSION[USER_LOGIN_HISTORY_NAME][self::CURRENT_LOGIN_BLOG_ID_KEY] = $id ? $id : get_current_blog_id();
    }

    static public function get_current_login_blog_id() {
        return isset($_SESSION[USER_LOGIN_HISTORY_NAME][self::CURRENT_LOGIN_BLOG_ID_KEY]) ? $_SESSION[USER_LOGIN_HISTORY_NAME][self::CURRENT_LOGIN_BLOG_ID_KEY] : NULL;
    }

    static public function get_last_insert_id() {
        return isset($_SESSION[USER_LOGIN_HISTORY_NAME][self::LAST_INSERT_ID_KEY]) ? $_SESSION[USER_LOGIN_HISTORY_NAME][self::LAST_INSERT_ID_KEY] : FALSE;
    }

    

    
    
}
