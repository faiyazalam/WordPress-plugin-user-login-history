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
<div>
    <?php
     if (!empty($attributes['title'])) {
        echo  "<h1>".esc_html($attributes['title'])."</h2>";
     }
    ?>
</div>
<form name="<?php echo $this->plugin_name . '-search-form'; ?>" method="get" action="" id="<?php echo $this->plugin_name . '-search-form'; ?>">
    <fieldset> 
        <input readonly autocomplete="off" placeholder="<?php esc_html_e("From", "faulh") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
        <input readonly autocomplete="off" placeholder="<?php esc_html_e("To", "faulh") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
        <select  name="date_type" >
            <?php
            Faulh_Template_Helper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL);
            ?>
        </select>
    </fieldset>
    <?php do_action('faulh_public_listing_search_form'); ?>
<?php if($reset_URL) {?>
    <a class=""  href="<?php echo $reset_URL ?>" ><?php esc_html_e('RESET', 'faulh'); ?></a>
<?php }?>
        <input class="" id="submit" type="submit" name="submit" value="<?php esc_html_e('FILTER', 'faulh') ?>" />
</form>
<div>
    <p>
<?php esc_html_e('This table is showing time in the timezone', 'faulh') ?> - <?php echo $Public_List_Table->get_table_timezone() ?>
    </p>
    <form method="post" id="<?php echo $this->plugin_name ?>_update_user_timezone">
        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce($this->plugin_name . "_update_user_timezone") ?>">
        <select required="required"  id="select_timezone" name="<?php echo $this->plugin_name . '-timezone' ?>">
            <option value=""><?php esc_html_e('Select Timezone', 'faulh') ?></option>
            <?php
            Faulh_Template_Helper::dropdown_timezones($Public_List_Table->get_table_timezone());
            ?>
        </select>
        <input type="submit" name="<?php echo $this->plugin_name . "_update_user_timezone" ?>" value="<?php echo esc_html__("Apply", 'faulh') ?>">
    </form>
</div>
<div><?php do_action('faulh_public_before_listing_table') ?></div>
<?php
$Public_List_Table->prepare_items();
$Public_List_Table->display();
?>
<div><?php do_action('faulh_public_after_listing_table') ?></div>
