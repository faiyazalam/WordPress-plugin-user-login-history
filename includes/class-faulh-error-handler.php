<?php

/**
 * The class saves error log in the plugin log file.
 *
 * @link       https://github.com/faiyazalam
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if(!class_exists('Faulh_Error_Handler'))
{
  class Faulh_Error_Handler {

    const ERROR_LOG_FILE = '/user-login-history.log';

    public static function error_log($message = '', $line = '', $file = '') {
        ini_set('error_log', WP_CONTENT_DIR . self::ERROR_LOG_FILE);
        error_log("$message at line:" . $line . " file:" . $file);
    }

}  
}
