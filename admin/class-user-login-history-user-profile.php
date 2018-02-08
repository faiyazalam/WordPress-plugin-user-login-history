<?php

/**
 * This is used to manage user meta data.
 * 
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin
 * @author     Er Faiyaz Alam
 * @access private
 */
class User_Login_History_User_Profile {

    /**
     * The unique identifier of this plugin.
     *
     * @access   private
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    private $plugin_name;

    /**
     * The usermeta key.
     *
     * @access   private
     * @var      string    $usermeta_key_timezone
     */
    private $usermeta_key_timezone;
    
        /**
     * The user id.
     *
     * @access   private
     * @var      string    $user_id
     */
    private $user_id;

    /**
     * Initialize the class and set its properties.
     *
     * @var      string    $plugin_name       The name of this plugin.
     */
    public function __construct($plugin_name, $user_id = NULL) {
        $this->plugin_name = $plugin_name;
        $this->usermeta_key_timezone = $this->plugin_name . "_timezone";
        $this->user_id = $user_id;
    }


    /**
     * The callback function for the action hook - show_user_profile.
     */
    function show_extra_profile_fields($user) {
        $user_timezone = get_user_meta($user->ID, $this->usermeta_key_timezone, TRUE);
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

    /**
     * The callback function for the action hook - user_profile_update_errors.
     */
    function user_profile_update_errors($errors, $update, $user) {
        if (!$update) {
            return;
        }

        if (empty($_POST[$this->plugin_name . '-timezone'])) {
            $errors->add('user_timezone_error', __('<strong>ERROR</strong>: Please select a timezone.', 'user-login-history'));
        }
    }

    /**
     * The callback function for the action hook - edit_user_profile_update.
     */
    function update_profile_fields($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        if (!empty($_POST[$this->plugin_name . '-timezone'])) {
            update_user_meta($user_id, $this->usermeta_key_timezone, $_POST[$this->plugin_name . '-timezone']);
        }
    }

    /**
     * Add timezone to the user profile.
     * 
     * @param int|string $user_id The user id.
     * @param string $timezone The timezone.
     * @return int|false Meta ID on success, false on failure.
     */
    function add_timezone($timezone) {
             return add_user_meta($this->user_id, $this->usermeta_key_timezone, $timezone, true);
    }

    /**
     * Get timezone of the current user.
     * 
     * @global object $current_user
     * @return boolean|string False on failure, timezone on success.
     */
    public function get_current_user_timezone() {
            global $current_user;
           if(empty($current_user->ID) )
           {
               return FALSE;
           }
        $timezone = get_user_meta($current_user->ID, $this->usermeta_key_timezone, TRUE);
        return $timezone && "unknown" != strtolower($timezone) ? $timezone : FALSE;
    }
    
    
}
