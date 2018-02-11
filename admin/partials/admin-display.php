<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials
 * @author     Er Faiyaz Alam
 */
?>
<div class="wrap">
    <h1><?php _e('Settings :: User Login History', 'user-login-history');?></h1>
    <?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/about/about-author.php'; ?>
    <?php require_once plugin_dir_path(dirname(__FILE__)) . 'partials/about/plugin-notice.php'; ?>
    <h2 class="nav-tab-wrapper">
        <?php
        $tabs = array(
            'backend' => __('Backend Options', 'user-login-history'),
            'frontend' => __('Frontend Options', 'user-login-history'),
            'help' => __('Help', 'user-login-history'),
            'about' => __('About', 'user-login-history'),
        
        );
        //set current tab
        $tab = ( isset($_GET['tab']) ? $_GET['tab'] : 'backend' );
        ?>
        <?php foreach ($tabs as $key => $value): ?>
            <a class="nav-tab <?php
            if ($tab == $key) {
                echo 'nav-tab-active';
            }
            ?>" href="<?php echo admin_url() ?>options-general.php?page=user-login-history-settings&tab=<?php echo $key; ?>"><?php echo $value; ?></a>
           <?php endforeach; ?>
    </h2>
    <div class="user-login-history-tabs">
         <?php if ($tab == 'backend'): ?>
            <?php include plugin_dir_path(dirname(__FILE__)) . 'partials/backend/timezone-setting.php'; ?>     
        <?php elseif ($tab == 'frontend'): ?>
            <form method="post" action="options.php">
                <?php settings_fields('frontend-fields-user-login-history'); ?>
                <?php do_settings_sections('frontend-fields-user-login-history'); ?>
                <?php  submit_button(); ?>
            </form>
        <?php elseif ($tab == 'help'): ?>
            <?php include plugin_dir_path(dirname(__FILE__)) . 'partials/help/help.php'; ?>
        <?php else: ?>
            <?php include plugin_dir_path(dirname(__FILE__)) . 'partials/about/about.php'; ?>
        <?php endif; ?>
    </div>
</div>