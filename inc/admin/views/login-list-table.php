<div class="wrap">
    <h1><?php esc_html_e('Login List', $this->plugin_text_domain) ?></h1>
    <form method="post">
        <input type="hidden" name="<?php echo $this->list_table->_args['singular'] ?>_form" value="">
        <?php
        $this->list_table->display();
        ?>
    </form>
</div>