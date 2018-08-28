<?php

namespace User_Login_History\Inc\Frontend;

use User_Login_History\Inc\Common\Abstracts\User_Profile as User_Profile_Abstract;

class User_Profile extends User_Profile_Abstract {

    /**
     * Check form submission and nonce and then update user timezone.
     * This is used to handle request from front-end.
     * After processing request, it redirects to current page.
     * 
     * @access public
     */
    public function update_user_timezone() {
        if (!is_user_logged_in()) {
            return;
        }

        if (!isset($_POST[$this->plugin_name . "_update_user_timezone"]) || empty($_POST['_wpnonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], $this->plugin_name . "_update_user_timezone")) {
            return;
        }

        if (!current_user_can('edit_user', $this->get_user_id())) {
            return false;
        }
        $this->update_usermeta_key_timezone();

        $this->delete_old_usermeta_key_timezone($this->get_user_id());
        wp_safe_redirect(esc_url_raw(add_query_arg()));
        exit;
    }

}
