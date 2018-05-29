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
$user_timezone = get_user_meta($current_user->ID, $option_name, TRUE);
$user_timezone = $user_timezone ? $user_timezone : 0;
$timezones = User_Login_History_Date_Time_Helper :: get_timezone_list();
?>
<h2><?php _e('Backend Options', 'user-login-history') ?></h2>
<div class="user-login-history-tabs">
    <form method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <span class="btf-tooltip" title="<?php _e('Convert date-time into the preferred timezone before showing them on the listing table.', 'user-login-history') ?>">?</span><?php _e('Timezone', 'user-login-history') ?>:
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
                        <input type="hidden" name="update_user_timezone_nonce" value="<?php echo wp_create_nonce(ULH_PLUGIN_OPTION_PREFIX.'update_user_timezone_secure_nonce') ?>">
                    </td>
                </tr>
            </tbody>
        </table>                 <?php submit_button(); ?>           </form>
</div>