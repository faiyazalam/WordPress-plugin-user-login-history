<div class="wrap">
    <?php \User_Login_History\Inc\Common\Helpers\Template::head(esc_html__('Login List', 'faulh')); ?>
    <?php if (is_multisite() && is_network_admin()) { ?>
        <h2><?php echo esc_html__('(This page has been depreciated and will be removed in upcoming version)', 'faulh') ?></h2>
    <?php } ?>
    <hr>
    <div><?php require(plugin_dir_path(dirname(__FILE__)) . 'views/forms/filter.php'); ?></div>
    <hr>
    <div><?php echo $this->Login_List_Table->timezone_edit_link() ?></div>
    <hr>
    <div class="faulh_admin_table">
        <form method="post">
            <input type="hidden" name="<?php echo $this->Login_List_Table->get_bulk_action_form() ?>" value="">
            <div class="wrapper1">
                <div class="content1"></div>
            </div>

            <div class="wrapper2">
                <div class="content1" style="width: 1145px;">
                    <?php $this->Login_List_Table->display(); ?>
                </div>
            </div>   

        </form>
    </div>
</div>