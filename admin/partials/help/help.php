<?php
/**
 * Help page for the plugin.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials/help
 * @author     Er Faiyaz Alam
 */
?>
<div class="clearfix">
    <div class="user-login-history-right-col">
        <h2><?php _e('How to use User Login History Plug-in?', 'user-login-history'); ?></h2>
        <ol>
           
            <li>
                <p><?php _e('To see all the tracked records in admin, click on the plug-in menu shown in the left sidebar.', 'user-login-history'); ?></p>
            </li>
            <li>
                <p><?php _e('To see all the tracked records of current loggedin user in frontend, use the following shortcode in your template file:', 'user-login-history'); ?> 
                <pre><code>&lt;?php do_shortcode('[user-login-history]'); ?&gt;</code></pre>
                </p>
            </li>
        </ol>

        <h3><?php _e('I need more help!', 'user-login-history'); ?></h3>
        <p><?php echo sprintf(__('I try to be as helpful as I can. You can find several topics in the WordPress forum or ask for help to a new issue. It\'s also possible to start an issue on <a target="_blank" href="%s">github.</a>', 'user-login-history'), 'https://github.com/faiyazalam/user-login-history'); ?></p>
        <p><strong><?php _e('Please search the forum before starting a new topic.', 'user-login-history'); ?></strong></p>
        <p><a href="https://wordpress.org/support/plugin/user-login-history" class="button-secondary" target="_blank"><?php _e('Check the forum', 'user-login-history'); ?></a></p>
       


    </div>
</div>