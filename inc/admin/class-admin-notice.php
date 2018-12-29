<?php

namespace User_Login_History\Inc\Admin;

/**
 * Handle admin notice functionality.
 *
 * @author    Er Faiyaz Alam
 */
class Admin_Notice {

    /**
     * Holds the suffix name of the transient.
     */
    const TRANSIENT_SUFFIX = "_admin_notice_transient";

    /**
     * Holds the interval of the transient.
     */
    const TRANSIENT_INTERVAL = 120;

    /**
     * Holds the name of transient.
     */
    private $transient_name;

    /**
     * Holds the plugin name.
     * This is used as prefix for transient name.
     */
    private $plugin_name;

    /**
     * Initialize the class and set its properties.
     *
     * @param       string $plugin_name        The name of this plugin.
     */
    public function __construct($plugin_name) {
        $this->transient_name = $this->plugin_name . self::TRANSIENT_SUFFIX;
    }

    /**
     * Add notice to the transient.
     * 
     * @param string $message
     * @param string $type Any message type string. This will be used as a class attribute. Default is success.
     */
    public function add_notice($message, $type = 'success') {
        $notices = get_transient($this->transient_name);
        if ($notices === false) {
            $new_notices[] = array($message, $type);
            set_transient($this->transient_name, $new_notices, self::TRANSIENT_INTERVAL);
        } else {
            $notices[] = array($message, $type);
            set_transient($this->transient_name, $notices, self::TRANSIENT_INTERVAL);
        }
    }

    /**
     * Hooked with admin_notices and network_admin_notices actions
     */
    public function show_notice() {
        $notices = get_transient($this->transient_name);
        if ($notices !== false) {
            foreach ($notices as $notice) {
                if (!empty($notice[1] && !empty($notice[0]))) {
                    echo '<div class="notice notice-' . $notice[1] . ' is-dismissible"><p>' . $notice[0] . '</p></div>';
                }
            }
            delete_transient($this->transient_name);
        }
    }

}
