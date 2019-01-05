<?php

namespace User_Login_History\Inc\Common\Abstracts;

abstract class User_Profile {

    /**
     * The unique identifier of this plugin.
     *
     * @access   private
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * The usermeta key.
     *
     * @access   private
     * @var      string    $usermeta_key_timezone
     */
    protected $usermeta_key_timezone;

    /**
     * The user id.
     *
     * @access   private
     * @var      string    $user_id
     */
    protected $user_id;

    /**
     * Initialize the class and set its properties.
     *
     * @var      string    $plugin_name       The name of this plugin.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Callback function for the hook - init.
     */
    public function init() {
        $this->set_user_id();
        $this->set_usermeta_key_timezone();
    }

    public function set_user_id($user_id = NULL) {

        if (!empty($user_id)) {
            $this->user_id = absint($user_id);
        } else {

            global $current_user;

            $this->user_id = !empty($current_user->ID) ? $current_user->ID : FALSE;
        }

        return $this;
    }

    public function get_user_id() {
        return $this->user_id;
    }

    public function set_usermeta_key_timezone($key = NULL) {
        if (!empty($key) && is_string($key)) {
            $this->usermeta_key_timezone = $key;
        } else {
            $this->usermeta_key_timezone = $this->plugin_name . "_timezone";
        }
    }

    public function get_usermeta_key_timezone() {
        return $this->usermeta_key_timezone;
    }

    /**
     * Add timezone to the user profile.
     * 
     * @param int|string $user_id The user id.
     * @param string $timezone The timezone.
     * @return int|false Meta ID on success, false on failure.
     */
    public function add_timezone($timezone) {
        return add_user_meta($this->get_user_id(), $this->get_usermeta_key_timezone(), $timezone, true);
    }

    /**
     * Get timezone of the current user.
     * 
     * @global object $current_user
     * @return boolean|string False on failure, timezone on success.
     */
    public function get_user_timezone() {
        return get_user_meta($this->get_user_id(), $this->get_usermeta_key_timezone(), TRUE);
    }

    /**
     * Delete old user meta data for old version.
     * @access protected
     */
    protected function delete_old_usermeta_key_timezone() {
        if (empty($this->get_user_id())) {
            return;
        }
        //delete the old usermeta key
        if (version_compare($this->version, '1.7.0', '<=')) {
            delete_user_meta($this->get_user_id(), 'fa_userloginhostory_user_timezone');
        }
    }

    /**
     * Update user meta timezone.
     * @access protected
     */
    protected function update_usermeta_key_timezone() {

        if (!empty($_POST[$this->get_usermeta_key_timezone()])) {
            update_user_meta($this->get_user_id(), $this->get_usermeta_key_timezone(), $_POST[$this->get_usermeta_key_timezone()]);
        } else {
            delete_user_meta($this->get_user_id(), $this->get_usermeta_key_timezone());
        }
    }

}
