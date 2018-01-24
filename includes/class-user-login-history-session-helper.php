<?php
class User_Login_History_Session_Helper {

    const LAST_INSERT_ID_KEY = 'last_insert_id';
    const CURRENT_LOGIN_BLOG_ID_KEY = 'current_login_blog_id';

    static public function set_session_last_insert_id($id = NULL) {
        $_SESSION[__CLASS__][self::LAST_INSERT_ID_KEY] = $id ? $id : FALSE;
    }

   static public function set_session_current_login_blog_id($id = NULL) {
        $_SESSION[__CLASS__][self::CURRENT_LOGIN_BLOG_ID_KEY] = $id ? $id : get_current_blog_id();
    }

    static public function get_session_current_login_blog_id() {
        return isset($_SESSION[__CLASS__][self::CURRENT_LOGIN_BLOG_ID_KEY]) ? $_SESSION[__CLASS__][self::CURRENT_LOGIN_BLOG_ID_KEY] : FALSE;
    }

    static public function get_session_last_insert_id() {
        return isset($_SESSION[__CLASS__][self::LAST_INSERT_ID_KEY]) ? $_SESSION[__CLASS__][self::LAST_INSERT_ID_KEY] : FALSE;
    }

    

    
    
}
