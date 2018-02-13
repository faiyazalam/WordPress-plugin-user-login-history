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
<form name="<?php echo $this->plugin_name . '-search-form'; ?>" method="get" action="" id="<?php echo $this->plugin_name . '-search-form'; ?>">
    <fieldset> 
        <input readonly autocomplete="off" placeholder="<?php _e("From", "faulh") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
        <input readonly autocomplete="off" placeholder="<?php _e("To", "faulh") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
        <select  name="date_type" >
            <?php
            Faulh_Template_Helper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL);
            ?>
        </select>
    </fieldset>
    <?php do_action('faulh_public_listing_search_form'); ?>
<?php if($reset_URL) {?>
    <a class=""  href="<?php echo $reset_URL ?>" ><?php _e('RESET', 'faulh'); ?></a>
<?php }?>
        <input class="" id="submit" type="submit" name="submit" value="<?php _e('FILTER', 'faulh') ?>" />
</form>
<div>
    <p>
<?php _e('This table is showing time in the timezone', 'faulh') ?> - <?php echo $timezone ?>
    </p>
    <form method="post" id="<?php echo $this->plugin_name ?>_update_user_timezone">
        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce($this->plugin_name . "_update_user_timezone") ?>">
        <select required="required"  id="select_timezone" name="<?php echo $this->plugin_name . '-timezone' ?>">
            <option value=""><?php _e('Select Timezone', 'faulh') ?></option>
            <?php
            Faulh_Template_Helper::dropdown_timezones($timezone);
            ?>
        </select>
        <input type="submit" name="<?php echo $this->plugin_name . "_update_user_timezone" ?>" value="<?php echo __("Apply", 'faulh') ?>">
    </form>
</div>
<div><?php do_action('faulh_public_before_listing_table') ?></div>
<?php
$Public_List_Table->prepare_items();
$Public_List_Table->display();
?>
<div><?php do_action('faulh_public_after_listing_table') ?></div>
