<h1><?php esc_html_e('Login List', $this->plugin_text_domain) ?></h1>
<form method="post">
            <input type="hidden" name="<?php echo $this->plugin_name.(is_network_admin()?'_network_admin_listing_table':'_admin_listing_table')?>" value="">
            <?php
            $this->list_table->display();
            ?>
        </form>