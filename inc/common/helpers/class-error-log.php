<?php

namespace User_Login_History\Inc\Common\Helpers;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 *
 * @author    Er Faiyaz Alam
 */
class Error_Log {

    const ERROR_LOG_FILE = '/user-login-history';

    public static function error_log($message = '', $line = '', $file = '') {
        ini_set('error_log', WP_CONTENT_DIR . self::ERROR_LOG_FILE . "-" . date('Y-m-d') . ".log");
        error_log("$message at line:" . $line . " file:" . $file);
    }

}
