This is an UNSTABLE version. To download STABLE version, visit: https://wordpress.org/plugins/user-login-history/

=== User Login History ===
Contributors: faiyazalam,w3reign,nekokun,harm10
Donate link: https://www.paypal.me/erfaiyazalam/
Tags: login,statistics,online,timesheet
Requires at least: 5.0.0
Requires PHP: 5.6.40
Tested up to: 5.6.0
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Helps you to know your website's visitors by tracking their login related information like login/logout time, country, browser and many more.

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

1. **Preferable Timezone (DEPRECATED! Will be removed in 3.0)** - You can select your preferred timezone to be used for the listing table.
1. **Shortcode** - The plugin comes with a customizable shortcode that you can use in your template or content to view the login history of current logged in user only. 
1. **Multisite Network** - On the network admin area, you can see the listing table which shows all the records fetched from all the blogs of the current network. 
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
When you’re done, post your file on this [issue](https://github.com/faiyazalam/WordPress-plugin-user-login-history/issues/6).
. 
You can also translate the plugin [online](https://translate.wordpress.org/projects/wp-plugins/user-login-history).

= How to use the plugin? =

1. To see all the tracked records in admin, click on the plugin menu shown in the left sidebar.

1. To see all the tracked records of current logged in users in frontend, use the following shortcode:

**Basic Usage of Shortcode**:

In your template file:

<pre><code>&lt;?php echo do_shortcode('[user_login_history]'); ?&gt;</code></pre>

In your content:

<pre><code>[user_login_history]</code></pre>

**Advanced Usage of Shortcode**:

In your template file:

<pre><code>&lt;?php echo do_shortcode("[user_login_history limit='20' reset_link='custom-uri' columns='ip_address,time_login' date_format='Y-m-d' time_format='H:i:s']"); ?&gt;</code></pre>

In your content:

<pre><code>[user_login_history limit='20' reset_link='custom-uri' columns='ip_address,time_login' date_format='Y-m-d' time_format='H:i:s']</code></pre>



= Shortcode Parameters =
Here is the list of all the parameters that you can use in the shortcode. All the parameters are optional.

1. title - Title of the listing table. Default is: empty string

1. limit - Number of records per page. Default is: 20

1. reset_link - Custom URI of the listing page. For the input "my-login-history", it will render a reset link with the following URL:
www.example.com/my-login-history
Default is the full permalink of the current post or page.

1. date_format - A valid date format. Default is:
<pre><code>Y-m-d</code></pre>

1. time_format - A valid time format. Default is:
<pre><code>H:i:s</code></pre>

1. show_timezone_selector - Whether you want to show timezone selector or not. Any value other than "true" will be treated as "false". Default is:
true

1. columns - List of column keys used to render columns on the listing table. Default keys are:
<pre><code>operating_system, browser, time_login, time_logout</code></pre>

1. Available Column Keys:
<pre><code>user_id, username, role, old_role, ip_address, country_name, browser, operating_system, timezone, user_agent, duration, time_last_seen, time_login, time_logout, login_status</code></pre>


= Geo Tracking =

The plugin uses [a free third party service](https://tools.keycdn.com/geo) to detect country and timezone based on IP address. Many projects are using this free service due to which sometimes the server of the service provider becomes slow. This may affect the login functionality of your website. Hence it is recommended that you do not enable this functionallity unless you have paid service or reliable service. If you have a paid service, you can [contact us](https://userloginhistory.com/contact/) to integrate it.


= Login Statuses =

1. Logged in - If user gets logged in successfully.

1. Logged out - If user clicks on logout button and gets logged out successfully.

1. Failed - If user enters invalid credentials.

1. Blocked - This is used for multisite network. By default, a user can login to any blog and then wordpress redirects to the blog on which the user is associated. The plugin saves login info at the blog on which the user logged in but cannot not save the information of the blog on which wordpress redirects the user. You can prevent this behavior by using the plugin setting.

1. Unknown - Since we have added a new column "Login Status" in the version 1.7.0, its value will be empty in the database table after upgrading to 1.7.0. To filter such records, you can use this status.

**Note** - In case, a user logs in with "Remember Me" and then closes his browser without doing logout, it will show the login status as "Logged in".


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
1. (Optional) Use the Settings->User Login History screen to configure the plugin.

== Frequently Asked Questions ==

= Can this plugin track the info of guest users? =
No.

= Is it compatible with WordPress multisite network? =
Yes.

= Where to see login list in admin? =
After activating the plugin, just re-login and then click on "User Login History" menu on the left sidebar to see the login list.

= What does the setting "online/idle minutes" actually do? =
There is a "Last Seen" column in the admin listing page.
There you will see a circle with different colors:
red is for offline users
grey is for idle users
green is for online users

You can change its settings.

The default setting is as follows:

online: 2 min
idle: 30 min

It means that:
1)the column "last seen" will show green color for the users who are active for 0-2 minutes.
2)the column "last seen" will show grey color for the users who are inactive for 2-30 minutes.
3)the column "last seen" will show red color for the users who are inactive for at-least 30 minutes.


== Screenshots ==

1. User login list table for backend
2. Screen options to customize the user login list table
3. Advanced search form to filter the records of user logins
4. Settings - Basics settings
5. Settings - Advanced settings
6. Settings - Preferred timezone setting on user profile page
7. Shortcode - User login list table for frontend

== Changelog ==

= 2.0.0 (10th June 2020) =

* Improved UI/UX of the listing table in admin panel.
* Code refactoring.
* Minor bugs fixes.

= 1.7.4(3rd April, 2020) =
* Fixed some permission issue on dashboard menu
* Implemented horizontal scroll bar on the listing table in backend
* Replaced role key with role name on the listing table

= 1.7.3(25th August, 2019) =
* Fixed index size limit issue

= 1.7.2(18th August, 2019) =
* Fixed some performance issues

= 1.7.1(16th March, 2019) =
* Fixed SQL bug in order by clause

= 1.7.0(4th June, 2018) =
* Compatible with **Multisite Network** - Now this plugin supports WordPress Multisite with multi-networks and multi-blogs.
* Added the column **Login Status** - This is used to check whether the user is logged in, logged out, failed login or blocked login.
* Added the option **Last Seen Time** in the filter forms.
* Added the column **Super Admin** - This is used only for multisite network.
* Deprecated the shortcode [user-login-history].
* Added new shortcode [user_login_history] with customizable parameters.
* Date range validation on the filter forms.
* Date and Time format of the user login listing table can be changed from the general settings.
* Improved design of the listing tables.

== Upgrade Notice ==

= 2.0.0 =

1. We have removed about us page and help page from the plugin menu. You can read the documentation in the plugin description on the WordPress plugin directory itself.

= 1.0 <= 1.7.0 =

After upgrading, you have to do the following changes:
1. Update your timezone from your profile edit page.
1. Replace the old shortcode [user-login-history] with new shortcode [user_login_history]
1. All the logged in users must be re-login otherwise last seen time and logout time will not be updated.
