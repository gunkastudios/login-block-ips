=== Login Block IPs ===
Contributors: Gunka Studios
Tags: login,ip,block,whitelist,deny,access
Requires at least: 5.6
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 1.0.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Block access to all IPs to the login page, except the ones you configured.

== Description ==
Login Block IPs is a Wordpress plugin that blocks all IP addresses to the login page except the ones you have configured.

There are two types of configuration:
1. With .htaccess file: The plugin modifies the .htaccess file to blocks all IP addresses except the whitelisted.
2. By code: The .htaccess file is not modified. The plugin detects if you access to Wordpress login page and you will be blocked if your IP address is not whitelisted.

Your IP address can change at any time, so there is a secured URL to access to login page if your IP changes and is not whitelisted.

== Installation ==
1. Upload `login-block-ips.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==
1. Options page - Settings page

== Changelog ==
= Login Block IPs 1.0.0 - 2022-03-01 =
* First release