<?php

/**
 * Rest Manager
 *
 *
 * @link              http://redcastor.io
 * @since             1.0.0
 * @package           Rest_Manager
 *
 * @wordpress-plugin
 * Plugin Name:       Rest Manager
 * Description:       Manage the all rest API route. diasble or filter plugins loaded on request rest api.
 * Version:           1.0.7
 * Author:            RedCastor
 * Author URI:        http://redcastor.io
 * Copyright:         Copyright (c) 2017, RedCastor.
 * License:           MIT License
 * License URI:       http://opensource.org/licenses/MIT
 * Text Domain:       rest-manager
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('REST_MANAGER_PLUGIN_NAME',         'rest-manager');
define('REST_MANAGER_MU_PLUGIN_NAME',      'rest-manager-request');
define('REST_MANAGER_PLUGIN_VERSION',      '1.0.7');

//Plugin directory
define('REST_MANAGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('REST_MANAGER_PLUGIN_URL' , plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rest-manager-activator.php
 */
function activate_rest_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rest-manager-activator.php';
	Rest_Manager_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rest-manager-deactivator.php
 */
function deactivate_rest_manager() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rest-manager-deactivator.php';
	Rest_Manager_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rest_manager' );
register_deactivation_hook( __FILE__, 'deactivate_rest_manager' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rest-manager.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rest_manager() {

	$plugin = new Rest_Manager();
	$plugin->run();

}
run_rest_manager();
