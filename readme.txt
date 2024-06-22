=== Freeze | The protection for abandoned websites ===
Contributors: giuse
Donate link: buymeacoffee.com/josem
Tags: security, updates, static, frozen
Requires at least: 4.6
Tested up to: 6.5
Stable tag: 0.0.2
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

It freezes your installation and tries to protect your abandoned website.


== Description ==

<strong>IF YOU DON'T KNOW HOW TO USE <a href="https://wordpress.org/support/article/ftp-clients/" target="_blank">FTP</a>, DON'T INSTALL THIS PLUGIN, IN ANOTHER CASE YOU MAY NOT BE ABLE TO ACCESS THE BACKEND.</strong>

This plugin freezes your installation and tries to protect your abandoned website.


You should always keep all your plugins, the theme, and the core up to date. We strongly recommend to use this plugin only as a last solution if you really have no time to properly maintain your website.
This plugin would have no sense if you keep the theme, the core, and all the plugins up to date.

We strongly recommend to do all the updates and don't use this plugin.

However, if you really have no time for the maintenance, and the installation is abandoned, at least install this plugin. It will try to protect your abandoned website.

Freeze will "close the doors" and try to protect your abandoned installation. Your installation will not check any more for updates., and no data exchange will be possible.
Nothing will work, but the normal page loading on the frontend.
If you have a contact form, it will not work any more. It will not be possible to leave comments on the blog, and so on.
Also the backend will be frozen. So, if you need some functionalities, this plugin is not for you. This plugin is for static abandoned websites (that should not exist).

It will be even not possible to log in after the log-in cookies expire.
<strong>The only way to log in again is by renaming the folder of this plugin using <a href="https://wordpress.org/support/article/ftp-clients/" target="_blank">FTP</a>, or by adding this line of code in wp-config.php before the comment /* That's all, stop editing! Happy blogging. */:</strong>

`
define( 'FREEZE_OFF',true );
`

<strong>IF YOU DON'T KNOW HOW TO USE FTP, DON'T INSTALL THIS PLUGIN.</strong>

To add a further level of security Freeze gives you also the possibility to rename the plugins folder.
Bad robots that scan the net to find old vulnerable plugins will not see the usual path of the plugins folder, and the probability they attack your installation becomes lower.

Freezing the installation and renaming the plugins folder doesn't mean that it will not be possible to exploit the vulnerabilities of old plugins.
The 100% of protection doesn't exist, but surely the probability will drop dramatically .



== REQUIREMENTS ==
You need <a href="https://wordpress.org/support/article/ftp-clients/" target="_blank">FTP</a> access, because you will not be able to log in any more after the log-in cookies expire.
The only way to log in again is by renaming the plugin using FTP, or by editing the wp-config.php file like explained in the description.
<strong>IF YOU DON'T KNOW HOW TO USE FTP DON'T INSTALL THIS PLUGIN.</strong>
*THIS PLUGIN IS ONLY FOR STATIC WEBSITES THAT DON'T NEED TO EXCHANGE DATA.


== HELP ==
If some functionalities like contact form submissions, comments... don't work, it's totally normal. This is the aim of this plugin. Only the normal loading of static pages should work, nothing else.
If there is something that works after installing this plugin, open a thread on the support  forum, because this should not happen.



== Changelog ==

= 0.0.2 =
* Added; neutralized $_FILES and $_ENV

= 0.0.1 =
* First release
