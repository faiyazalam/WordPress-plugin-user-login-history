<?php

namespace User_Login_History\Inc\Common\Helpers;

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

class Browser extends \Browser {
    
}
