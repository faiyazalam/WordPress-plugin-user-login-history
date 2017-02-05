<?php
global $wpdb, $current_user;
if (!$current_user->ID) {
    _e('Error- Unable to find user id.', 'user-login-history');
    return;
}

$options = get_option(ULH_PLUGIN_OPTION_PREFIX.'frontend_fields');
if (!$options) {
    _e('No Fields has been selected from admin panel. Please select fields from frontend option tab of the plugin setting.', 'user-login-history');
    return;
}

$Public_List_Table_Helper = new User_Login_History_Public_List_Table_Helper();
$user_timezone = get_user_meta($current_user->ID, ULH_PLUGIN_OPTION_PREFIX . "user_timezone", TRUE);


$limit = get_option(ULH_PLUGIN_OPTION_PREFIX.'frontend_limit');
$table = $wpdb->prefix . ULH_TABLE_NAME;
$Paginator_Helper = new User_Login_History_Paginator_Helper();
$Date_Time_Helper = new User_Login_History_Date_Time_Helper();
$default_timezone = $Date_Time_Helper->get_default_timezone();
$current_date_time = $Date_Time_Helper->get_current_date_time();

$count_query = "select count(id) from " . $table . " where 1 ";
$sql_query = "SELECT * FROM " . $table . " where 1 ";


$prepare_sql = $Public_List_Table_Helper->prepare_where_query();
$sql_query .= $prepare_sql['sql_query'];
$sql_query .= " AND user_id = $current_user->ID";

$count_query .= $prepare_sql['count_query'];
$count_query .= " AND user_id = $current_user->ID";


$sql_query .= " order by id DESC";
$options_pagination = array();
$options_pagination['count_query'] = $count_query;
$options_pagination['values'] = $prepare_sql['values'];
$options_pagination['sql_query'] = $sql_query;
$options_pagination['limit'] = $limit;

$paginations = $Paginator_Helper->pagination($options_pagination);

$page_links = $paginations['page_links'];
$logins = $paginations['rows'];
?>

<form name="user_login_history_public_filter_form" method="get" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <table width="100%" class="form-table">
        <tbody>
            <tr>
                <td><input readonly="readonly" autocomplete="off" placeholder="From" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? $_GET['date_from'] : "" ?>" class="textfield-bg"></td>
                <td><input readonly="readonly" autocomplete="off" placeholder="To" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? $_GET['date_to'] : "" ?>" class="textfield-bg"></td>
                <td>
                    <select name="date_type" class="selectfield-bg">
                        <?php
                        $date_types = array('login' => 'Login', 'logout' => 'Logout');
                        foreach ($date_types as $date_type_key => $date_type) {
                            ?>

                            <option value="<?php print $date_type_key ?>" <?php selected($_GET['date_type'], $date_type_key); ?>>
                                <?php echo $date_type ?>
                            </option>
                        <?php } ?>
                    </select>

                </td>
            </tr>
        </tbody></table>
    <table width="100%">
        <tbody>
            <tr>
                <td>
                    <input type="submit" value="Filter" name="ulh_public_filter_form_submit" class="go-bg">
                   
                </td>
            </tr>
        </tbody></table>
</form>
<div>
    <p>Your Current Timezone - <?php echo $user_timezone ? $user_timezone : $default_timezone ?></p>
</div>
<table>
    <thead style="cursor: pointer;">
        <tr>
            <th><u><?php _e('Sr. No', 'user-login-history'); ?></u></th>
            <?php
            if ($options['old_role']) {
                ?>
                <th><u><?php _e('<span class="btf-tooltip" title="Role while user gets loggedin">Old Role(?)</span>', 'user-login-history'); ?></u></th>

                <?php
            }

            if ($options['ip_address']) {
                ?>
                <th><u><?php _e('IP', 'user-login-history'); ?></u></th>
                <?php
            }

            if ($options['browser']) {
                ?>
                <th><u><?php _e('Browser', 'user-login-history'); ?></u></th>
                <?php
            }

            if ($options['operating_system']) {
                ?>
                <th><u><?php _e('OS', 'user-login-history'); ?></u></th>
                <?php
            }

            if ($options['country']) {
                ?>
                <th><u><?php _e('Country', 'user-login-history'); ?></u></th>
                <?php
            }
            ?>

            <?php
            if ($options['duration']) {
                ?>
                <th><u><?php _e('Duration', 'user-login-history'); ?></u></th>
                <?php
            }

            if ($options['last_seen']) {
                ?>
                <th><u><?php _e('<span title="Last seen time in the session">Last Seen(?)</span>', 'user-login-history'); ?></u></th>
                <?php
            }

            if ($options['login']) {
                ?>
                <th><u><?php _e('Login', 'user-login-history'); ?></u></th>
                <?php
            }

            if ($options['logout']) {
                ?>
                <th><u><?php _e('Logout', 'user-login-history'); ?></u></th>
                <?php
            }
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

                $duration = "Loggedin";
                $time_logout = "Loggedin";
                if ($login->time_logout != '0000-00-00 00:00:00') {

                    $login->time_logout = $Date_Time_Helper->convert_to_user_timezone($login->time_logout, '', $user_timezone);

                    $duration = date('H:i:s', strtotime($login->time_logout) - strtotime($time_login));
                    $time_logout = $login->time_logout;
                }




                //  $time_logout = $login->time_logout == '0000-00-00 00:00:00' ? 'Logged In' : $Date_Time_Helper->convert_to_user_timezone($login->time_logout, '', $user_timezone);





                $country = "Unknown" == $login->country_name ? "$login->country_name" : $login->country_name . "(" . $login->country_code . ")";
                ?>
                <tr>
                    <td ><?php echo $no; ?></td>

                    <?php
                    if ($options['old_role']) {
                        ?>
                        <td ><?php echo $login->old_role; ?></td>

                        <?php
                    }

                    if ($options['ip_address']) {
                        ?>
                        <td ><?php echo $login->ip_address; ?></td>
                        <?php
                    }

                    if ($options['browser']) {
                        ?>
                        <td ><?php echo $login->browser; ?></td>
                        <?php
                    }
                    ?>

                    <?php
                    if ($options['operating_system']) {
                        ?>
                        <td ><?php echo $login->operating_system; ?></td>
                        <?php
                    }

                    if ($options['country']) {
                        ?>
                        <td ><?php echo $country; ?></td>
                        <?php
                    }

                    if ($options['duration']) {
                        ?>
                        <td ><?php echo $duration; ?></td>
                        <?php
                    }

                    if ($options['last_seen']) {
                        ?>
                        <td ><?php
                            echo $Date_Time_Helper->human_time_diff_from_now($login->time_last_seen, $user_timezone);
                            ;
                            ?></td>
                        <?php
                    }
                    ?>

                    <?php
                    if ($options['login']) {
                        ?>
                        <td ><?php echo $time_login; ?></td>
                        <?php
                    }

                    if ($options['logout']) {
                        ?>
                        <td ><?php echo $time_logout; ?></td>
                        <?php
                    }
                    ?>
                </tr>
                <?php
                $no += 1;
            }
        } else {
            echo '<tr><td>'.__('No Records Found !!!', 'user-login-history').'</tr></td>';
        }
        ?>
    </tbody>
</table>

<script>
    jQuery(function () {
        jQuery(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});

    });

    jQuery(function () {
        jQuery("#date_to").datepicker({dateFormat: 'yy-mm-dd'});
        jQuery("#date_from").datepicker({dateFormat: 'yy-mm-dd'}).bind("change", function () {
            var minValue = jQuery(this).val();
            minValue = jQuery.datepicker.parseDate("yy-mm-dd", minValue);
            minValue.setDate(minValue.getDate() + 0);
            jQuery("#date_to").datepicker("option", "minDate", minValue);
        })
    });
</script>
<?php
if ($page_links) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}
?>