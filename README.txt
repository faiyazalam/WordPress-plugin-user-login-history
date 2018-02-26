=== User Login History ===
Contributors: faiyazalam,w3reign
Donate link: http://wordpress.org/
Tags: login, last seen, history, active login, last login, ip, browser, country, track, admin, member, members, profile, role, roles, shortcode, user, users, time zone
Requires at least: 3.0.1
Tested up to: 4.8
Stable tag: 1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Version: 1.7

== Description ==
By this plugin you can track any visitor's login details with the following attributes:
1. **Login Date-Time**
1. **Logout Date-Time**
1. **Last Seen Date-Time**
1. **Login Status** - Success/Logout/Fail
1. **Session Duration** - How long the user stayed on your website per session.
1. **User ID**
1. **Username**
1. **IP Address**
1. **Browser**
1. **Operating System**
1. **Current Role**
1. **Old Role** - The role while user gets logged-in into your website.
1. **Country Name (Based on IP Address)**
1. **Country Code (Based on IP Address)**
1. **Timezone (Based on IP Address)**

Other Useful Features:
1. "Editable Timezone" - You can select your preferred time zone to be used for the listing table.
1. "Shortcode" - To see the listing table on front-end for the current logged-in user, you can use this shortcode `<?php echo do_shortcode['user-login-history'] ?>` in your php template file.
1. "Multisite Network (Since version 1.7)" - On the network admin area, you can see the listing table which shows all the records fetched from all the blogs of the current network. 
1. "Advanced Search Filter"
1. "CSV Export"


= Translations =

Currently, this plugin is available in the following two languages i.e. 
1. **English**
1. **Italian** (translated by [Leonardo Gandini](https://wordpress.org/support/users/nekokun/))

You can download the language files from [here](https://translate.wordpress.org/projects/wp-plugins/user-login-history "Click here to download the language file for the plugin."). 

= Bug Fixes =

If you find any bug, please create a topic with a step by step description to reproduce the bug.
Please search the forum before creating a new topic.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/user-login-history` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Use the Settings->User Login History screen to configure the plugin.

== Frequently Asked Questions ==

= Can this plugin track the info of guest users? =
No.

= Is it compatible with WordPress multisite network? =
Yes.

== Screenshots ==

1. User List with login details for backend.
2. Screen Options
3. Settings - Backend Options
4. Settings - Frontend Options
5. Shortcode - User List with login details for frontend.

== Changelog ==

= 1.7(release date) =
* Compatible with **Multisite Network** - Now this plugin supports WordPress Multisite with multi-networks and multi-blogs.
* Added the column **Login Status** - This is used to check whether the user is logged-in, logged-out, login-failed or blocked.
* Added the option **Last Seen Time** to filter the results.
* Added the column **Super Admin** - This is used only for multisite network.
* Added **Hooks** to extend its functionality easily.

== Upgrade Notice ==

= 1.* <= 1.7 =
After upgrading, you have to do the following changes:
1. Update your time zone from your profile edit page.
1. Replace the old shortcode [user-login-history] with new shortcode [user_login_history]