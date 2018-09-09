<?php

use User_Login_History\Inc\Common\Helpers\TemplateHelper;
?>
<form id="filter_form" method="get">
    <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']) ?>" />
    <input type="hidden" name="csv" id="csv" value="">
    <input type="hidden" name="_wpnonce" id="csv_nonce" value="<?php echo wp_create_nonce('csv_nonce') ?>">
    <div class="basic_search">
        <input readonly autocomplete="off" placeholder="<?php esc_html_e("From", $this->plugin_text_domain) ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
        <input readonly autocomplete="off" placeholder="<?php esc_html_e("To", $this->plugin_text_domain) ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
        <select  name="date_type" >
            <?php TemplateHelper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL); ?>
        </select>
    </div>
    <div><a id="show_hide_advanced_search" href="#"><?php esc_html_e('Show Advanced Filters', $this->plugin_text_domain) ?></a></div>
    <div id="advanced_search">
        <div><label for="user_id"><?php esc_html_e("User ID", $this->plugin_text_domain) ?></label><input id="user_id" min="1" type="number" placeholder="<?php esc_html_e("Enter User ID", $this->plugin_text_domain) ?>" name="user_id" value="<?php echo isset($_GET['user_id']) ? esc_attr($_GET['user_id']) : "" ?>" ></div>
        <div><label for="username"><?php esc_html_e("Username", $this->plugin_text_domain) ?></label><input id="username" placeholder="<?php esc_html_e("Enter Username", $this->plugin_text_domain) ?>" name="username" value="<?php echo isset($_GET['username']) ? esc_attr($_GET['username']) : "" ?>" ></div>
        <div><label for="country" ><?php esc_html_e("Country", $this->plugin_text_domain) ?></label><input id="country" placeholder="<?php esc_html_e("Enter Country", $this->plugin_text_domain) ?>" name="country_name" value="<?php echo isset($_GET['country_name']) ? esc_attr($_GET['country_name']) : "" ?>" ></div>
        <div><label for="browser" ><?php esc_html_e("Browser", $this->plugin_text_domain) ?></label><input id="browser" placeholder="<?php esc_html_e("Enter Browser", $this->plugin_text_domain) ?>" name="browser" value="<?php echo isset($_GET['browser']) ? esc_attr($_GET['browser']) : "" ?>" ></div>
        <div><label for="operating_system" ><?php esc_html_e("Operating System", $this->plugin_text_domain) ?></label><input  id="operating_system" placeholder="<?php esc_html_e("Enter Operating System", $this->plugin_text_domain) ?>" name="operating_system" value="<?php echo isset($_GET['operating_system']) ? esc_attr($_GET['operating_system']) : "" ?>" ></div>
        <div><label for="ip_address" ><?php esc_html_e("IP Address", $this->plugin_text_domain) ?></label><input id="ip_address" placeholder="<?php esc_html_e("Enter IP Address", $this->plugin_text_domain) ?>" name="ip_address" value="<?php echo isset($_GET['ip_address']) ? esc_attr($_GET['ip_address']) : "" ?>" ></div>
        <?php if (is_network_admin()) { ?>
            <div><label for="blog_id" ><?php esc_html_e("Blog ID", $this->plugin_text_domain) ?></label><input id="blog_id" placeholder="<?php esc_html_e("Blog ID", $this->plugin_text_domain) ?>" name="blog_id" value="<?php echo isset($_GET['blog_id']) ? esc_attr($_GET['blog_id']) : "" ?>" ></div>
            <?php
        }
        ?>
        <div><label for="timezone" ><?php esc_html_e("Timezone", $this->plugin_text_domain) ?></label><select id="timezone"  name="timezone">
                <?php $selected_timezone = isset($_GET['timezone']) ? $_GET['timezone'] : "" ?>
                <option value=""><?php esc_html_e('Select Timezone', $this->plugin_text_domain) ?></option>
                <option value="unknown" <?php selected($selected_timezone, "unknown"); ?> ><?php esc_html_e('Unknown', $this->plugin_text_domain) ?></option>
                <?php TemplateHelper::dropdown_timezones($selected_timezone); ?>
            </select></div>
        <div><label for="role" ><?php esc_html_e("Current Role", $this->plugin_text_domain) ?></label><select id="role"  name="role">
                <option value=""><?php esc_html_e("Select Current Role", $this->plugin_text_domain) ?></option>
                <?php
                $selected_role = isset($_GET['role']) ? $_GET['role'] : NULL;
                wp_dropdown_roles($selected_role);
                ?>
                <?php if (is_network_admin()) { ?>
                    <option value="superadmin" <?php selected($selected_role, "superadmin"); ?> ><?php esc_html_e("Super Administrator", $this->plugin_text_domain) ?></option>
                <?php } ?>
            </select></div>
        <div><label for="old_role" ><?php esc_html_e("Old Role", $this->plugin_text_domain) ?></label><select id="old_role"  name="old_role">
                <option value=""><?php esc_html_e("Select Old Role", $this->plugin_text_domain) ?></option>
                <?php
                $selected_old_role = isset($_GET['old_role']) ? $_GET['old_role'] : NULL;
                wp_dropdown_roles($selected_old_role);
                ?>
                <?php if (is_network_admin()) { ?>
                    <option value="superadmin" <?php selected($selected_old_role, "superadmin"); ?> ><?php esc_html_e("Super Administrator", $this->plugin_text_domain) ?></option>  <?php } ?>
            </select></div>
        <div><label for="login_status" ><?php esc_html_e("Login Status", $this->plugin_text_domain) ?></label><select id="login_status" name="login_status">
                <option value=""><?php esc_html_e('Select Login Status', $this->plugin_text_domain) ?></option>
                <?php $selected_login_status = isset($_GET['login_status']) ? $_GET['login_status'] : "" ?>
                <option value="unknown" <?php selected($selected_login_status, "unknown"); ?> ><?php esc_html_e('Unknown', $this->plugin_text_domain) ?></option>
                <?php TemplateHelper::dropdown_login_statuses($selected_login_status); ?>
            </select></div>
        <?php if (is_network_admin()) { ?>
            <div><label for="is_super_admin" ><?php esc_html_e("Super Admin", $this->plugin_text_domain) ?></label>
                <select id="is_super_admin" name="is_super_admin" >
                    <option value=""><?php esc_html_e('Select Super Admin', $this->plugin_text_domain) ?></option>

                    <?php
                    TemplateHelper::dropdown_is_super_admin(isset($_GET['is_super_admin']) ? $_GET['is_super_admin'] : NULL);
                    ?>
                </select>
            </div>
        <?php } ?>

    </div>

    <fieldset>
        <a href="<?php echo esc_url("admin.php?page=" . $_GET['page']); ?>"><?php esc_html_e('CANCEL', $this->plugin_text_domain) ?></a>
        <input id="submit" type="submit" name="submit" value="<?php esc_html_e('FILTER', $this->plugin_text_domain) ?>" />
        <a id="download_csv_link" href="#"><?php esc_html_e('DOWNLOAD CSV', $this->plugin_text_domain); ?></a> 
    </fieldset>

</form>