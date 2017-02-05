<?php
/**
 * setting field for timezone for admin.
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
$option_name = ULH_PLUGIN_OPTION_PREFIX.'frontend_limit';
$option = get_option($option_name);
?>

<?php _e('Show Records Per Page', 'user-login-history')?><input type="number" min="1" max="100"  name="<?php echo $option_name;?>" value="<?php echo $option ?>"  />