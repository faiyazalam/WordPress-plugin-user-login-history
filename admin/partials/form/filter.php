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
        <input type="hidden" name="order" value="<?php echo!empty($_GET['order']) ? esc_attr($_GET['order']) : "" ?>" />
        <input type="hidden" name="orderby" value="<?php echo!empty($_GET['orderby']) ? esc_attr($_GET['orderby']) : "" ?>" />
        <div class="secondRow">
            <div><input readonly autocomplete="off" placeholder="<?php esc_html_e("From", "faulh") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" ></div>
            <div><input readonly autocomplete="off" placeholder="<?php esc_html_e("To", "faulh") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" ></div>
            <div> <select  name="date_type" >
                    <?php Faulh_Template_Helper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL); ?>
                </select></div>
        </div>
        <div class="secondRow">
            <div><input type="number" placeholder="<?php esc_html_e("Enter User ID", "faulh") ?>" name="user_id" value="<?php echo isset($_GET['user_id']) ? esc_attr($_GET['user_id']) : "" ?>" ></div>
            <div><input placeholder="<?php esc_html_e("Enter Username", "faulh") ?>" name="username" value="<?php echo isset($_GET['username']) ? esc_attr($_GET['username']) : "" ?>" ></div>
            <div><input placeholder="<?php esc_html_e("Enter Country", "faulh") ?>" name="country_name" value="<?php echo isset($_GET['country_name']) ? esc_attr($_GET['country_name']) : "" ?>" ></div>
            <div><input placeholder="<?php esc_html_e("Enter Browser", "faulh") ?>" name="browser" value="<?php echo isset($_GET['browser']) ? esc_attr($_GET['browser']) : "" ?>" ></div>
            <div><input placeholder="<?php esc_html_e("Enter Operating System", "faulh") ?>" name="operating_system" value="<?php echo isset($_GET['operating_system']) ? esc_attr($_GET['operating_system']) : "" ?>" ></div>
            <div><input placeholder="<?php esc_html_e("Enter IP Address", "faulh") ?>" name="ip_address" value="<?php echo isset($_GET['ip_address']) ? esc_attr($_GET['ip_address']) : "" ?>" ></div>
            <?php if (is_network_admin()) { ?>
                <div><input placeholder="<?php esc_html_e("Blog ID", "faulh") ?>" name="blog_id" value="<?php echo isset($_GET['blog_id']) ? esc_attr($_GET['blog_id']) : "" ?>" ></div>

                <?php
            }
            ?>
        </div>

        <div class="secondRow">
            <div> <select  name="timezone">
                    <?php $selected_timezone = isset($_GET['timezone']) ? $_GET['timezone'] : "" ?>
                    <option value=""><?php esc_html_e('Select Timezone', 'faulh') ?></option>
                    <option value="unknown" <?php selected($selected_timezone, "unknown"); ?> ><?php esc_html_e('Unknown', 'faulh') ?></option>
                    <?php Faulh_Template_Helper::dropdown_timezones($selected_timezone); ?>
                </select></div>
            <div><select  name="role">
                    <option value=""><?php esc_html_e("Select Current Role", "faulh") ?></option>
                    <?php
                    $selected_role = isset($_GET['role']) ? $_GET['role'] : NULL;
                    wp_dropdown_roles($selected_role);
                    ?>
                    <?php if (is_network_admin()) { ?>
                        <option value="superadmin" <?php selected($selected_role, "superadmin"); ?> ><?php esc_html_e("Super Administrator", "faulh") ?></option>
                    <?php } ?>
                </select></div>
            <div> <select   name="old_role">
                    <option value=""><?php esc_html_e("Select Old Role", "faulh") ?></option>
                    <?php
                    $selected_old_role = isset($_GET['old_role']) ? $_GET['old_role'] : NULL;
                    wp_dropdown_roles($selected_old_role);
                    ?>
                    <?php if (is_network_admin()) { ?>
                        <option value="superadmin" <?php selected($selected_old_role, "superadmin"); ?> ><?php esc_html_e("Super Administrator", "faulh") ?></option>  <?php } ?>
                </select></div>
            <div> <select  name="login_status">
                    <option value=""><?php esc_html_e('Select Login Status', 'faulh') ?></option>
                    <?php $selected_login_status = isset($_GET['login_status']) ? $_GET['login_status'] : "" ?>
                       <option value="unknown" <?php selected($selected_login_status, "unknown"); ?> ><?php esc_html_e('Unknown', 'faulh') ?></option>
                    <?php Faulh_Template_Helper::dropdown_login_statuses($selected_login_status); ?>
                </select></div>
            <?php if (is_network_admin()) { ?>
                <div>
                    <select  name="is_super_admin" >
                        <option value=""><?php esc_html_e('Select Super Admin', 'faulh') ?></option>

                        <?php
                        Faulh_Template_Helper::dropdown_is_super_admin(isset($_GET['is_super_admin']) ? $_GET['is_super_admin'] : NULL);
                        ?>
                    </select>
                </div>
            <?php } ?>

        </div>
        <?php do_action('faulh_admin_listing_search_form'); ?>
        <div class="submitAction" >
            <a class="faulhbtn faulh-btn-primary" id="download_csv_link"><?php esc_html_e('DOWNLOAD CSV', 'faulh'); ?></a> 

            <div id="publishing-action">
                <a class="faulhbtn action"  href="<?php echo $reset_URL ?>" ><?php esc_html_e('RESET', 'faulh'); ?></a>
                <input type="hidden" name="<?php echo $this->plugin_name ?>_export_csv" id="export-csv" value="">
                <input type="hidden" name="<?php echo $this->plugin_name ?>_export_nonce" id="<?php echo $this->plugin_name ?>_export_nonce" value="<?php echo wp_create_nonce($this->plugin_name . '_export_csv') ?>">
                <input class="faulhbtn faulh-btn-primary" id="submit" type="submit" name="submit" value="<?php esc_html_e('FILTER', 'faulh') ?>" />
            </div>
        </div>
    </form>
    <br class="clear">
</div> 