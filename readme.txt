=== WPCore Plugin Manager ===
Plugin Name: WPCore Plugin Manager
Plugin URI: http://wpcore.com
Tags: plugins, installation, admin, administration, install, wpcore, plugin collections, plugin groups, plugin manager, bulk plugin installer, multisite, compatible
Author URI: http://wpcore.com
Author: Stuart Starr
Contributors: stueynet
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Create plugin collections and install them in one click on any WordPress site.

== Description ==
WPCore is a tool that allows you to manage collections of WordPress plugins and then quickly install them on any WordPress site. You can generate your collections at http://wpcore.com and then import them to your WordPress site by copying and pasting your unique collection key in WordPress.

== Installation ==
1. Download the plugin via http://wpcore.com/download.
1. Upload the ZIP file through the \"Plugins > Add New > Upload\" screen in your WordPress dashboard.
1. Activate the plugin through the \'Plugins\' menu in WordPress
1. Generate a plugin collection http://wpcore.com/collections/create
1. Add some plugins to that collection
1. Copy the unique collection key
1. In your WordPress admin panel visit Settings -> WPCore
1. Add collection keys
1. Install the plugins!

== Frequently Asked Questions ==

You'll find the [FAQ on WPcore.com](http://wpcore.com/help).

== Screenshots ==

1. Create plugin collections at wpcore.com
2. Install them instantly on any WordPress site.


== Changelog ==
= 1.4.1 =
* Changed classname from tom to wpcore to avoid conflicts with other themes and plugins that use the tom activation class.

= 1.4.0 =
* Further optimization. Now only 1 API call is made for all your collections. Dramatic speed increase in when saving collection keys.

= 1.3.3 =
* Increased cache timeout. Click clear cache button in settings to fetch latest collection info
* Tested with Multisite

= 1.3.2 =
* Fixed issue where in some cases users were getting  Cannot redeclare wpcore_set_false()

= 1.3.1 =
* MAJOR PERFORMANCE UPDATE!
* Completely restructured the plugin to use the WordPress transient cache
* Will only make external calls to WPCore API when you save and edit your collection keys
* Major speed improvement

= 1.2.8 =
* Reenabled the admin nag dismiss button.


= 1.2.7 =
* Updated Plugin name to alleviate some confusion between this plugin and the WordPress Core.

= 1.2.6 =
* Fixed bug that caused all the assets on the WordPress.org SVN to go missing. All good now.

= 1.2.2 =
* Updated readme again (sorry)

= 1.2.1 =
* Updated readme again (sorry)

= 1.2 =
* Updated readme

= 1.1 =
* Complete refactor. Adherence to all WP.org coding standards. More Object oriented.

= 1.0 =
* Using wp_remote_get and wp_remote_retrieve_body to grab stuff from wpcore.com

= 0.7 =
* Organized the menu. Put it near plugins cause that just makes sense.

= 0.6 =
* Fixed warning for undefined variable

= 0.5 =
* This is the first usable build
* Add and edit multiple keys
* Install tons of plugins all at once

= 0.4 =
* Hopefully fixed the changelog

= 0.3 =
* Nothing here either

= 0.2 =
* Not too much in this one

= 0.1 =
* First one

== Upgrade Notice ==

= 1.3.1 =
This is an major performance update. I have restructured the plugin to rely much less on external calls to wpcore.com. Please install asap. Report any issues!
