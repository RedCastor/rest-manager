<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Global Functions of the plugin.
 */
require_once plugin_dir_path(__FILE__ ) . 'includes/rest-manager-core-functions.php';

rest_manager_uninstall_mu_plugin();

//Delete options in database.
global $wpdb;

// Delete options.
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '" . REST_MANAGER_PLUGIN_NAME . "\_%';");
