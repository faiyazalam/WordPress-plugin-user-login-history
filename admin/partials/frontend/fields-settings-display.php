<?php
/**
 * setting fields for frontend table.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials/frontend
 * @author     Er Faiyaz Alam
 */
?>
<?php
$option_name = ULH_PLUGIN_OPTION_PREFIX.'frontend_fields';
$options = get_option($option_name);
$options['old_role'] = isset($options['old_role']) ? $options['old_role'] : FALSE;
$options['ip_address'] = isset($options['ip_address']) ? $options['ip_address'] : FALSE;
$options['browser'] = isset($options['browser']) ? $options['browser'] : FALSE;
$options['operating_system'] = isset($options['operating_system']) ? $options['operating_system'] : FALSE;
$options['country'] = isset($options['country']) ? $options['country'] : FALSE;
$options['last_seen'] = isset($options['last_seen']) ? $options['last_seen'] : FALSE;
$options['login'] = isset($options['login']) ? $options['login'] : FALSE;
$options['logout'] = isset($options['logout']) ? $options['logout'] : FALSE;
$options['duration'] = isset($options['duration']) ? $options['duration'] : FALSE;
?>
<input id="old_role" type="checkbox" name="<?php echo $option_name;?>[old_role]" value="1"  <?php checked($options['old_role'], 1); ?> /><label for="old_role"><?php _e('Old Role', 'user-login-history')?></label></hr></hr>
<input id="ip_address" type="checkbox" name="<?php echo $option_name;?>[ip_address]" value="1"  <?php checked($options['ip_address'], 1); ?> /><label for="ip_address"><?php _e('IP Address', 'user-login-history')?></label></hr></hr>
<input id="browser" type="checkbox" name="<?php echo $option_name;?>[browser]" value="1"  <?php checked($options['browser'], 1); ?> /><label for="browser"><?php _e('Browser', 'user-login-history')?></label></hr></hr>
<input id="operating_system" type="checkbox" name="<?php echo $option_name;?>[operating_system]" value="1"  <?php checked($options['operating_system'], 1); ?> /><label for="operating_system"><?php _e('Operating System', 'user-login-history')?></label></hr></hr>
<input id="country" type="checkbox" name="<?php echo $option_name;?>[country]" value="1"  <?php checked($options['country'], 1); ?> /><label for="country"><?php _e('Country', 'user-login-history')?></label></hr></hr>
<input id="duration" type="checkbox" name="<?php echo $option_name;?>[duration]" value="1"  <?php checked($options['duration'], 1); ?> /><label for="duration"><?php _e('Duration', 'user-login-history')?></label></hr></hr>
<input id="last_seen" type="checkbox" name="<?php echo $option_name;?>[last_seen]" value="1"  <?php checked($options['last_seen'], 1); ?> /><label for="last_seen"><?php _e('Last Seen', 'user-login-history')?></label></hr></hr>
<input id="login" type="checkbox" name="<?php echo $option_name;?>[login]" value="1"  <?php checked($options['login'], 1); ?> /><label for="login"><?php _e('Login', 'user-login-history')?></label></hr></hr>
<input id="logout" type="checkbox" name="<?php echo $option_name;?>[logout]" value="1"  <?php checked($options['logout'], 1); ?> /><label for="logout"><?php _e('Logout', 'user-login-history')?></label></hr></hr>
<?php do_action('user_login_history_frontend_fields_setting', $option_name, $options); ?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#submit').click(function () {
            checked = jQuery("input[type=checkbox]:checked").length;
            if (!checked) {
                alert("You must check at least one checkbox for column.");
                return false;
            }
        });
    });
</script>