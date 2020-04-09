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
class Tool {

    /**
     * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
     * to the end of the array.
     *
     * @param array $array
     * @param string $key
     * @param array $new
     *
     * @return array
     */
    static public function array_insert_after(array $array, $key, array $new) {
        $keys = array_keys($array);
        $index = array_search($key, $keys);
        $pos = false === $index ? count($array) : $index + 1;
        return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
    }

    static public function getRoleNamesByKeys($keys = []) {
        if (empty($keys)) {
            return FALSE;
        }

        if (!function_exists('get_editable_roles')) {
            require_once( ABSPATH . 'wp-admin/includes/user.php' );
        }

        if (is_string($keys)) {
            $keys = explode(",", $keys);
        }

        $names = [];
        $editable_roles = array_reverse(get_editable_roles());

        foreach ($keys as $key) {
            $editable_role = $editable_roles[trim($key)];
            $names[] = translate_user_role($editable_role['name']);
        }

        return implode(", ", $names);
    }

}
