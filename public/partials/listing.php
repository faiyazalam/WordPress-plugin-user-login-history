<?php
/**
 * Template file to render listing table for public.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/public/partials
 */
?>
<div class="<?php echo $this->plugin_name . '-wrapper'; ?>">
   <?php do_action('faulh_public_before_search_form') ?>
<?php echo !empty($attributes['title']) ?  "<div class='".$this->plugin_name."-listing_title'>".$attributes['title']."</div>": ""?>
<form name="<?php echo $this->plugin_name . '-search-form'; ?>" method="get" action="" id="<?php echo $this->plugin_name . '-search-form'; ?>">
    <fieldset> 

        <input style="width:39%;display: inline-block" readonly type="text" autocomplete="off" placeholder="<?php esc_html_e("From", "faulh") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
        <input style="width:39%;display: inline-block" readonly type="text" autocomplete="off" placeholder="<?php esc_html_e("To", "faulh") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
        <select name="date_type" >
            <?php
            Faulh_Template_Helper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL);
            ?>
        </select>

        <div style="margin-top:20px; text-align: right">
 <?php if ($reset_URL) { ?>
            <a style="margin-right:10px" href="<?php echo $reset_URL ?>" ><?php esc_html_e('RESET', 'faulh'); ?></a>
                <?php } ?>
                <input class="" id="submit" type="submit" name="submit" value="<?php esc_html_e('FILTER', 'faulh') ?>" />
        </div>
        

    </fieldset>
    <?php do_action('faulh_public_listing_search_form'); ?>
</form>
<?php do_action('faulh_public_after_search_form') ?>
    <hr>
<div>
    <?php
    if (!empty($attributes['show_timezone_selector']) && "true" == $attributes['show_timezone_selector']) {
        ?>
      
        <form method="post" id="<?php echo $this->plugin_name ?>_update_user_timezone">
            <fieldset>
            <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce($this->plugin_name . "_update_user_timezone") ?>">
            <p><?php esc_html_e('This table is showing time in the timezone', 'faulh') ?> - <strong><?php echo $Public_List_Table->get_table_timezone() ?></strong></p>
            <select style="width:78%" required="required"  id="select_timezone" name="<?php echo $this->plugin_name . '-timezone' ?>">
                <option value=""><?php esc_html_e('Select Timezone', 'faulh') ?></option>
                <?php
                Faulh_Template_Helper::dropdown_timezones($Public_List_Table->get_table_timezone());
                ?>
            </select>
<input type="submit" name="<?php echo $this->plugin_name . "_update_user_timezone" ?>" value="<?php echo esc_html__("Apply", 'faulh') ?>">

      </fieldset>
            </form>
        <?php
    }
    ?>
</div>
<?php do_action('faulh_public_before_listing_table') ?>
<?php
$Public_List_Table->display();
?>
<?php do_action('faulh_public_after_listing_table') ?>
</div>