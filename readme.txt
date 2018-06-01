=== Rest Manager ===
Contributors: redcastor
Tags: api, rest, resp-api, wp-json, manager, rest-manager
Requires at least: 4.8
Requires PHP: 5.6.31
Tested up to: 4.9.5
Stable tag: 1.0.7
License: MIT License
License URI: http://opensource.org/licenses/MIT

Manage all rest api route. Disable or filter plugins loaded on request rest api.

== Description ==

Speed up wordpress rest api by filter loaded plugin for every route.
On each rest api request wordpress load every plugins, this is a bottleneck and the rest api slow down.
This plugin install a must use plugin for filter plugins before load to set only desired plugins load by route.
The must use plugin is uninstall on desactivate the rest-manager plugin.

Features:

* Disbale selected rest route.
* Filter plugins load by rest route.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/rest-manager` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Rest Manager screen to configure the plugin


== Frequently Asked Questions ==


== Screenshots ==

1. Settings Page.
2. Rest route filter plugin.

== Changelog ==

= 1.0.7 =
* Fix default active "on" route option if route not exist in "rest-manager_routes" wp options.
  On install new plugin with new routes the route option is not correctly set to default.

= 1.0.6 =
* Change settings column name from Select to Active

= 1.0.5 =
* Fix load settings fields for route fields
* On filter route force to load rest-manager if route is filtered and if not active.

= 1.0.4 =
* Add remove old deprecated mu-plugin.

= 1.0.3 =
* Update Readme
* Fix version

= 1.0.2 =
* Fix mu plugin name
* Fix pluggable function not exist.
* Add delete option on uninstall plugin
