<?php

/**
 * Faulh_Date_Time_Helper
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 * @access private
 */
if (!class_exists('Browser')) {
    require_once plugin_dir_path(dirname(__FILE__)) . 'vendors/Browser/Browser.php';
}

if (!class_exists('Faulh_Browser_Helper')) {

    class Faulh_Browser_Helper extends Browser {
        
    }

}

