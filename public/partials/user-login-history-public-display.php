<?php
global $wpdb, $current_user;
if (!$current_user->ID) {
    _e('Error- Unable to find user id.', 'user-login-history');
    return;
}
$unknown = __('Unknown', 'user-login-history');
$options = get_option(ULH_PLUGIN_OPTION_PREFIX . 'frontend_fields');
if (!$options) {
    _e('No Fields has been selected from admin panel. Please select fields from frontend option tab of the plugin setting.', 'user-login-history');
    return;
}

$options['old_role'] = isset($options['old_role']) ? $options['old_role'] : FALSE;
$options['ip_address'] = isset($options['ip_address']) ? $options['ip_address'] : FALSE;
$options['browser'] = isset($options['browser']) ? $options['browser'] : FALSE;
$options['operating_system'] = isset($options['operating_system']) ? $options['operating_system'] : FALSE;
$options['country'] = isset($options['country']) ? $options['country'] : FALSE;
$options['last_seen'] = isset($options['last_seen']) ? $options['last_seen'] : FALSE;
$options['login'] = isset($options['login']) ? $options['login'] : FALSE;
$options['logout'] = isset($options['logout']) ? $options['logout'] : FALSE;
$options['duration'] = isset($options['duration']) ? $options['duration'] : FALSE;

$Public_List_Table_Helper = new User_Login_History_Public_List_Table_Helper();
$user_timezone = get_user_meta($current_user->ID, ULH_PLUGIN_OPTION_PREFIX . "user_timezone", TRUE);

$limit = get_option(ULH_PLUGIN_OPTION_PREFIX . 'frontend_limit');
$table = User_Login_History_DB_Helper::get_table_name();
$Paginator_Helper = new User_Login_History_Paginator_Helper();
$Date_Time_Helper = new User_Login_History_Date_Time_Helper();
$default_timezone = $Date_Time_Helper->get_default_timezone();
$current_date_time = User_Login_History_Date_Time_Helper::get_current_date_time();

$count_query = "select count(FaUserLogin.id) from " . $table . " as FaUserLogin  where user_id = $current_user->ID ";
$sql_query = "SELECT FaUserLogin.* FROM " . $table . " as FaUserLogin where  user_id = $current_user->ID ";

$prepare_sql = $Public_List_Table_Helper->prepare_where_query();

if ($prepare_sql) {
    $sql_query .= $prepare_sql;
    $count_query .= $prepare_sql;
}

if (!empty($_REQUEST['orderby'])) {
    $sql_query .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
    $sql_query .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
} else {
    $sql_query .= ' ORDER BY id DESC';
}
$options_pagination = array();
$options_pagination['count_query'] = $count_query;
$options_pagination['sql_query'] = $sql_query;
$options_pagination['limit'] = $limit;

$paginations = $Paginator_Helper->pagination($options_pagination);

$page_links = $paginations['page_links'];
$logins = $paginations['rows'];
$timezones = User_Login_History_Date_Time_Helper :: get_timezone_list();
?>
<form name="user_login_history_public_filter_form" method="get" action="">
    <table width="100%" class="form-table">
        <tbody>
            <tr>
                <td><input readonly="readonly" autocomplete="off" placeholder="From" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" class="textfield-bg"></td>
                <td><input readonly="readonly" autocomplete="off" placeholder="To" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" class="textfield-bg"></td>
                <td>
                    <select name="date_type" class="selectfield-bg">
                        <?php
                        $selected_date_type = isset($_GET['date_type']) ? $_GET['date_type'] : NULL;
                        User_Login_History_Template_Helper::dropdown_time_field_types($selected_date_type);
                        ?>
                    </select>
                </td>
            </tr>
        </tbody></table>
    <?php do_action('user_login_history_public_filter_form'); ?>
    <table width="100%">
        <tbody>
            <tr>
                <td>
                    <input type="submit" value="<?php _e('FILTER', 'user-login-history') ?>" name="ulh_public_filter_form_submit" class="go-bg">
                </td>
            </tr>
        </tbody></table>
</form>
<div>
    <p><?php _e('This table is showing time in the timezone', 'user-login-history') ?> - <?php echo $user_timezone ? $user_timezone : User_Login_History_Date_Time_Helper::DEFAULT_TIMEZONE ?> </p> 
    <p>       
         <select id="select_timezone" name="<?php echo $option_name ?>">
                    <?php $selected_timezone = isset($_GET['timezone']) ? $_GET['timezone'] : "" ?>
                    <option value=""><?php _e('Select Timezone', 'user-login-history') ?></option>
                    <option value="unknown" <?php selected($selected_timezone, "unknown"); ?> ><?php _e('Unknown', 'user-login-history') ?></option>
                    <?php
                    User_Login_History_Template_Helper::dropdown_timezone($selected_timezone);
                    ?>
                </select>
    
    </p>
</div>
<table>
    <thead style="cursor: pointer;">
        <tr>
            <th><u><?php _e('Sr. No.', 'user-login-history'); ?></u></th>
            <?php
            if ($options['old_role']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column('<span class="btf-tooltip" title="' . __('Role while user gets loggedin', 'user-login-history') . '">' . __('Old Role', 'user-login-history') . '(?)</span>', 'old_role') ?></th>
                <?php
            }
            if ($options['ip_address']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column(__('IP', 'user-login-history'), 'ip_address'); ?></th>
                <?php
            }
            if ($options['browser']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column(__('Browser', 'user-login-history'), 'browser') ?></th>
                <?php
            }
            if ($options['operating_system']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column(__('OS', 'user-login-history'), 'operating_system'); ?></th>
                <?php
            }
            if ($options['country']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column(__('Country', 'user-login-history'), 'country_name'); ?></th>
                <?php
            }
            if ($options['duration']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column(__('Duration', 'user-login-history'), ''); ?></th>
                <?php
            }
            if ($options['last_seen']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column(__('<span title="Last seen time in the session">Last Seen(?)</span>', 'user-login-history'), 'time_last_seen') ?></th>
                <?php
            }
            if ($options['login']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column(__('Login', 'user-login-history'), 'login'); ?></th>
                <?php
            }
            if ($options['logout']) {
                ?>
                <th><?php User_Login_History_Public_List_Table_Helper::show_column(__('Logout', 'user-login-history'), 'logout'); ?></th>
                <?php
            }
            do_action('user_login_history_public_table_th', $options);
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if (!empty($logins)) {
            ?>
            <?php
            foreach ($logins as $login) {
                $time_login = $Date_Time_Helper->convert_to_user_timezone($login->time_login, '', $user_timezone);
                $duration = $time_logout = __('Logged-in', 'user-login-history');
                $duration = User_Login_History_Date_Time_Helper::calculte_duration((array) $login);
                if ($login->time_logout && $login->time_logout != '0000-00-00 00:00:00') {
                    $login->time_logout = $Date_Time_Helper->convert_to_user_timezone($login->time_logout, '', $user_timezone);

                    $time_logout = $login->time_logout;
                }
                $country = !$login->country_name || 'unknown' == strtolower($login->country_name) ? __('Unknown', 'user-login-history') : $login->country_name . "(" . $login->country_code . ")";
                ?>
                <tr>
                    <td ><?php echo $no; ?></td>
                    <?php
                    if ($options['old_role']) {
                        ?>
                        <td ><?php echo $login->old_role ? esc_html($login->old_role) : $unknown; ?></td>
                        <?php
                    }
                    if ($options['ip_address']) {
                        ?>
                        <td ><?php echo $login->ip_address ? esc_html($login->ip_address) : $unknown; ?></td>
                        <?php
                    }
                    if ($options['browser']) {
                        ?>
                        <td ><?php echo $login->browser ? esc_html($login->browser) : $unknown; ?></td>
                        <?php
                    }
                    if ($options['operating_system']) {
                        ?>
                        <td ><?php echo $login->operating_system ? esc_html($login->operating_system) : $unknown; ?></td>
                        <?php
                    }
                    if ($options['country']) {
                        ?>
                        <td ><?php echo esc_html($country); ?></td>
                        <?php
                    }
                    if ($options['duration']) {
                        ?>
                        <td ><?php echo esc_html($duration); ?></td>
                        <?php
                    }
                    if ($options['last_seen']) {
                        ?>
                        <td ><?php
                            echo $login->time_last_seen ? $Date_Time_Helper->human_time_diff_from_now($login->time_last_seen, $user_timezone) : $unknown;
                            ;
                            ?></td>
                        <?php
                    }
                    if ($options['login']) {
                        ?>
                        <td ><?php echo esc_html($time_login); ?></td>
                        <?php
                    }
                    if ($options['logout']) {
                        ?>
                        <td ><?php echo esc_html($time_logout); ?></td>
                        <?php
                    }
                    do_action('user_login_history_public_table_td', $options, $login);
                    ?>
                </tr>
                <?php
                $no += 1;
            }
        } else {
            echo '<tr><td>' . __('No Records Found!', 'user-login-history') . '</tr></td>';
        }
        ?>
    </tbody>
</table>
<?php
if ($page_links) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
?>
<div class="user_login_history_public_after_listing_table"><?php do_action('user_login_history_public_after_listing_table'); ?></div>