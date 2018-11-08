<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
use User_Login_History\Inc\Common\Abstracts\User_Profile as User_Profile_Abstract;

class User_Profile extends User_Profile_Abstract {

    /**
     * The callback function for the action hook - show_user_profile.
     */
    public function show_user_profile($user) {
        $user_timezone = get_user_meta($this->get_user_id(), $this->get_usermeta_key_timezone(), TRUE);
        ?>
        <h3 id="<?php echo $this->plugin_name ?>"><?php esc_html_e('User Login History', $this->plugin_text_domain) ?></h3>

        <table class="faulh-form-table">
            <tr class="<?php echo "user-" . $this->get_usermeta_key_timezone() . "-wrap" ?>">
                <th><label for="<?php echo $this->get_usermeta_key_timezone() ?>"><?php esc_html_e('Timezone', 'faulh'); ?></label></th>
                <td>
                    <select required="required" id="<?php echo $this->get_usermeta_key_timezone() ?>" name="<?php echo $this->get_usermeta_key_timezone() ?>">
                        <option value=""><?php esc_html_e('Select Timezone', 'faulh') ?></option>
                        <?php
                        Template_Helper::dropdown_timezones($user_timezone);
                        ?>
                    </select>
                    <div><?php esc_html_e('This is used to convert date-time (e.g. login time, last seen time etc.) on the listing table.', $this->plugin_text_domain) ?></div>
                </td>
            </tr>

        </table>
        <?php
    }

    /**
     * The callback function for the action hook - user_profile_update_errors.
     */
    public function user_profile_update_errors($errors, $update, $user) {
        if (!$update) {
            return;
        }

        if (empty($_POST[$this->get_usermeta_key_timezone()])) {

            $errors->add('user_timezone_error', sprintf(esc_html__('%1$sERROR%2$s: Please select a timezone.', 'faulh'), "<strong>", "</strong>"));
        }
    }

    /**
     * The callback function for the action hook - edit_user_profile_update.
     */
    public function update_profile_fields($user_id) {

        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        $this->update_usermeta_key_timezone();
        $this->delete_old_usermeta_key_timezone();
    }

}
