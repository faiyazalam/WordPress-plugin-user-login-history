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
if (!class_exists('Faulh_User_Profile')) {

    class Faulh_User_Profile {

        /**
         * The unique identifier of this plugin.
         *
         * @access   private
         * @var      string    $plugin_name    The string used to uniquely identify this plugin.
         */
        private $plugin_name;

        /**
         * The current version of the plugin.
         *
         * @access   protected
         * @var      string    $version    The current version of the plugin.
         */
        private $version;

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
        public function __construct($plugin_name, $version, $user_id = NULL) {

            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->usermeta_key_timezone = $this->plugin_name . "_timezone";
            $this->user_id = $user_id;
        }

        /**
         * The callback function for the action hook - show_user_profile.
         */
        function show_extra_profile_fields($user) {
            $user_timezone = get_user_meta($user->ID, $this->usermeta_key_timezone, TRUE);
            ?>
            <style>


            </style>
            <h3 id="<?php echo $this->plugin_name ?>"><?php echo Faulh_Template_Helper::plugin_name(); ?></h3>

            <table class="faulh-form-table">
                <tr class="user-<?php echo $this->plugin_name . "-timezone" ?>-wrap">
                    <th><label for="<?php echo $this->plugin_name . "-timezone" ?>"><?php esc_html_e('Timezone', 'faulh'); ?></label></th>
                    <td>
                        <select required="required" name="<?php echo $this->plugin_name . '-timezone' ?>">
                            <option value=""><?php esc_html_e('Select Timezone', 'faulh') ?></option>
                            <?php
                            Faulh_Template_Helper::dropdown_timezones($user_timezone);
                            ?>
                        </select>
                        <div><?php esc_html_e('This is used to convert date-time (e.g. login time, last seen time etc.) on the listing table.', 'faulh') ?></div>
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
                // $errors->add('user_timezone_error', esc_html__('<strong>ERROR</strong>: Please select a timezone.', 'faulh'));
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
            } else {
                delete_user_meta($user_id, $this->usermeta_key_timezone);
            }
            $this->delete_old_meta_by_user_id($user_id);
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
            if (empty($current_user->ID)) {
                return FALSE;
            }

            $timezone = get_user_meta($current_user->ID, $this->usermeta_key_timezone, TRUE);
            return $timezone && "unknown" != strtolower($timezone) ? $timezone : FALSE;
        }

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

            global $current_user;
            if (!current_user_can('edit_user', $current_user->ID)) {
                return false;
            }
            if (!empty($_POST[$this->plugin_name . "-timezone"])) {
                update_user_meta($current_user->ID, $this->usermeta_key_timezone, $_POST[$this->plugin_name . "-timezone"]);
            } else {
                delete_user_meta($current_user->ID, $this->usermeta_key_timezone);
            }

            $this->delete_old_meta_by_user_id($current_user->ID);
            wp_safe_redirect(esc_url_raw(add_query_arg()));
            exit;
        }

        /**
         * Delete old user meta data for old version.
         * @param int $user_id
         * @access private
         */
        private function delete_old_meta_by_user_id($user_id = NULL) {
            if (empty($user_id)) {
                return;
            }
            //delete the old usermeta key
            if (version_compare($this->version, '1.7.0', '<=')) {
                delete_user_meta($user_id, 'fa_userloginhostory_user_timezone');
            }
        }

    } 

}

