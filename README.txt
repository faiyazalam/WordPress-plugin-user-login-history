This is an UNSTABLE version. To download STABLE version, visit: https://wordpress.org/plugins/user-login-history/

=== User Login History ===
Contributors: faiyazalam,w3reign,nekokun,harm10
Donate link: https://www.paypal.me/erfaiyazalam/
Tags: login,log,online,duration,report,failed,user,history,track,admin,tool
Requires at least: 4.9.0
Requires PHP: 5.5
Tested up to: 4.9.5
Stable tag: 1.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Version: 1.7.0

The plugin helps you to track login related data like IP address, login/logout/last-seen time, country, user role, browser and many more.

== Description ==

The plugin helps you to track any visitor\'s login details with the following attributes:

1. **Login** - Login Date-Time
1. **Logout** - Logout Date-Time
1. **Last Seen** - Last Seen Date-Time
1. **Login Status** - Logged in/Logged out/Failed/Blocked
1. **Online Status** - Online/Offline/Idle
1. **Session Duration** - How long the user stayed on your website per session.
1. **User ID**
1. **Username**
1. **Current Role**
1. **Old Role** - The role while user gets logged in into your website.
1. **Browser**
1. **Operating System**
1. **IP Address**
1. **Country Name and Country Code** (Based on IP Address)
1. **Timezone** (Based on IP Address)

= Some More Useful Features =

1. **Preferable Timezone** - You can select your preferred timezone to be used for the listing table.
1. **Shortcode** - The plugin comes with a customizable shortcode that you can use in your template or content to view the login history of current logged in user. You can use the shortcodes `<?php echo do_shortcode['user-login-history'] ?>` and `[user-login-history]` in your template file and content respectively. For more detail, please see the help page under plugin menu.
1. **Multisite Network (Since version 1.7.0)** - On the network admin area, you can see the listing table which shows all the records fetched from all the blogs of the current network. 
1. **Advanced Search Filter**
1. **CSV Export**

= Compatible With =
1. [WooCommerce](https://woocommerce.com/)
1. [BuddyPress](https://buddypress.org/)
1. [UserPro](https://userproplugin.com/)
1. [Ultimate Member](https://wordpress.org/plugins/ultimate-member/)
1. [Loginizer](https://wordpress.org/plugins/loginizer/)
1. [Theme My Login](https://wordpress.org/plugins/theme-my-login/)
1. [Admin Custom Login](https://wordpress.org/plugins/admin-custom-login/)
1. [Login No Captcha reCAPTCHA](https://wordpress.org/plugins/login-recaptcha/)
1. [Force Login](https://wordpress.org/plugins/wp-force-login/)
1. [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/)

= Translations =

Currently, this plugin is available in the following two languages i.e. 

1. **English**
1. **Italian**

You can download the language files from [here](https://translate.wordpress.org/projects/wp-plugins/user-login-history "Click here to download the language file for the plugin."). 

Do you want to translate this plugin to another language?

I recommend using [POEdit](http://poedit.net/) or if you prefer to do it straight from the WordPress admin interface use [Loco Translate](https://wordpress.org/plugins/loco-translate/).
When you’re done, send us the file(s) and I’ll add it to the official plugin. You can also translate the plugin [Online](https://translate.wordpress.org/projects/wp-plugins/user-login-history).

= Bug Fixes =

If you find any bug, please create a topic with a step by step description to reproduce the bug.
Please search the forum before creating a new topic.

= Keywords =
user log, log, logger, detector, tracker, membership, 
register, sign up, admin, subscriber, editor, contributor, geo location, 
profile, front end registration, manager, report, statistics, activity, user role editor


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

= 1.7.0(release date) =
* Compatible with **Multisite Network** - Now this plugin supports WordPress Multisite with multi-networks and multi-blogs.
* Added the column **Login Status** - This is used to check whether the user is logged in, logged out, failed login or blocked login.
* Added the option **Last Seen Time** to filter the results.
* Added the column **Super Admin** - This is used only for multisite network.
* Added **Hooks** to extend its functionality easily.
* Deprecated the shortcode [user-login-history].
* Date range validation on the filter forms.
* Date and Time format of the admin listing table can be changed from the general settings.
* Improved design of the listing tables.

== Upgrade Notice ==

= 1.0 <= 1.7.0 =
After upgrading, you have to do the following changes:
1. Update your timezone from your profile edit page.
1. Replace the old shortcode [user-login-history] with new shortcode [user_login_history]
1. All the logged in users must be re-login otherwise last seen time and logout time will not be updated.