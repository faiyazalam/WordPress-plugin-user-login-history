=== User Login History ===
Contributors: faiyazalam,w3reign,nekokun,harm10
Donate link: https://www.paypal.me/erfaiyazalam/
Tags: login log,login activity,brute force indicator,security,history,tool
Requires at least: 5.0.0
Requires PHP: 7.4
Tested up to: 6.5.2
Stable tag: 2.1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Helps you to know your website's visitors by tracking their login related information like login/logout time, country, browser and many more.

== Description ==

The plugin helps you to track any visitor\'s login details with the following attributes:

1. **Login** - Login Date-Time
1. **Logout** - Logout Date-Time
1. **Last Seen** - Last Seen Date-Time
1. **Login Status** - Logged in/Logged out/Failed
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
1. **Mobile [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Whether the user loggedin with a mobile (e.g. tablet and mobile phone) device.
1. **Proxy IP [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Whether the user loggedin from a proxy IP.

= Features =

The [User Login History Free Version](https://www.userloginhistory.com/) plugin has all the basic features that will help you to know your website visitors. The [User Login History Pro Version](https://www.userloginhistory.com/features 'View All Features') plugin has some more premium and useful features along with all the basic features.

1. **AUTO LOGOUT [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Automatically logout idle users every 'X' minute.
You can also specify roles.
This feature is built on WordPress Cron Job.
1. **IP ADDRESS CONTROL [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Allows you to control of masking and hiding of user's IP address.
1. **EMAIL ALERT [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Allows you to get notified via email for success/failed login.
You can also specify roles and modify email templates.
1. **AUTO DELETE OLD RECORDS [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Automatically delete the records older than 'X' days.
You can also specify the roles.
This feature is built on WordPress Cron Job.
1. **TRACK SPECIFIC ROLES [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Allows you to track specific roles only.
1. **CSV SEPARATOR [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Allows you to enter a CSV separator for CSV export.
1. **REPORT - TIMESHEET [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Generate a timesheet report
1. **REPORT -  NO LOGIN LIST [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Generate a report of users who have not login for a given date range
1. **REPORT - LOGIN DEVICE [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' )** - Generate report of login count based on IP address
1. **LOGIN TIME TRACKER** - Tracks the date and time of user's login, logout, last seen, etc.
1. **LOGIN STATUS TRACKER** - Tracks user's login status to check if the user is logged in, logged out, failed, etc.
1. **ONLINE STATUS TRACKER** - Tracks user's online status to check if the user is online, idle or offline.
1. **USER INFORMATION TRACKER** - Tracks user's old role, current role, username, etc.
1. **DEVICE INFORMATION TRACKER** - Tracks user's operating system and browser.
1. **GEO LOCATION TRACKER** - Tracks user's timezone and country based on IP address.
1. **ADVANCED SEARCH FILTER** - Filters the records.
1. **CSV EXPORTER** - Exports the records in csv format.
1. **CUSTOMIZABLE SHORTCODE** - Renders the records on front-end.
1. **PREFERABLE TIMEZONE (DEPRECATED! Will be removed in 3.0)** - You can select your preferred timezone to be used for the listing table.
1. **MULTISITE NETWORK** - On the network admin area, you can see the listing table which shows all the records fetched from all the blogs of the current network. 

= Translations =

You can download the language files from [here](https://translate.WordPress.org/projects/wp-plugins/user-login-history "Click here to download the language file for the plugin."). 

Do you want to translate this plugin to another language?

I recommend using [POEdit](http://poedit.net/) or if you prefer to do it straight from the WordPress admin interface use [Loco Translate](https://WordPress.org/plugins/loco-translate/).
When you’re done, post your file on this [issue](https://github.com/faiyazalam/WordPress-plugin-user-login-history/issues/6).
. 
You can also translate the plugin [online](https://translate.WordPress.org/projects/wp-plugins/user-login-history).

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
You can use the shortcode to display the login list of the current user. It does not display the login list of other users.
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

1. roles [(Pro Feature)](https://www.userloginhistory.com/features 'View All Features' ):
It allows you to set role(s) in the shortcode parameter so that you can see the login list of other users who belong to the role(s). 
<pre><code>[user_login_history roles='administrator, editor']</code></pre>

1. columns - List of column keys used to render columns on the listing table. Default keys are:
<pre><code>operating_system, browser, time_login, time_logout</code></pre>

1. Available Column Keys:
<pre><code>user_id, username, role, old_role, ip_address, country_name, browser, operating_system, timezone, user_agent, duration, time_last_seen, time_login, time_logout, login_status</code></pre>



= Geo Tracking =

The plugin uses [a free third party service](https://tools.keycdn.com/geo) to detect country and timezone based on IP address. Many projects are using this free service due to which sometimes the server of the service provider becomes slow. This may affect the login functionality of your website. Hence it is recommended that you do not enable this functionality unless you have paid service or reliable service. If you have a paid service, you can [contact us](https://userloginhistory.com/contact/) to integrate it.


= Login Statuses =

1. Logged in - If the user gets logged in successfully.

1. Logged out - If the user clicks on logout button and gets logged out successfully.

1. Failed - If the user enters invalid credentials.

1. Blocked (DEPRECATED! Will be removed in 3.0) - This is used for the multisite network. By default, a user can login to any blog and then WordPress redirects to the blog on which the user is associated. The plugin saves login info at the blog on which the user logged in but cannot not save the information of the blog on which WordPress redirects the user. You can prevent this behavior by using the plugin setting. Please note that we already removed this status from the pro version plugin but not from the free version yet.

1. Unknown (DEPRECATED! Will be removed in 3.0) - Since we have added a new column "Login Status" in the version 1.7.0, its value will be empty in the database table after upgrading to 1.7.0. To filter such records, you can use this status.

**Note** - In case, a user log in with "Remember Me" and then closes his browser without doing logout, it will show the login status as "Logged in".


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

= 2.1.4 (5th May 2024) =

* Implemented a new php library for csv export - https://csv.thephpleague.com


= 2.1.3 (10th November 2023) =

* Fixed a few PHP 8.2 deprecation notices like Creation of dynamic property ExampleNamespace\ExampleClass::$exampleVariable is deprecated

= 2.1.2 (05th October 2023) =

* Code refactoring for the settings page
* Removed the role filter to improve the sql query performance

= 2.1.1 (21st May 2023) =

* Fixed error [issue](https://wordpress.org/support/topic/plugin-error-2-1-0/)

= 2.1.0 (17th February 2021) =

* Fixed geo location [issue](https://wordpress.org/support/topic/no-geo-location-user-agent-not-properly-defined/)

= 2.0.1 (10th February 2021) =

* Code refactoring.
* Added Buy Pro Version link.

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
