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
    <?php Faulh_Template_Helper::head(esc_html__('How to use the plugin?', 'faulh')); ?>
    <div class="clearfix">
        <div class="faulh-submenu-page-<?php echo!empty($_GET['page']) ? $_GET['page'] : "" ?>">
            <ol>
                <li>
                    <p><?php esc_html_e('To see all the tracked records in admin, click on the plugin menu shown in the left sidebar.', 'faulh'); ?></p>
                </li>
                <li>
                    <p><?php esc_html_e('To see all the tracked records of current logged in users in frontend, use the following shortcode in your template file:', 'faulh'); ?> 
                    <pre><code>&lt;?php echo do_shortcode('[user_login_history]'); ?&gt;</code></pre>
                    </p>
                </li>
                <li>
                    <p><strong><?php esc_html_e('Advanced Usage of shortcode:', 'faulh'); ?></strong>
                    <pre><code>&lt;?php echo do_shortcode("[user_login_history limit='20' reset_link='my-logins' columns='ip_address,time_login' date_format='Y-m-d' time_format='H:i:s']"); ?&gt;</code></pre>
                    </p>
                </li>
                <li>
                    <p><strong><?php esc_html_e('List of all the columns to be used in shortcode:', 'faulh') ?></strong><br><span>user_id, username, role, old_role, ip_address, browser, operating_system, country_name, duration, time_last_seen, timezone, time_login, time_logout, user_agent, login_status</span></p>
                </li>
            </ol>


            <h2><?php esc_html_e('Geo Tracking', 'faulh'); ?>: </h2>
            <p><?php echo sprintf(esc_html__('The plugin uses a free %1$sthird party service%2$s to detect country and timezone based on IP address. Many projects are using this free service due to which sometimes the server of the service provider becomes slow. This affects the login functionality of your website. Hence it is recommended that do not enable this functionallity unless you have paid service or reliable service. If you have a paid service, you can contact me to integrate it.', 'faulh'), "<a target='_blank' href='https://tools.keycdn.com/geo'>", "</a>") ?></p>



            <h2><?php esc_html_e('Login Statuses', 'faulh'); ?>: </h2>
            <ol>
                <li>
                    <p><strong><?php esc_html_e('Logged in', 'faulh') ?></strong> - <?php esc_html_e('If user gets logged in successfully.', 'faulh') ?></p>
                </li>
                <li>
                    <p><strong><?php esc_html_e('Logged out', 'faulh') ?></strong> - <?php esc_html_e('If user clicks on logout button and gets logged out successfully.', 'faulh') ?></p>
                </li>
                <li>
                    <p><strong><?php esc_html_e('Failed', 'faulh') ?></strong> - <?php esc_html_e('If user enters invalid credential.', 'faulh') ?></p>
                </li>
                <li>
                    <p><strong><?php esc_html_e('Blocked', 'faulh') ?></strong> - <?php esc_html_e('This is used for multisite network. 
By default a user can login to any blog and then wordpress redirects him to his blog. 
The plugin saves login info at the blog on which he logged in but cannot not save the info of the blog on which wordpress redirected him. 
You can prevent this behavior by using the plugin setting.', 'faulh') ?></p>
                </li>
            </ol>
            <p><strong><?php esc_html_e('Note', 'faulh') ?></strong> - <?php esc_html_e('In case a user logs in with "Remember Me" and then closes his browser without doing logout, it will show the login status "Logged in".', 'faulh') ?></p>

            <h2><?php esc_html_e('Deprecated Shortcode (Since 1.7)', 'faulh'); ?>: </h2>
            <p><?php esc_html_e('Please do not use the following shortcode as it is deprecated and will be removed in future.', 'faulh') ?></p>
            <pre><code>&lt;?php echo do_shortcode('[user-login-history]'); ?&gt;</code></pre>


            <h2><?php esc_html_e('Bug Fixes:', 'faulh'); ?></h2>
            <p><?php esc_html_e('If you find any bug, please create a topic with a step by step description to reproduce the bug.', 'faulh'); ?></strong></p>
            <p><strong><?php esc_html_e('Please search the forum before creating a new topic.', 'faulh'); ?></p>
            <p><a href="https://wordpress.org/support/plugin/user-login-history" class="button-secondary" target="_blank"><?php esc_html_e('Search the forum', 'faulh'); ?></a></p>
        </div>
    </div>

</div>
