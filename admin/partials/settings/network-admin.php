<?php

/**
 * Template file to render setting page for network admin.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials/settings
 */
?>
<div class="wrap">
    <h2><?php _e('Network Settings', 'faulh') ?> - <?php echo Faulh_Template_Helper::plugin_name() ?></h2>
    <form method="post">
        <input type="hidden" name="<?php echo $this->plugin_name . '_network_admin_setting_submit' ?>" >
        <fieldset>
            <div>
                <label for="block_user">
                    <?php _e('Block User', 'faulh'); ?>
                </label>
                <input id="block_user" type="checkbox" <?php checked($this->get_settings('block_user'), 1); ?>  name="block_user" value="<?php echo esc_attr($this->get_settings('block_user')); ?>" size="50" />
                <p><?php _e('User will not be able to login on another blog on the network.', 'faulh') ?></p>
            </div>
            <div>
                <label for="block_user_message">
                    <?php _e('Message for Blocked User', 'faulh'); ?>
                </label>
                <textarea id="block_user_message" name="block_user_message"><?php echo esc_attr($this->get_settings('block_user_message')); ?></textarea>
            </div>
        </fieldset>
        <?php wp_nonce_field($this->plugin_name . '_network_admin_setting_nonce', $this->plugin_name . '_network_admin_setting_nonce'); ?>
        <?php submit_button(); ?>
    </form>
</div>