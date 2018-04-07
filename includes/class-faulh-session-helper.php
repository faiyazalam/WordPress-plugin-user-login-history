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
if (!class_exists('Faulh_Session_Helper')) {

    class Faulh_Session_Helper {

        private $plugin_name;

        public function __construct($plugin_name) {
            $this->plugin_name = $plugin_name;
        }

        /**
         * Holds the key for the current blog id on which user gets logged in.
         */
        const LOGIN_BLOG_ID_KEY = 'login_blog_id';

        /**
         * Gets the blog id from the session.
         * @return int The blog id from which user gets logged in.
         */
        public function get_current_login_blog_id() {
            $user_id = get_current_user_id();
            if (!$user_id) {
                return FALSE;
            }

            $WP_Session_Tokens = WP_Session_Tokens::get_instance($user_id);
            $session = $WP_Session_Tokens->get(wp_get_session_token());
            return !empty($session[$this->plugin_name][self::LOGIN_BLOG_ID_KEY]) ? (int) $session[$this->plugin_name][self::LOGIN_BLOG_ID_KEY] : FALSE;
        }

        public function attach_session_information($array, $user_id) {
            return array($this->plugin_name => array(
                    self::LOGIN_BLOG_ID_KEY => get_current_blog_id(),
            ));
        }

    }

}

