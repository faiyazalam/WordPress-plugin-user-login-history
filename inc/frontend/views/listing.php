<?php
/**
 * Template file to render listing table for public.
 *
 * @link       https://github.com/faiyazalam
 *
 * @package    User_Login_History
 * @subpackage User_Login_History/public/partials
 */
?>
<div class="<?php echo $this->plugin_name . '-wrapper'; ?>">
    <?php do_action('faulh_public_before_search_form') ?>
    <?php echo!empty($attributes['title']) ? "<div class='" . $this->plugin_name . "-listing_title'>" . $attributes['title'] . "</div>" : "" ?>
    <div><?php require(plugin_dir_path(dirname(__FILE__)) . 'views/forms/filter.php'); ?></div>
    <?php do_action('faulh_public_after_search_form') ?>
    <hr>
    <div>
        <?php
        if (!empty($attributes['show_timezone_selector']) && "true" == $attributes['show_timezone_selector']) {
            ?>
        <div><?php require(plugin_dir_path(dirname(__FILE__)) . 'views/forms/timezone.php'); ?></div>
            <?php
        }
        ?>
    </div>
    <?php do_action('faulh_public_before_listing_table') ?>
    <?php
    $this->get_List_Table()->display();
    ?>
    <?php do_action('faulh_public_after_listing_table') ?>
</div>