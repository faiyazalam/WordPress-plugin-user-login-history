<?php

namespace User_Login_History\Inc\Common\Helpers;

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

class ErrorLogHelper {
        
    const ERROR_LOG_FILE = '/user-login-history.log';

    public static function error_log($message = '', $line = '', $file = '') {
        ini_set('error_log', WP_CONTENT_DIR . self::ERROR_LOG_FILE);
        error_log("$message at line:" . $line . " file:" . $file);
    }
       
}