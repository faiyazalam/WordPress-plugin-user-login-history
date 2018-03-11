<?php

/**
 * Template file to render listing table for admin.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials
 */
?>
<div class="wrap">
    <?php Faulh_Template_Helper::head(esc_html__('How to use the plugin?', 'user-login-history')); ?>
    <div class="clearfix">
    <div class="faulh-submenu-page-<?php echo !empty($_GET['page'])?$_GET['page']:""?>">
        <ol>
            <li>
                <p><?php esc_html_e('To see all the tracked records in admin, click on the plugin menu shown in the left sidebar.', 'user-login-history'); ?></p>
            </li>
            <li>
                <p><?php esc_html_e('To see all the tracked records of current loggedin users in frontend, use the following shortcode in your template file:', 'user-login-history'); ?> 
                <pre><code>&lt;?php echo do_shortcode('[user-login-history]'); ?&gt;</code></pre>
                </p>
            </li>
            <li>
                <p><?php esc_html_e('Advanced Usage of shortcode:', 'user-login-history'); ?> 
<pre><code>&lt;?php echo do_shortcode("[user-login-history limit='20' reset_link='my-logins' columns='ip_address,time_login' date_format='Y-m-d' time_format='H:i:s']"); ?&gt;</code></pre>
                </p>
            </li>
            <li>
                <p><strong><?php esc_html_e('List of all the colums to be used in shortcode:', 'faulh') ?></strong><br><span>user_id, username, role, old_role, ip_address, browser, operating_system, country_name, duration, time_last_seen, timezone, time_login, time_logout, user_agent, login_status</span></p>
            </li>
        </ol>
        <h3><?php esc_html_e('Bug Fixes:', 'user-login-history'); ?></h3>
        <p><?php esc_html_e('If you find any bug, please create a topic with a step by step description to reproduce the bug.', 'user-login-history'); ?></strong></p>
        <p><strong><?php esc_html_e('Please search the forum before creating a new topic.', 'user-login-history'); ?></p>
        <p><a href="https://wordpress.org/support/plugin/user-login-history" class="button-secondary" target="_blank"><?php esc_html_e('Search the forum', 'user-login-history'); ?></a></p>
    </div>
</div>
    
</div>
