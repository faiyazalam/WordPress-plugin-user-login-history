<?php
/**
 * This page is for plugin notice.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials/about
 * @author     Er Faiyaz Alam
 */
?>
<?php
if(!ini_get('allow_url_fopen') ) {
    return;
} 
$plugin_notice = file_get_contents('https://raw.githubusercontent.com/faiyazalam/user-login-history/plugin_notice/plugin_notice.txt');
if(false == $plugin_notice || "" == $plugin_notice || ctype_space($plugin_notice) )
{
    return;
}
?>
<div class="update-nag"><p><?php echo $plugin_notice ?></p></div>
<div><p><?php _e('The above notice will be removed automatically.', 'user-login-history')?></p></div>
