<?php

/**
 * Template file to render listing table for network admin.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/admin/partials
 */
?>
<div class="wrap">
    <h2><?php _e('User Login History Network', 'user-login-history') ?></h2>
        <div><p><?php require(plugin_dir_path(dirname(__FILE__)) . 'partials/timezone.php');?></p></div>
<div class="<?php echo $this->plugin_name; ?>-search-filter">
      <?php require(plugin_dir_path(dirname(__FILE__)) . 'partials/form/filter.php');?>
        <br class="clear">
    </div>
    <div>
        <form method="post">
            <input type="hidden" name="<?php echo $this->plugin_name . '_network_admin_listing_table' ?>" value="">
            <?php
            $this->list_table->prepare_items();
            $this->list_table->display();
            ?>
        </form>
        <br class="clear">
    </div>
</div>
