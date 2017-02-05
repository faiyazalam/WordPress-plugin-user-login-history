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
?>
<input type="checkbox" name="<?php echo $option_name;?>[old_role]" value="1"  <?php checked($options['old_role'], 1); ?> /><?php _e('Old Role', 'user-login-history')?></hr></hr>
<input type="checkbox" name="<?php echo $option_name;?>[ip_address]" value="1"  <?php checked($options['ip_address'], 1); ?> /><?php _e('IP Address', 'user-login-history')?></hr></hr>
<input type="checkbox" name="<?php echo $option_name;?>[browser]" value="1"  <?php checked($options['browser'], 1); ?> /><?php _e('Browser', 'user-login-history')?></hr></hr>
<input type="checkbox" name="<?php echo $option_name;?>[operating_system]" value="1"  <?php checked($options['operating_system'], 1); ?> /><?php _e('Operating System', 'user-login-history')?></hr></hr>
<input type="checkbox" name="<?php echo $option_name;?>[country]" value="1"  <?php checked($options['country'], 1); ?> /><?php _e('Country', 'user-login-history')?></hr></hr>
<input type="checkbox" name="<?php echo $option_name;?>[duration]" value="1"  <?php checked($options['duration'], 1); ?> /><?php _e('Duration', 'user-login-history')?></hr></hr>
<input type="checkbox" name="<?php echo $option_name;?>[last_seen]" value="1"  <?php checked($options['last_seen'], 1); ?> /> <?php _e('Last Seen', 'user-login-history')?></hr></hr>
<input type="checkbox" name="<?php echo $option_name;?>[login]" value="1"  <?php checked($options['login'], 1); ?> /><?php _e('Login', 'user-login-history')?></hr></hr>
<input type="checkbox" name="<?php echo $option_name;?>[logout]" value="1"  <?php checked($options['logout'], 1); ?> /><?php _e('Logout', 'user-login-history')?></hr></hr>
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