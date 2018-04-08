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
<div class="wrap faulhSettingPage">
    <?php echo Faulh_Template_Helper::head(esc_html__('Network Settings', 'faulh')) ?>
    <form method="post">
        <input type="hidden" name="<?php echo $this->plugin_name . '_network_admin_setting_submit' ?>" >
        <fieldset>
            <div class="mt20">
                <label class="infoBlockUser"> <strong><?php esc_html_e('Block User', 'faulh') ?></strong></label>
                <label for="block_user">
                    <input id="block_user" type="checkbox" <?php checked($this->get_settings('block_user'), 1); ?>  name="block_user" value="<?php echo esc_attr($this->get_settings('block_user')); ?>" size="50" />
                    <?php esc_html_e('User will not be able to login on another blog on the network.', 'faulh'); ?>
                </label>   
            </div>
            <div class="mt20">&nbsp;</div>
            <div class="mt20">
                <label for="block_user_message">
                    <strong><?php esc_html_e('Message for Blocked User', 'faulh'); ?></strong>
                </label>
                <textarea id="block_user_message" name="block_user_message"><?php echo esc_attr($this->get_settings('block_user_message')); ?></textarea>
            </div>
        </fieldset>
         
        <?php wp_nonce_field($this->plugin_name . '_network_admin_setting_nonce', $this->plugin_name . '_network_admin_setting_nonce'); ?>
        <?php submit_button(); ?>
    </form>
</div>