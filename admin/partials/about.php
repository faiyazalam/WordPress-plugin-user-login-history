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
    <?php Faulh_Template_Helper::head(esc_html__('About The Plugin', 'faulh')); ?>
    <div class="clearfix">
        <div class="faulh-submenu-page-<?php echo !empty($_GET['page'])?$_GET['page']:""?>">
        <h4><?php esc_html_e('By this plugin you can track any visitor\'s login details with the following attributes:', 'faulh'); ?></h4>
        <ol>
            <li><?php esc_html_e('Login Date-Time', 'faulh'); ?></li>
            <li><?php esc_html_e('Logout Date-Time', 'faulh'); ?></li>
            <li><?php esc_html_e('Last Seen Date-Time', 'faulh'); ?></li>
            <li><?php esc_html_e('Login Status - Logged in/Logged out/Failed/Blocked', 'faulh'); ?></li>
            <li><?php esc_html_e('Online Status - Online/Offline/Idle', 'faulh'); ?></li>
            <li><?php esc_html_e('Session Duration - How long the user stayed on your website per session.', 'faulh'); ?></li>
            <li><?php esc_html_e('User ID', 'faulh'); ?></li>
            <li><?php esc_html_e('Username', 'faulh'); ?></li>
            <li><?php esc_html_e('IP Address', 'faulh'); ?></li>
            <li><?php esc_html_e('Browser', 'faulh'); ?></li>
            <li><?php esc_html_e('Operating System', 'faulh'); ?></li>
            <li><?php esc_html_e('Current Role', 'faulh'); ?></li>
            <li><?php esc_html_e('Old Role - The role while user gets logged-in into your website.', 'faulh'); ?></li>
            <li><?php esc_html_e('Country Name and Country Code (Based on IP Address)', 'faulh'); ?></li>
            <li><?php esc_html_e('Timezone (Based on IP Address)', 'faulh'); ?></li>
        </ol>
        <h4><?php esc_html_e('Other Useful Features:', 'faulh'); ?></h4>
        <ol>
            <li><?php esc_html_e('Preferable Timezone - You can select your preferred timezone to be used for the listing table.', 'faulh'); ?></li>
            <li><?php esc_html_e('Shortcode - To see the listing table on front-end for the current logged-in user, you can use the following shortcode in your template file.', 'faulh'); ?><br> <pre><code>&lt;?php echo do_shortcode('[user_login_history]'); ?&gt;</code></pre></li>
            <li><?php esc_html_e('Multisite Network (Since version 1.7) - On the network admin area, you can see the listing table which shows all the records fetched from all the blogs of the current network.', 'faulh'); ?><br> 
            </li>
            <li><?php esc_html_e('Advanced Search Filter', 'faulh'); ?></li>
            <li><?php esc_html_e('CSV Export', 'faulh'); ?></li>
        </ol>
        <h4><?php esc_html_e('Translations', 'faulh'); ?></h4>
        <p><?php esc_html_e('Currently, this plugin is available in two languages i.e. English and Italian.', 'faulh') ?></p>
        <p><a href="https://translate.wordpress.org/projects/wp-plugins/user-login-history" class="button-secondary" target="_blank">
                <?php esc_html_e('Download Language Files', 'faulh'); ?>
            </a></p>
        <h3><?php esc_html_e('I am here!', 'faulh'); ?></h3>
        <p><strong><?php esc_html_e('You can reach me through the following links:', 'faulh'); ?></strong></p>
        <p>
            <a href="https://profiles.wordpress.org/faiyazalam" class="button-secondary" target="_blank">WordPress</a>
            <a href="https://github.com/faiyazalam" class="button-secondary" target="_blank">Github</a>
            <a href="http://stackoverflow.com/users/4380588/faiyaz-alam" class="button-secondary" target="_blank">StackOverFlow</a>
            <a href="https://www.upwork.com/o/profiles/users/_~01737016f9bf37a62b/" class="button-secondary" target="_blank">UpWork</a>
            <a href="https://www.peopleperhour.com/freelancer/er-faiyaz/php-cakephp-zend-magento-moodle-tot/1016456" class="button-secondary" target="_blank">PeoplePerHour</a>
            <a href="https://www.linkedin.com/in/er-faiyaz-alam-0704219a" class="button-secondary" target="_blank">LinkedIn</a>
        </p>
       
    </div>
</div>
</div>
