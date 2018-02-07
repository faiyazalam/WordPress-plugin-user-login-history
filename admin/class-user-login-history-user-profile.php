<?php

/**
 * User_Login_History_DB_Helper
 * 
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/includes
 * @author     Er Faiyaz Alam
 */
class User_Login_History_User_Profile {

    private $plugin_name;

    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
           $this->user_meta_timezone = USER_LOGIN_HISTORY_USER_META_PREFIX . "user_timezone";
    }

   

    function show_extra_profile_fields($user) {
        $user_timezone = get_user_meta($user->ID, $this->user_meta_timezone, TRUE);
        ?>
        <h3><?php _e('User Login History', 'user-login-history'); ?></h3>

        <table>
            <tr>
                <th><label for="<?php echo $this->plugin_name . "-timezone" ?>"><?php _e('Timezone', 'user-login-history'); ?></label></th>
                <td>
                    <select required="required" name="<?php echo $this->plugin_name . '-timezone' ?>">
                        <option value=""><?php _e('Select Timezone', 'user-login-history') ?></option>
                        <?php
                        User_Login_History_Template_Helper::dropdown_timezones($user_timezone);
                        ?>
                    </select>
                </td>
            </tr>

        </table>
        <?php
    }

    function user_profile_update_errors($errors, $update, $user) {
        if (!$update) {
            return;
        }

        if (empty($_POST[$this->plugin_name . '-timezone'])) {
            $errors->add('user_timezone_error', __('<strong>ERROR</strong>: Please select a timezone.', 'user-login-history'));
        }
    }

    function update_profile_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        if (!empty($_POST[$this->plugin_name . '-timezone'])) {
            update_user_meta($user_id, $this->user_meta_timezone, $_POST[$this->plugin_name . '-timezone']);
        }
    }

}
