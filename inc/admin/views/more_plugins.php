<?php
if (! defined('ABSPATH')) exit;
?>
<style>
    .plugin-list {
        display: flex;
        flex-direction: column;
    }
    .plugin-item {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        background: #f9f9f9;
    }
    .plugin-item h2 {
        margin-top: 0;
    }
    .button {
        margin-right: 10px;
    }
</style>
<div class="wrap">
    <h1><?php echo esc_html__('More Useful Plugins', 'user-login-history'); ?></h1>
    <div class="plugin-list">
        <div class="plugin-item">
            <h2><?php echo esc_html__('Countera - Track post view count by user and date for better insights.', 'user-login-history'); ?></h2>
            <p><?php echo esc_html__('Countera is a lightweight and efficient plugin that helps you track and display the number of views each of your posts receives.', 'user-login-history'); ?></p>
            <a target="_blank" href="<?php echo esc_url('https://wordpress.org/plugins/countera/'); ?>" class="button button-primary"><?php esc_html_e('Free Download', 'user-login-history'); ?></a>
        </div>       
    </div>
</div>
