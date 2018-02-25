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
    <?php Faulh_Template_Helper::head(__('About The Plugin', 'faulh')); ?>
    <div class="clearfix">
        <div class="faulh-submenu-page-<?php echo !empty($_GET['page'])?$_GET['page']:""?>">
        <h4><?php _e('By this plugin you can track any visitor\'s login details with the following attributes:', 'user-login-history'); ?></h4>
        <ol>
            <li><?php _e('Login Date-Time', 'user-login-history'); ?></li>
            <li><?php _e('Logout Date-Time', 'user-login-history'); ?></li>
            <li><?php _e('Last Seen Date-Time', 'user-login-history'); ?></li>
            <li><?php _e('Login Status - Success/Logout/Fail', 'user-login-history'); ?></li>
            <li><?php _e('Session Duration - How long the user stayed on your website per session.', 'user-login-history'); ?></li>
            <li><?php _e('User ID', 'user-login-history'); ?></li>
            <li><?php _e('Username', 'user-login-history'); ?></li>
            <li><?php _e('IP Address', 'user-login-history'); ?></li>
            <li><?php _e('Browser', 'user-login-history'); ?></li>
            <li><?php _e('Operating System', 'user-login-history'); ?></li>
            <li><?php _e('Current Role', 'user-login-history'); ?></li>
            <li><?php _e('Old Role - The role while user gets logged-in into your website.', 'user-login-history'); ?></li>
            <li><?php _e('Country Name', 'user-login-history'); ?></li>
            <li><?php _e('Country Code', 'user-login-history'); ?></li>
            <li><?php _e('Timezone', 'user-login-history'); ?></li>
        </ol>
        <h4><?php _e('Other Useful Features:', 'user-login-history'); ?></h4>
        <ol>
            <li><?php _e('Editable Timezone - You can select your preferred timezone to be used for the listing table.', 'user-login-history'); ?></li>
            <li><?php _e('Shortcode - To see the listing table on front-end for the current logged-in user, you can use the following shortcode in your template file.', 'user-login-history'); ?><br> <pre><code>&lt;?php echo do_shortcode('[user_login_history]'); ?&gt;</code></pre></li>
            <li><?php _e('Multisite Network (Since version 1.7) - On the network admin area, you can see the listing table which shows all the records fetched from all the blogs of the current network.', 'user-login-history'); ?><br> 
            </li>
            <li><?php _e('Advance Search Filter', 'user-login-history'); ?></li>
            <li><?php _e('CSV Export', 'user-login-history'); ?></li>
        </ol>
        <h4><?php _e('Translations', 'user-login-history'); ?></h4>
        <p><?php _e('Currently, this plugin is available in two languages i.e. English and Italian.', 'user-login-history') ?></p>
        <p><a href="https://translate.wordpress.org/projects/wp-plugins/user-login-history" class="button-secondary" target="_blank">
                <?php _e('Click here to download the language files.', 'user-login-history'); ?>
            </a></p>
        <h3><?php _e('I am here!', 'user-login-history'); ?></h3>
        <p><strong><?php _e('You can reach to me through the following links:', 'user-login-history'); ?></strong></p>
        <p>
            <a href="https://profiles.wordpress.org/faiyazalam" class="button-secondary" target="_blank">WordPress</a>
            <a href="https://github.com/faiyazalam" class="button-secondary" target="_blank">Github</a>
            <a href="http://stackoverflow.com/users/4380588/faiyaz-alam" class="button-secondary" target="_blank">StackOverFlow</a>
            <a href="https://www.upwork.com/o/profiles/users/_~01737016f9bf37a62b/" class="button-secondary" target="_blank">UpWork</a>
            <a href="https://www.peopleperhour.com/freelancer/er-faiyaz/php-cakephp-zend-magento-moodle-tot/1016456" class="button-secondary" target="_blank">PeoplePerHour</a>
            <a href="www.linkedin.com/in/er-faiyaz-alam-0704219a" class="button-secondary" target="_blank">LinkedIn</a>
        </p>
       
    </div>
</div>
</div>
