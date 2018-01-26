<div class="wrap">
    <h2><?php _e('User Login History', 'user-login-history') ?></h2>
    <div class="<?php echo $this->plugin_name; ?>-search-filter">
            <form name="<?php echo $this->plugin_name . '-search-form'; ?>" method="get" action="" id="<?php echo $this->plugin_name . '-search-form'; ?>">
                <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']) ?>" />
                <fieldset> 
                    <input readonly autocomplete="off" placeholder="<?php _e("From", "user-login-history") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
                    <input readonly autocomplete="off" placeholder="<?php _e("To", "user-login-history") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
                    <select  name="date_type" >
                        <?php
                        User_Login_History_Template_Helper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL);
                        ?>
                    </select></fieldset>
                <fieldset> <input placeholder="<?php _e("Enter User Id", "user-login-history") ?>" name="user_id" value="<?php echo isset($_GET['user_id']) ? esc_attr($_GET['user_id']) : "" ?>" >
                    <input placeholder="<?php _e("Enter Username", "user-login-history") ?>" name="username" value="<?php echo isset($_GET['username']) ? esc_attr($_GET['username']) : "" ?>" >
                    <input placeholder="<?php _e("Enter Country", "user-login-history") ?>" name="country_name" value="<?php echo isset($_GET['country_name']) ? esc_attr($_GET['country_name']) : "" ?>" >
                    <input placeholder="<?php _e("Enter Browser", "user-login-history") ?>" name="browser" value="<?php echo isset($_GET['browser']) ? esc_attr($_GET['browser']) : "" ?>" >
                    <input placeholder="<?php _e("Enter Operating System", "user-login-history") ?>" name="operating_system" value="<?php echo isset($_GET['operating_system']) ? esc_attr($_GET['operating_system']) : "" ?>" >
                    <input placeholder="<?php _e("Enter IP Address", "user-login-history") ?>" name="ip_address" value="<?php echo isset($_GET['ip_address']) ? esc_attr($_GET['ip_address']) : "" ?>" >
                </fieldset>
                <fieldset>
                    <select  name="timezone">
                        <?php $selected_timezone = isset($_GET['timezone']) ? $_GET['timezone'] : "" ?>
                        <option value=""><?php _e('Select Timezone', 'user-login-history') ?></option>
                        <option value="unknown" <?php selected($selected_timezone, "unknown"); ?> ><?php _e('Unknown', 'user-login-history') ?></option>
                        <?php
                                                User_Login_History_Template_Helper::dropdown_timezone($selected_timezone);
                        ?>
                    </select>
                    <select  name="role">
                        <option value=""><?php _e("Select Current Role", "user-login-history") ?></option>
                        <?php
                        $selected_role = isset($_GET['role']) ? $_GET['role'] : NULL;
                        wp_dropdown_roles($selected_role);
                        ?>
                        <?php if (is_multisite()) { ?>
                            <option value="superadmin" <?php selected($selected_role, "superadmin"); ?> ><?php _e("Super Administrator", "user-login-history") ?></option>
                        <?php } ?>
                    </select>
                    <select   name="old_role">
                        <option value=""><?php _e("Select Old Role", "user-login-history") ?></option>
                        <?php
                        $selected_old_role = isset($_GET['old_role']) ? $_GET['old_role'] : NULL;
                        wp_dropdown_roles($selected_old_role);
                        ?>
                        <?php if (is_multisite()) { ?>
                            <option value="superadmin" <?php selected($selected_old_role, "superadmin"); ?> ><?php _e("Super Administrator", "user-login-history") ?></option>  <?php } ?>
                    </select>
                    <select  name="login_status">
                        <option value=""><?php _e('Select Login Status', 'user-login-history') ?></option>
                        <option value="success" <?php selected(isset($_GET['login_status']) ? $_GET['login_status'] : "", "success"); ?> ><?php _e('Login Success', 'user-login-history') ?></option>
                        <option value="logout" <?php selected(isset($_GET['login_status']) ? $_GET['login_status'] : "", "logout"); ?> ><?php _e('Logout', 'user-login-history') ?></option>
                        <option value="fail" <?php selected(isset($_GET['login_status']) ? $_GET['login_status'] : "", "fail"); ?> ><?php _e('Login Fail', 'user-login-history') ?></option>
                    </select>
                </fieldset>
                <?php do_action('user_login_history_admin_listing_search_form');  ?>
                <a class="" href="<?php echo wp_nonce_url('admin.php?page='. esc_attr($_GET['page']).'&delete_all=1', USER_LOGIN_HISTORY_OPTION_PREFIX . 'delete_all_admin_records') ?>" id="delete_all_user_login_history"><?php _e('DELETE ALL RECORDS', 'user-login-history'); ?></a> 
                <a class="" id="download_csv_button"><?php _e('DOWNLOAD CSV', 'user-login-history'); ?></a> 
                <a class=""  href="<?php echo admin_url("admin.php?page=".esc_attr($_GET['page'])) ?>" ><?php _e('RESET', 'user-login-history'); ?></a>
                <input type="hidden" name="export-user-login-history" id="export-user-login-history" value="">
                <input type="hidden" name="export-user-login-history-nonce" id="export-user-login-history-nonce" value="<?php echo wp_create_nonce(USER_LOGIN_HISTORY_OPTION_PREFIX . 'export_csv') ?>">
                <input class="" id="submit" type="submit" name="submit" value="<?php _e('FILTER', 'user-login-history') ?>" />
            </form>
             <br class="clear">
        </div>
    <div>
        <form method="post">
            <input type="hidden" name="<?php echo $this->plugin_name.'-admin-listing-table'?>" value="">
            <?php
            $this->admin_list_table->prepare_items();
            $this->admin_list_table->display();
            ?>
        </form>
        <br class="clear">
    </div>
</div>
