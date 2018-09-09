<?php

namespace User_Login_History\Inc\Admin;
use User_Login_History as NS;
use User_Login_History\Inc\Core\Activator;
use User_Login_History\Inc\Common\Helpers\DbHelper;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://userloginhistory.com
 * @since      1.0.0
 *
 * @author    Er Faiyaz Alam
 */

    class NetworkBlogManager {
        
      /**
     * Create table whenever a new blog is created.
     * 
     * @access public
     */
    public function on_create_blog($blog_id, $user_id, $domain, $path, $site_id, $meta) {
        if(empty($blog_id))
        {
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
     * 
     * @access public
     */
    public function deleted_blog($blog_id) {
         if(empty($blog_id))
        {
            return;
        }
            switch_to_blog($blog_id);
            DbHelper::drop_table(NS\PLUGIN_TABLE_FA_USER_LOGINS);
            restore_current_blog();
    }


    }
