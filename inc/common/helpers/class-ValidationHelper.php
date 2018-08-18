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

class ValidationHelper{

    /**
     * For backward compatibility, we use this method.
     * @param type $value
     * @return type
     */
    static public function isEmpty($value = '') {
        return (empty($value) || "unknown" == strtolower($value)) ? TRUE : FALSE;
    }
}
