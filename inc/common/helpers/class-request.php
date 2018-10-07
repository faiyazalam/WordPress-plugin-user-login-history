<?php
// E:\programs\xampp\htdocs\wp_ulhautoloader\wp-content\plugins\faulh/inc/common/helpers/class-request.php
//E:\programs\xampp\htdocs\wp_ulhautoloader\wp-content\plugins\faulh\inc\common\helpers\class-request
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

    class Request {
        
       static public function is_current_page_by_file_name($file = 'admin') {
        global $pagenow;
        return $file . '.php' == $pagenow;
    }

   


    }
