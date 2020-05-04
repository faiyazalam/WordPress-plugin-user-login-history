<?php

use User_Login_History\Inc\Common\Helpers\Template as Template_Helper;
?>
<form name="<?php echo $this->plugin_name . '-search-form'; ?>" method="get" action="" id="<?php echo $this->plugin_name . '-search-form'; ?>">
    <fieldset> 

        <input class="date-inputss" style="width:39%;display: inline-block" readonly type="text" autocomplete="off" placeholder="<?php esc_html_e("From", "faulh") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
        <input class="date-inputss" style="width:39%;display: inline-block" readonly type="text" autocomplete="off" placeholder="<?php esc_html_e("To", "faulh") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
        <select name="date_type" >
            <?php
            Template_Helper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL);
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