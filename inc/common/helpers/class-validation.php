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
class Validation {

    /**
     * For backward compatibility, we use this method isEmptyString.
     * @param type $value
     * @return type
     */
    static public function isEmpty($value = '') {
        if (is_string($value)) {
            $value = trim($value);
            return (empty($value) || "unknown" == strtolower($value));
        }

        return empty($value);
    }

}
