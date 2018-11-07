<?php

namespace User_Login_History\Inc\Admin;

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
class Admin_Notice {

    const TRANSIENT_SUFFIX = "_admin_notice_transient";
    const TRANSIENT_INTERVAL = 120;

    private $plugin_name;
    private $version;
    private $transient_name;

   
    public function __construct($plugin_name, $version, $plugin_text_domain) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_text_domain = $plugin_text_domain;
        $this->transient_name = $this->plugin_name . self::TRANSIENT_SUFFIX;
    }

    public function add_notice($message, $type = 'success') {
        $notices = get_transient($this->transient_name_name);
        if ($notices === false) {
            $new_notices[] = array($message, $type);
            set_transient($this->transient_name, $new_notices, self::TRANSIENT_INTERVAL);
        } else {
            $notices[] = array($message, $type);
            set_transient($this->transient_name, $notices, self::TRANSIENT_INTERVAL);
        }
    }

   
    public function show_notice() {
        $notices = get_transient($this->transient_name);
        if ($notices !== false) {
            foreach ($notices as $notice) {
                if(!empty($notice[1] && !empty($notice[0] )))
                {
                     echo '<div class="notice notice-' . $notice[1] . ' is-dismissible"><p>' . $notice[0] . '</p></div>';  
                }
            }
            delete_transient($this->transient_name);
        }
    }

}
