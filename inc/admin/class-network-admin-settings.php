<?php

namespace User_Login_History\Inc\Admin;

/**
 * Network admnin settings.
 */
use User_Login_History as NS;
use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;

class Network_Admin_Settings {

    /**
     * The ID of this plugin.
     *
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Form name to be used in html form.
     * @var string 
     */
    private $form_name;

    /**
     * Form nonce to be used in html form.
     * @var string 
     */
    private $form_nonce_name;

    /**
     * Settings name to be used to save network settings only.
     * @var string 
     */
    private $settings_name;

    /**
     * Holds the instance of Admin Notice
     * @var Admin_Notice 
     */
    private $Admin_Notice;

    /**
     * Initialize the class and set its properties.
     *
     * @access public
     * @param      array    $args       The overridden arguments.
     * @param      string    $plugin_name       The name of this plugin.
     */
    function __construct($plugin_name, $version, Admin_Notice $Admin_Notice) {
        $this->plugin_name = $plugin_name;
        $this->form_name = $this->plugin_name . '_network_admin_setting_submit';
        $this->form_nonce_name = $this->plugin_name . '_network_admin_setting_nonce';
        $this->settings_name = $this->plugin_name . '_network_settings';
        $this->Admin_Notice = $Admin_Notice;
    }

    /**
     * Validate form submission.
     * @return bool
     */
    private function is_form_submitted() {
        return isset($_POST[$this->get_form_name()]) && !empty($_POST[$this->get_form_nonce_name()]) && wp_verify_nonce($_POST[$this->get_form_nonce_name()], $this->get_form_nonce_name()) && current_user_can('administrator');
    }

    /**
     * Update the settings.
     * 
     * @access private
     */
    private function update_settings() {

        $settings = array();

        if (isset($_POST['block_user'])) {
            $settings['block_user'] = 1;
        }
        if (isset($_POST['block_user_message'])) {
            $settings['block_user_message'] = sanitize_textarea_field($_POST['block_user_message']);
        }

        if (!empty($settings)) {
            update_site_option($this->settings_name, $settings);
        } else {
            delete_site_option($this->settings_name);
        }

        return TRUE;
    }

    public function get_form_name() {
        return $this->form_name;
    }

    public function get_form_nonce_name() {
        return $this->form_nonce_name;
    }

    /**
     * Hooked with network_admin_menu action
     */
    public function admin_menu() {
        add_submenu_page(
                'settings.php', NS\PLUGIN_NAME, NS\PLUGIN_NAME, 'administrator', $this->plugin_name . '-setting', array($this, 'screen')
        );
    }

    /**
     * The template file for setting page.
     * 
     * @access public
     * @return string The template file path.
     */
    public function screen() {
        require_once plugin_dir_path((__FILE__)) . 'views/settings/network-admin.php';
    }

    /**
     * Check nonce and form submission and then update the settings.
     * 
     * @access public
     */
    public function update() {
        if (!$this->is_form_submitted()) {
            return;
        }

        if ($this->update_settings()) {
            $message = esc_html__('Settings updated successfully.', 'faulh');
            $status = TRUE;
        } else {
            $message = esc_html__('Please try again.', 'faulh');
            $status = FALSE;
        }

        $this->Admin_Notice->add_notice($message, $status ? 'success' : 'error');
        wp_safe_redirect(esc_url(network_admin_url("settings.php?page=" . $_GET['page'])));
        exit;
    }

    /**
     * Get the setting by name.
     *
     * @param $setting string optional setting name
     */
    public function get_settings($setting = '') {
        $settings = wp_parse_args(get_site_option($this->settings_name), array(
            'block_user' => null,
            'block_user_message' => 'Please contact website administrator.',
        ));

        if ($setting) {
            return isset($settings[$setting]) ? maybe_unserialize($settings[$setting]) : null;
        }
        return $settings;
    }

    /**
     * Get block user settings
     * @return bool
     * TODO::Test if this function works correctly
     */
    public function get_block_user() {
        return $this->get_settings('block_user');
    }

    /**
     * Get message for block user settings.
     * @return string
     */
    public function get_block_user_message() {
        return $this->get_settings('block_user_message');
    }

}
