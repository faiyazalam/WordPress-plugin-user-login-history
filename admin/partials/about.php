<?php
/**
 * Template file to render about page for admin.
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
$uri_help_page = "admin.php?page={$this->plugin_name}-help";
$url_help_page = is_network_admin() ? network_admin_url($uri_help_page): admin_url($uri_help_page);
?>
<div class="wrap">
    <?php Faulh_Template_Helper::head(); ?>
    <div class="clearfix">
        <div class="faulh-submenu-page-<?php echo!empty($_GET['page']) ? esc_attr($_GET['page']) : "" ?>">
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
                <li><strong><?php esc_html_e('Old Role', 'faulh'); ?></strong> - <?php esc_html_e('The role while user gets logged in into your website.', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Browser', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('Operating System', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('IP Address', 'faulh'); ?></strong></li>
                <li><strong><?php esc_html_e('Country Name and Country Code', 'faulh'); ?></strong> - <?php esc_html_e('Based on IP Address', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Timezone', 'faulh'); ?></strong> - <?php esc_html_e('Based on IP Address', 'faulh'); ?></li>
            </ol>
               <hr>
            <h2><?php esc_html_e('Some More Useful Features:', 'faulh'); ?></h2>
            <ol>
                <li><strong><?php esc_html_e('Preferable Timezone', 'faulh') ?></strong> - <?php esc_html_e('You can select your preferred timezone to be used for the listing table.', 'faulh'); ?></li>
                <li><strong><?php esc_html_e('Shortcode', 'faulh') ?></strong> - <?php echo sprintf(esc_html__('The plugin comes with a customizable shortcode that you can use in your template or content to view the login history of current logged in user. For more detail, please see the %1$sHelp%2$s page.', 'faulh'), "<a href='" . $url_help_page . "'>", "</a>"); ?></li>
                <li><strong><?php esc_html_e('Multisite Network (Since version 1.7.0)', 'faulh') ?></strong> - <?php esc_html_e('On the network admin area, you can see the listing table which shows all the records fetched from all the blogs of the current network.', 'faulh'); ?><br> 
                </li>
                <li><strong><?php esc_html_e('Advanced Search Filter', 'faulh') ?></strong></li>
                <li><strong><?php esc_html_e('CSV Export', 'faulh') ?></strong></li>
            </ol>
               <hr>
            <h2><?php esc_html_e('Translations', 'faulh'); ?></h2>
            <p><?php esc_html_e('Currently, this plugin is available in two languages i.e. English and Italian.', 'faulh') ?></p>
            <p><a href="https://translate.wordpress.org/projects/wp-plugins/user-login-history" class="button-secondary" target="_blank">
                    <?php esc_html_e('Download Language Files', 'faulh'); ?>
                </a></p>
            <p><strong><?php esc_html_e('Do you want to translate this plugin to another language?', 'faulh') ?></strong></p>
            <p><?php echo sprintf(esc_html__('I recommend using %1$sPOEdit%2$s or if you prefer to do it straight from the WordPress admin interface use %3$sLoco Translate%4$s.
When you’re done, send us the file(s) and I’ll add it to the official plugin. You can also translate the plugin %5$sOnline%6$s.', 'faulh'), "<a href='http://poedit.net/' target='_blank'>", "</a>", "<a href='https://wordpress.org/plugins/loco-translate/' target='_blank'>", "</a>", "<a href='https://translate.wordpress.org/projects/wp-plugins/user-login-history' target='_blank'>", "</a>") ?>
            </p>
   <hr>
            <h2><?php esc_html_e('I Am Here!', 'faulh'); ?></h2>
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
               <hr>    
            <h2><?php esc_html_e('Like My Work?', 'faulh'); ?></h2>
                <p>
                    <?php Faulh_Template_Helper::create_button('https://wordpress.org/support/plugin/user-login-history/reviews/', esc_html__('Submit Your Review', 'faulh')) ?> 
                    <?php Faulh_Template_Helper::create_button($paypal_link['url'], esc_html__('Donate', 'faulh')) ?> 
                </p>
            <?php } ?>
        </div>
    </div>
</div>