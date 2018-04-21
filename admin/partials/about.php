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
<?php
$author_links = Faulh_Template_Helper::plugin_author_links();
$paypal_key = 'paypal';
$paypal_link = $author_links[$paypal_key];
unset($author_links[$paypal_key]);
?>
<div class="wrap">
    <?php Faulh_Template_Helper::head(); ?>
    <div class="clearfix">
        <div class="faulh-submenu-page-<?php echo!empty($_GET['page']) ? $_GET['page'] : "" ?>">
            <h2><?php esc_html_e('The plugin helps you to track any visitor\'s login details with the following attributes:', 'faulh'); ?></h2>
            <ol>
                <li><strong><?php esc_html_e('Login', 'faulh'); ?></strong> - <?php esc_html_e('Login Date-Time', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Logout', 'faulh'); ?></strong> - <?php esc_html_e('Logout Date-Time', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Last Seen', 'faulh'); ?></strong> - <?php esc_html_e('Last Seen Date-Time', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Login Status', 'faulh'); ?></strong> - <?php esc_html_e('Logged in/Logged out/Failed/Blocked', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Online Status', 'faulh'); ?></strong> - <?php esc_html_e('Online/Offline/Idle', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Session Duration', 'faulh'); ?></strong> - <?php esc_html_e('How long the user stayed on your website per session.', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('User ID', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('Username', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('Current Role', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('Old Role', 'faulh'); ?></strong> - <?php esc_html_e('The role while user gets logged-in into your website.', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Browser', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('Operating System', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('IP Address', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('Country Name and Country Code', 'faulh'); ?></strong> - <?php esc_html_e('(Based on IP Address)', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Timezone (Based on IP Address)', 'faulh'); ?></strong> - <?php esc_html_e('(Based on IP Address)', 'faulh'); ?></li>
            </ol>
            <h2><?php esc_html_e('Some More Useful Features:', 'faulh'); ?></h2>
            <ol>
                <li><?php esc_html_e('Preferable Timezone - You can select your preferred timezone to be used for the listing table.', 'faulh'); ?></li>
                <li><?php esc_html_e('Shortcode - To see the listing table on front-end for the current logged-in user, you can use the following shortcode in your template file.', 'faulh'); ?><br> <pre><code>&lt;?php echo do_shortcode('[user_login_history]'); ?&gt;</code></pre></li>
                <li><?php esc_html_e('Multisite Network (Since version 1.7) - On the network admin area, you can see the listing table which shows all the records fetched from all the blogs of the current network.', 'faulh'); ?><br> 
                </li>
                <li><?php esc_html_e('Advanced Search Filter', 'faulh'); ?></li>
                <li><?php esc_html_e('CSV Export', 'faulh'); ?></li>
            </ol>
            <h2><?php esc_html_e('Translations', 'faulh'); ?></h2>
            <p><?php esc_html_e('Currently, this plugin is available in two languages i.e. English and Italian.', 'faulh') ?></p>
            <p><a href="https://translate.wordpress.org/projects/wp-plugins/user-login-history" class="button-secondary" target="_blank">
                    <?php esc_html_e('Download Language Files', 'faulh'); ?>
                </a></p>
            <h2><?php esc_html_e('I am here!', 'faulh'); ?></h2>
            <p><strong><?php esc_html_e('You can reach me through the following links:', 'faulh'); ?></strong></p>
            <p>
                <?php
                foreach ($author_links as $key => $link) {
                    if (empty($link['url']) || empty($link['label'])) {
                        continue;
                    }
                    ?>
                    <?php Faulh_Template_Helper::create_button($link['url'], $link['label']) ?> 
                <?php }
                ?>
            </p>
            <?php if (!empty($paypal_link['url']) && !empty($paypal_link['label'])) { ?>
                <h2><?php esc_html_e('Like My Work?', 'faulh'); ?></h2>
                <p>
                    <?php Faulh_Template_Helper::create_button('https://wordpress.org/support/plugin/user-login-history/reviews/', esc_html__('Submit Your Review', 'faulh')) ?> 
                    <?php Faulh_Template_Helper::create_button($paypal_link['url'], esc_html__('Donate', 'faulh')) ?> 
                </p>
            <?php } ?>
        </div>
    </div>
</div>