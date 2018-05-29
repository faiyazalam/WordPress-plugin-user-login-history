<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://github.com/faiyazalam
 * @since      1.4.1
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials
 * @author     Er Faiyaz Alam
 */
?>
<?php
require_once plugin_dir_path(dirname(__FILE__)) . 'about/about-author.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'about/plugin-notice.php';

global $current_user;
$Date_Time_Helper = new User_Login_History_Date_Time_Helper();
$user_timezone = get_user_meta($current_user->ID, ULH_PLUGIN_OPTION_PREFIX . "user_timezone", TRUE);
if (!$user_timezone) {
    $user_timezone = $Date_Time_Helper->get_default_timezone();
}
$timezones = $Date_Time_Helper->get_timezone_list();
?>
   
<div class="wrap">
    <h1><?php _e('User Login History', 'user-login-history'); ?></h1>
    <div id="poststuff">
        <div class="search-filter">
            <form name="user-login-histoty-search-form" method="get" action="" id="user-login-histoty-search-form">
                <input type="hidden" name="page" value="user-login-history" />
                <table class="wp-list-table widefat fixed striped">
                    <tbody>
                        <tr>
                            <td><input readonly autocomplete="off" placeholder="<?php _e("From", "user-login-history") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_html($_GET['date_from']) : "" ?>" class="textfield-bg"></td>
                            <td><input readonly autocomplete="off" placeholder="<?php _e("To", "user-login-history") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_html($_GET['date_to']) : "" ?>" class="textfield-bg"></td>
                            <td>
                                <select class="selectfield-bg" name="date_type" >
                                    <?php
                                    $date_types = array('login' => __("Login", "user-login-history"), 'logout' => __("Logout", "user-login-history"));
                                    foreach ($date_types as $date_type_key => $date_type) {
                                        ?>
                                        <option value="<?php print $date_type_key ?>" <?php selected(isset($_GET['date_type']) ? $_GET['date_type'] : "", $date_type_key); ?>>
                                            <?php echo $date_type ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                    </tbody></table>
                <table class="wp-list-table widefat fixed striped">
                    <tbody>
                        <tr>
                            <td><input placeholder="<?php _e("Enter User Id", "user-login-history") ?>" name="user_id" value="<?php echo isset($_GET['user_id']) ? esc_html($_GET['user_id']) : "" ?>" class="textfield-bg"></td>
                            <td><input placeholder="<?php _e("Enter Username", "user-login-history") ?>" name="username" value="<?php echo isset($_GET['username']) ? esc_html($_GET['username']) : "" ?>" class="textfield-bg"></td>
                            <td><input placeholder="<?php _e("Enter Country", "user-login-history") ?>" name="country_name" value="<?php echo isset($_GET['country_name']) ? esc_html($_GET['country_name']) : "" ?>" class="textfield-bg"></td>
                            <td><input placeholder="<?php _e("Enter Browser", "user-login-history") ?>" name="browser" value="<?php echo isset($_GET['browser']) ? esc_html($_GET['browser']) : "" ?>" class="textfield-bg"></td>
                            <td><input placeholder="<?php _e("Enter Operating System", "user-login-history") ?>" name="operating_system" value="<?php echo isset($_GET['operating_system']) ? esc_html($_GET['operating_system']) : "" ?>" class="textfield-bg"></td>
                            <td><input placeholder="<?php _e("Enter IP Address", "user-login-history") ?>" name="ip_address" value="<?php echo isset($_GET['ip_address']) ? esc_html($_GET['ip_address']) : "" ?>" class="textfield-bg"></td>
                        </tr>
                    </tbody></table>
                <table class="wp-list-table widefat fixed striped">
                    <tbody>
                        <tr>
                            <td>
                                <select class="selectfield-bg" name="timezone">
                                    <option value=""><?php _e('Select Timezone', 'user-login-history') ?></option>
                                    <?php
                                    foreach ($timezones as $timezone) {
                                        ?>
                                        <option value="<?php print $timezone['zone'] ?>" <?php selected(isset($_GET['timezone']) ? $_GET['timezone'] : "", $timezone['zone']); ?>>
                                            <?php echo $timezone['zone'] . "(" . $timezone['diff_from_GMT'] . ")" ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td>
                                <select class="selectfield-bg" name="role">
                                    <option value=""><?php _e("Select Current Role", "user-login-history") ?></option>
                                    <?php
                                    $selected_role = isset($_GET['role']) ? $_GET['role'] : NULL;
                                    wp_dropdown_roles($selected_role);
                                    ?>
                                </select>
                            </td>
                            <td>
                                <select  class="selectfield-bg" name="old_role">
                                    <option value=""><?php _e("Select Old Role", "user-login-history") ?></option>
                                    <?php
                                    $selected_old_role = isset($_GET['old_role']) ? $_GET['old_role'] : NULL;
                                    wp_dropdown_roles($selected_old_role);
                                    ?>
                                </select></td>
                        </tr>
                    </tbody></table>
                <?php do_action('user_login_history_admin_listing_search_form'); ?>
                <table class="wp-list-table widefat fixed striped">
                    <tbody>
                        <tr>
                            <td align="left">
                                
                                <a class="button button-link-delete" href="<?php echo wp_nonce_url('admin.php?page=user-login-history&delete_all_user_login_history=1', ULH_PLUGIN_OPTION_PREFIX.'delete_all_records') ?>" id="delete_all_user_login_history"><?php _e('DELETE ALL RECORDS', 'user-login-history'); ?></a> 
                            
                                <a class="button button-primary button-large" id="download_csv_button"><?php _e('DOWNLOAD CSV', 'user-login-history'); ?></a> 
                            </td>

                            <td align="right">
                                <a class="button button-large"  href="<?php echo admin_url() . "admin.php?page=user-login-history" ?>" ><?php _e('RESET', 'user-login-history'); ?></a>
                            
                                <input type="hidden" name="export-user-login-history" id="export-user-login-history" value="">
                                <input type="hidden" name="export-user-login-history-nonce" id="export-user-login-history-nonce" value="<?php echo wp_create_nonce(ULH_PLUGIN_OPTION_PREFIX.'export_csv') ?>">
                            
                                <input class="button button-primary button-large" id="submit" type="submit" name="submit" value="<?php _e('FILTER', 'user-login-history') ?>" />
                            </td>
                          
                        </tr>
                    </tbody></table>
            </form>
        </div>
        <br class="clear">
    </div>
    <div id="post-body" class="metabox-holder ">
        <div id="post-body-content">
            <div><p><?php _e('This table is showing time in the timezone', 'user-login-history') ?> - <strong><?php echo $user_timezone ?></strong>&nbsp;<span><a class="post-edit-link" href="<?php echo admin_url() ?>options-general.php?page=user-login-history-settings&tab=backend">Edit</a></span></p> </div>
            <div class="meta-box-sortables ui-sortable">
                <form method="post" id="user_login_history_table_form">
                   <div class="listingTableOuter"> <?php
                    $this->list_table_object->prepare_items();
                    $this->list_table_object->display();
                    ?>
					</div>
                </form>
            </div>
        </div>
    </div>
      <br class="clear">
    <div><?php do_action('user_login_history_admin_listing_footer'); ?></div>
</div>
