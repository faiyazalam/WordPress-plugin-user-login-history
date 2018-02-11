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
    <h2><?php echo Faulh_Template_Helper::plugin_name() ?> <?php _e('Network', 'faulh') ?></h2>
       <div><p><?php echo $this->list_table->table_timezone_edit()?></p></div>
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
