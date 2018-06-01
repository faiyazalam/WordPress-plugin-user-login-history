<?php
/**
 * Template file to render help page for admin.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials
 */
?>
<?php
$columns = Faulh_DB_Helper::all_columns();
unset($columns['blog_id']);
unset($columns['is_super_admin']);
$columns_str = (implode(',', array_keys($columns)));
?>
<div class="wrap">
    <?php Faulh_Template_Helper::head(esc_html__('How to use the plugin?', 'faulh')); ?>
    <div class="clearfix">
        <div class="faulh-submenu-page-<?php echo!empty($_GET['page']) ? esc_attr($_GET['page']) : "" ?>">
            <ol>
                <li>
                    <p><?php esc_html_e('To see all the tracked records in admin, click on the plugin menu shown in the left sidebar.', 'faulh'); ?></p>
                </li>
                <li>
                    <p><?php esc_html_e('To see all the tracked records of current logged in users in frontend, use the following shortcode:', 'faulh'); ?></p>
                    <p><strong><?php esc_html_e('Normal Usage of Shortcode:', 'faulh'); ?></strong></p>
                    <p><?php esc_html_e('In your template file:', 'faulh'); ?></p>
                    <pre><code>&lt;?php echo do_shortcode('[user_login_history]'); ?&gt;</code></pre>
                    <p><?php esc_html_e('In your content:', 'faulh'); ?></p>
                    <pre><code>[user_login_history]</code></pre>
                    <p><strong><?php esc_html_e('Advanced Usage of Shortcode:', 'faulh'); ?></strong></p>
                    <p><?php esc_html_e('In your template file:', 'faulh'); ?></p>
                    <pre><code>&lt;?php echo do_shortcode("[user_login_history limit='20' reset_link='custom-uri' columns='ip_address,time_login' date_format='Y-m-d' time_format='H:i:s']"); ?&gt;</code></pre>
                    <p><?php esc_html_e('In your content:', 'faulh'); ?></p>
                    <pre><code>[user_login_history limit='20' reset_link='custom-uri' columns='ip_address,time_login' date_format='Y-m-d' time_format='H:i:s']</code></pre>


                    
                </li>
            </ol>
            <hr>
             <h2><?php esc_html_e('Shortcode Parameters:', 'faulh'); ?></h2>
                    <p><?php esc_html_e('Here is the list of all the parameters that you can use in the shortcode. All the parameters are optional.', 'faulh') ?></p>
                    <ol>
                        <li><strong>title</strong> - <?php esc_html_e('Title of the listing table.', 'faulh'); ?></li>
                  <li><strong>limit</strong> - <?php esc_html_e('Number of records per page. Default is:', 'faulh'); ?><pre><code>20</code></pre></li>
                  <li><strong>reset_link</strong> - <?php esc_html_e('Custom URI of the listing page. For the input "my-login-history", it will render a reset link with the following URL:', 'faulh'); ?><span><pre><code>www.example.com/<?php esc_html_e('my-login-history', 'faulh') ?></code></pre></span>
                  <?php esc_html_e('Default is the full permalink of the current post or page.', 'faulh') ?></li>
                        <li><strong>date_format</strong> - <?php esc_html_e('A valid date format. Default is:', 'faulh'); ?><pre><code>Y-m-d</code></pre></li>
                        <li><strong>time_format</strong> - <?php esc_html_e('A valid time format. Default is:', 'faulh'); ?><pre><code>H:i:s</code></pre></li>
                        <li><strong>show_timezone_selector</strong> - <?php esc_html_e('Whether you want to show timezone selector or not. Any value other than "true" will be treated as "false". Default is:', 'faulh'); ?><pre><code>true</code></pre></li>
                        <li><strong>columns</strong> - <?php esc_html_e('List of column keys used to render columns on the listing table. Default keys are:', 'faulh'); ?>
                            <span><pre><code>operating_system,browser,time_login,time_logout</code></pre></span>
                            <p><?php esc_html_e('Available Column Keys:', 'faulh') ?></p>
                            <span><pre><code><?php echo $columns_str?></code></pre></span>
                        </li>
                    </ol>
                       <hr>
            <h2><?php esc_html_e('Deprecated Shortcode (Since 1.7.0)', 'faulh'); ?>: </h2>
            <p><?php esc_html_e('Do not use the following shortcode as it is deprecated and will be removed in future.', 'faulh') ?></p>
            <pre><code>[user-login-history]</code></pre>

               <hr>
            <h2><?php esc_html_e('Geo Tracking', 'faulh'); ?>: </h2>
            <p><?php echo sprintf(esc_html__('The plugin uses a free %1$sthird party service%2$s to detect country and timezone based on IP address. '
                    . 'Many projects are using this free service due to which sometimes the server of the service provider becomes slow. '
                    . 'This may affect the login functionality of your website. '
                    . 'Hence it is recommended that you do not enable this functionallity unless you have paid service or reliable service. '
                    . 'If you have a paid service, you can contact us to integrate it.', 'faulh'), "<a target='_blank' href='https://tools.keycdn.com/geo'>", "</a>") ?></p>
              <hr>
            <h2><?php esc_html_e('Login Statuses', 'faulh'); ?>: </h2>
            <ol>
                <li>
                    <p><strong><?php esc_html_e('Logged in', 'faulh') ?></strong> - <?php esc_html_e('If user gets logged in successfully.', 'faulh') ?></p>
                </li>
                <li>
                    <p><strong><?php esc_html_e('Logged out', 'faulh') ?></strong> - <?php esc_html_e('If user clicks on logout button and gets logged out successfully.', 'faulh') ?></p>
                </li>
                <li>
                    <p><strong><?php esc_html_e('Failed', 'faulh') ?></strong> - <?php esc_html_e('If user enters invalid credentials.', 'faulh') ?></p>
                </li>
                <li>
                    <p><strong><?php esc_html_e('Blocked', 'faulh') ?></strong> - <?php esc_html_e('This is used for multisite network. 
By default, a user can login to any blog and then wordpress redirects to the blog on which the user is associated. 
The plugin saves login info at the blog on which the user logged in but cannot not save the information of the blog on which wordpress redirects the user. 
You can prevent this behavior by using the plugin setting.', 'faulh') ?></p>
                </li>
<li>
<p><strong><?php esc_html_e('Unknown', 'faulh') ?></strong> - <?php esc_html_e('Since we have added a new column "Login Status" in the version 1.7.0, its value will be empty in the database table after upgrading to 1.7.0. To filter such records, you can use this status.', 'faulh') ?></p>
</li>
            </ol>
            <p><strong><?php esc_html_e('Note', 'faulh') ?></strong> - <?php esc_html_e('In case, a user logs in with "Remember Me" and then closes his browser without doing logout, it will show the login status as "Logged in".', 'faulh') ?></p>

   <hr>
            <h2><?php esc_html_e('Bug Fixes:', 'faulh'); ?></h2>
            <p><?php esc_html_e('If you find any bug, please create a topic with a step by step description on how to reproduce the bug.', 'faulh'); ?></strong></p>
            <p><strong><?php esc_html_e('Please search the forum before creating a new topic.', 'faulh'); ?></p>
            <p><a href="https://wordpress.org/support/plugin/user-login-history" class="button-secondary" target="_blank"><?php esc_html_e('Search The Forum', 'faulh'); ?></a></p>
        </div>
    </div>
</div>
