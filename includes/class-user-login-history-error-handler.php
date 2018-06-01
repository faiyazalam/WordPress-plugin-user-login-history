<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 */
class User_Login_History_Error_Handler {


    public static function error_log($message = '', $line = '', $file = '') {
        ini_set('error_log', WP_CONTENT_DIR . '/user-login-history.log');
        error_log("$message at line:" . $line . " file:" . $file);
    }

}
