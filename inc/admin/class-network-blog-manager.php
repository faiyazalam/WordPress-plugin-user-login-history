<?php

/**
 * Backend Functionality
 *
 * @category Plugin
 * @package  User_Login_History
 * @author   Faiyaz Alam <contactfaiyazalam@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0+
 * @link     http://userloginhistory.com
 */

namespace User_Login_History\Inc\Admin;

use User_Login_History as NS;
use User_Login_History\Inc\Core\Activator;
use User_Login_History\Inc\Common\Helpers\Db as Db_Helper;

/**
 * Network Blog Management Functionality.
 */
class Network_Blog_Manager
{

	/**
	 * Create table whenever a new blog is created.
	 * Hooked with wp_insert_site action.
	 * 
	 * @param \Wp_Site $new_site.
	 */
	public function on_create_blog(\Wp_Site $new_site)
	{
		$blog_id = $new_site->blog_id;

		if (is_plugin_active_for_network(NS\PLUGIN_BOOTSTRAP_FILE_PATH_FROM_PLUGIN_FOLDER)) {
			switch_to_blog($blog_id);
			Activator::create_table();
			Activator::update_options();
			restore_current_blog();
		}
	}

	/**
	 * Drop table whenever a blog is deleted.
	 * Hooked with wp_delete_site action.
	 *
	 * @param \Wp_Site $old_site.
	 */
	public function deleted_blog(\Wp_Site $old_site)
	{
		$blog_id = $old_site->blog_id;
		switch_to_blog($blog_id);
		Db_Helper::drop_table(NS\PLUGIN_TABLE_FA_USER_LOGINS);
		restore_current_blog();
	}
}
