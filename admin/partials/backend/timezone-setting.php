<?php
/**
 * setting field for timezone for admin.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials/backend
 * @author     Er Faiyaz Alam
 */
?>
<?php
global $current_user;
$option_name = ULH_PLUGIN_OPTION_PREFIX . "user_timezone";

if (isset($_POST[$option_name])) {
    update_user_meta($current_user->ID, $option_name, $_POST[$option_name]);
  $message ='<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible"> 
<p><strong>'.__("Settings saved.", "user-login-history").'</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.__("Dismiss this notice.", "user-login-history").'</span></button></div>';   
}

$user_timezone = get_user_meta($current_user->ID, $option_name, TRUE);
$user_timezone = $user_timezone ? $user_timezone : 0;
$timezones = User_Login_History_Date_Time_Helper :: get_timezone_list();
?>
<h2><?php _e('Backend Options', 'user-login-history') ?></h2>
<?php echo isset($message)?$message:'' ?>
<div class="user-login-history-tabs">
    <form method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <span class="btf-tooltip" title="<?php _e('Convert the saved time into the preferred timezone and then show on listing table.', 'user-login-history') ?>">?</span><?php _e('Timezone', 'user-login-history') ?>:
                    </th>
                    <td>
                        <select name="<?php echo $option_name ?>" style="font-family: 'Courier New', Courier, monospace; width: 450px;">
                            <option value="0"><?php _e('Select Timezone', 'user-login-history') ?></option>
<?php
foreach ($timezones as $timezone) {
    ?>

                                <option value="<?php print $timezone['zone'] ?>" <?php selected($user_timezone, $timezone['zone']); ?>>
    <?php echo $timezone['zone'] . "(" . $timezone['diff_from_GMT'] . ")" ?>
                                </option>
                                <?php } ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>                 <?php submit_button('Save Changes'); ?>           </form>
</div>