<?php

/**
 * The class is used for session management.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
class User_Login_History_Session_Helper {

    /**
     * Holds the key for the last insert id.
     */
    const LAST_INSERT_ID_KEY = 'last_insert_id';

    /**
     * Holds the key for the current blog id on which user gets loggedin.
     */
    const CURRENT_LOGIN_BLOG_ID_KEY = 'current_login_blog_id';

    /**
     * Sets last insert id in the session.
     * @param int|string $id
     */
    static public function set_last_insert_id($id = NULL) {
        $_SESSION[USER_LOGIN_HISTORY_NAME][self::LAST_INSERT_ID_KEY] = $id ? $id : FALSE;
    }

    /**
     * Sets the blog id in the session.
     * @param int|string $id
     */
    static public function set_current_login_blog_id($id = NULL) {
        $_SESSION[USER_LOGIN_HISTORY_NAME][self::CURRENT_LOGIN_BLOG_ID_KEY] = $id ? $id : get_current_blog_id();
    }

    /**
     * Gets the blog id from the session.
     * @return int The blog id from which user gets loggedin.
     */
    static public function get_current_login_blog_id() {
        return isset($_SESSION[USER_LOGIN_HISTORY_NAME][self::CURRENT_LOGIN_BLOG_ID_KEY]) ? $_SESSION[USER_LOGIN_HISTORY_NAME][self::CURRENT_LOGIN_BLOG_ID_KEY] : NULL;
    }

    /**
     * Gets the last insert id from the session.
     * @return int The last insert id from the session.
     */
    static public function get_last_insert_id() {
        return isset($_SESSION[USER_LOGIN_HISTORY_NAME][self::LAST_INSERT_ID_KEY]) ? $_SESSION[USER_LOGIN_HISTORY_NAME][self::LAST_INSERT_ID_KEY] : FALSE;
    }

    /**
     * Destroys the session created by this class.
     */
    static public function destroy() {
        unset($_SESSION[USER_LOGIN_HISTORY_NAME]);
    }
    
    }
