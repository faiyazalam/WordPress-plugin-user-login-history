<?php

/**
 * Template file to render filter form on the listing table.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials/form
 */

$reset_URI = "admin.php?page=" . esc_attr($_GET['page']);
$reset_URL = is_network_admin() ? network_admin_url($reset_URI) : admin_url($reset_URI); 
?>
<div class="<?php echo $this->plugin_name; ?>-search-filter">
        <form name="<?php echo $this->plugin_name . '-search-form'; ?>" method="get" action="" id="<?php echo $this->plugin_name . '-search-form'; ?>">
            <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']) ?>" />
            <input type="hidden" name="order" value="<?php echo !empty($_GET['order']) ? esc_attr($_GET['order']) : "" ?>" />
            <input type="hidden" name="orderby" value="<?php echo !empty($_GET['orderby']) ? esc_attr($_GET['orderby']) : "" ?>" />
            <fieldset> 
                <input readonly autocomplete="off" placeholder="<?php _e("From", "faulh") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
                <input readonly autocomplete="off" placeholder="<?php _e("To", "faulh") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
                <select  name="date_type" >
                    <?php
                    Faulh_Template_Helper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL);
                    ?>
                </select>
            </fieldset>
            <fieldset>
                <input placeholder="<?php _e("Enter User Id", "faulh") ?>" name="user_id" value="<?php echo isset($_GET['user_id']) ? esc_attr($_GET['user_id']) : "" ?>" >
                <input placeholder="<?php _e("Enter Username", "faulh") ?>" name="username" value="<?php echo isset($_GET['username']) ? esc_attr($_GET['username']) : "" ?>" >
                <input placeholder="<?php _e("Enter Country", "faulh") ?>" name="country_name" value="<?php echo isset($_GET['country_name']) ? esc_attr($_GET['country_name']) : "" ?>" >
                <input placeholder="<?php _e("Enter Browser", "faulh") ?>" name="browser" value="<?php echo isset($_GET['browser']) ? esc_attr($_GET['browser']) : "" ?>" >
                <input placeholder="<?php _e("Enter Operating System", "faulh") ?>" name="operating_system" value="<?php echo isset($_GET['operating_system']) ? esc_attr($_GET['operating_system']) : "" ?>" >
                <input placeholder="<?php _e("Enter IP Address", "faulh") ?>" name="ip_address" value="<?php echo isset($_GET['ip_address']) ? esc_attr($_GET['ip_address']) : "" ?>" >
               <?php if(is_network_admin()) {?>
                <input placeholder="<?php _e("Blog ID", "faulh") ?>" name="blog_id" value="<?php echo isset($_GET['blog_id']) ? esc_attr($_GET['blog_id']) : "" ?>" >
         <?php
         }
                ?>
            </fieldset>
            <fieldset>
                <select  name="timezone">
                    <?php $selected_timezone = isset($_GET['timezone']) ? $_GET['timezone'] : "" ?>
                    <option value=""><?php _e('Select Timezone', 'faulh') ?></option>
                    <option value="unknown" <?php selected($selected_timezone, "unknown"); ?> ><?php _e('Unknown', 'faulh') ?></option>
                    <?php
                    Faulh_Template_Helper::dropdown_timezones($selected_timezone);
                    ?>
                </select>
                <select  name="role">
                    <option value=""><?php _e("Select Current Role", "faulh") ?></option>
                    <?php
                    $selected_role = isset($_GET['role']) ? $_GET['role'] : NULL;
                    wp_dropdown_roles($selected_role);
                    ?>
                    <?php if (is_multisite()) { ?>
                        <option value="superadmin" <?php selected($selected_role, "superadmin"); ?> ><?php _e("Super Administrator", "faulh") ?></option>
                    <?php } ?>
                </select>
                <select   name="old_role">
                    <option value=""><?php _e("Select Old Role", "faulh") ?></option>
                    <?php
                    $selected_old_role = isset($_GET['old_role']) ? $_GET['old_role'] : NULL;
                    wp_dropdown_roles($selected_old_role);
                    ?>
                    <?php if (is_multisite()) { ?>
                        <option value="superadmin" <?php selected($selected_old_role, "superadmin"); ?> ><?php _e("Super Administrator", "faulh") ?></option>  <?php } ?>
                </select>
                <select  name="login_status">
                    <option value=""><?php _e('Select Login Status', 'faulh') ?></option>
                    <?php Faulh_Template_Helper::dropdown_login_statuses(isset($_GET['login_status']) ? $_GET['login_status'] : ""); ?>
                </select>
            </fieldset>
            <?php do_action('faulh_admin_listing_search_form'); ?>
            <a class="" id="download_csv_link"><?php _e('DOWNLOAD CSV', 'faulh'); ?></a> 
            <a class=""  href="<?php echo $reset_URL ?>" ><?php _e('RESET', 'faulh'); ?></a>
            <input type="hidden" name="<?php echo $this->plugin_name?>_export_csv" id="export-csv" value="">
           <input type="hidden" name="<?php echo $this->plugin_name?>_export_nonce" id="<?php echo $this->plugin_name?>_export_nonce" value="<?php echo wp_create_nonce($this->plugin_name . '_export_csv') ?>">
            <input class="" id="submit" type="submit" name="submit" value="<?php _e('FILTER', 'faulh') ?>" />
        </form>
        <br class="clear">
    </div>