<?php
use User_Login_History\Inc\Common\Helpers\TemplateHelper;
?>
<div class="wrap">
    <h1><?php esc_html_e('Login List', $this->plugin_text_domain) ?></h1>
    <hr>
    <form method="get">
                <input type="hidden" name="page" value="<?php echo esc_attr($_GET['page']) ?>" />
        <fieldset>
           <input readonly autocomplete="off" placeholder="<?php esc_html_e("From", "faulh") ?>" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? esc_attr($_GET['date_from']) : "" ?>" >
           <input readonly autocomplete="off" placeholder="<?php esc_html_e("To", "faulh") ?>" name="date_to" id="date_to" value="<?php echo isset($_GET['date_to']) ? esc_attr($_GET['date_to']) : "" ?>" >
               <select  name="date_type" >
                    <?php TemplateHelper::dropdown_time_field_types(isset($_GET['date_type']) ? $_GET['date_type'] : NULL); ?>
            </select>
        </fieldset>
        <fieldset>
              <input id="submit" type="submit" name="submit" value="<?php esc_html_e('FILTER', 'faulh') ?>" />
        </fieldset>
            
    </form>
    <hr>
    <form method="post">
        <input type="hidden" name="<?php echo $this->list_table->get_bulk_action_form() ?>" value="">
        <?php
        $this->list_table->display();
        ?>
    </form>
</div>