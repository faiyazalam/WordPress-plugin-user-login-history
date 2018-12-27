<?php

namespace User_Login_History\Inc\Admin;

/**
 * Add Timezone dropdown on user profile page in admin.
 */
use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
use User_Login_History\Inc\Common\Abstracts\User_Profile as User_Profile_Abstract;

class User_Profile extends User_Profile_Abstract {

    /**
     * Hooked with show_user_profile action action
     */
    public function show_extra_profile_fields($user) {
        ?>
        <h3 id="<?php echo $this->plugin_name ?>"><?php esc_html_e('User Login History', 'faulh') ?></h3>

        <table class="faulh-form-table">
            <tr class="<?php echo "user-" . $this->get_usermeta_key_timezone() . "-wrap" ?>">
                <th><label for="<?php echo $this->get_usermeta_key_timezone() ?>"><?php esc_html_e('Timezone', 'faulh'); ?></label></th>
                <td>
                    <select required="required" id="<?php echo $this->get_usermeta_key_timezone() ?>" name="<?php echo $this->get_usermeta_key_timezone() ?>">
                        <option value=""><?php esc_html_e('Select Timezone', 'faulh') ?></option>
                        <?php
                        Template_Helper::dropdown_timezones($this->get_user_timezone());
                        ?>
                    </select>
                    <div><?php esc_html_e('This is used to convert date-time (e.g. login time, last seen time etc.) on the listing table.', 'faulh') ?></div>
                </td>
            </tr>

        </table>
        <?php
    }

    /**
     * Hooked with user_profile_update_errors action
     */
    public function user_profile_update_errors($errors, $update, $user) {
        global $pagenow;
        if (in_array($pagenow, array('user-edit.php', 'profile.php')) && empty($_POST[$this->get_usermeta_key_timezone()])) {
            $errors->add($this->plugin_name . "_user_timezone_error", sprintf(esc_html__('%1$sERROR%2$s: Please select a timezone.', 'faulh'), "<strong>", "</strong>"));
        }
    }

    /**
     * Hooked with edit_user_profile_update action
     */
    public function update_profile_fields($user_id) {

        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        $this->update_usermeta_key_timezone();
        $this->delete_old_usermeta_key_timezone();
    }

}
