<?php
global $current_user;
$user_timezone = get_user_meta($current_user->ID, USER_LOGIN_HISTORY_USER_META_PREFIX . "user_timezone", TRUE);
?>
<form name="<?php echo $this->plugin_name . '-search-form'; ?>" method="get" action="" id="<?php echo $this->plugin_name . '-search-form'; ?>">
    <fieldset> 
        <input readonly autocomplete="off" placeholder="<?php _e("From", "user-login-history") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
        <input readonly autocomplete="off" placeholder="<?php _e("To", "user-login-history") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
        <select  name="date_type" >
            <?php
            User_Login_History_Template_Helper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL);
            ?>
        </select>
    </fieldset>
    <?php do_action('user_login_history_public_listing_search_form'); ?>
    <input class="" id="submit" type="submit" name="submit" value="<?php _e('FILTER', 'user-login-history') ?>" />
</form>
<div>
    <p>
<?php _e('This table is showing time in the timezone', 'user-login-history') ?> - <?php echo $user_timezone ? $user_timezone : User_Login_History_Date_Time_Helper::DEFAULT_TIMEZONE ?>
    </p> 
    
    <form method="post" id="<?php echo $this->plugin_name ?>_update_user_timezone">
        <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce($this->plugin_name . "_update_user_timezone") ?>">
        <select  id="select_timezone" name="<?php echo $this->plugin_name . '-timezone' ?>">
            <option value=""><?php _e('Select Timezone', 'user-login-history') ?></option>
            <?php
            User_Login_History_Template_Helper::dropdown_timezone($user_timezone);
            ?>
        </select>
        <input type="submit" name="<?php echo $this->plugin_name . "_update_user_timezone" ?>" value="<?php echo __("Apply", 'user-login-history') ?>">
    </form>

</div>
<?php
$obj = new User_Login_History_Public_List_Table($this->plugin_name);
$obj->prepare_items();
$obj->display();
?>