<?php

namespace User_Login_History\Inc\Admin;

use User_Login_History as NS;
use User_Login_History\Inc\Core\Activator;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;

/**
 * Network Blog Management Functionality.
 *
 * @link       http://userloginhistory.com
 *
 * @author    Er Faiyaz Alam
 */
class Network_Blog_Manager {

    /**
     * Create table whenever a new blog is created.
     * Hooked with wpmu_new_blog action
     * @access public
     */
    public function on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {
        if (empty($blog_id)) {
            return;
        }

        if (is_plugin_active_for_network(NS\PLUGIN_BOOTSTRAP_FILE_PATH_FROM_PLUGIN_FOLDER)) {
            switch_to_blog($blog_id);
            Activator:: create_table();
            Activator::update_options();
            restore_current_blog();
        }
    }

    /**
     * Drop table whenever a blog is deleted.
     * Hooked with deleted_blog action
     * @access public
     */
    public function deleted_blog($blog_id) {
        if (empty($blog_id)) {
            return;
        }
        switch_to_blog($blog_id);
        Db_Helper::drop_table(NS\PLUGIN_TABLE_FA_USER_LOGINS);
        restore_current_blog();
    }

}
