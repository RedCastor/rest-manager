<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Rest_Manager
 * @subpackage Rest_Manager/includes
 * @author     Auban le Gelle <team@redcastor.io>
 */
class Rest_Manager_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {


    rest_manager_uninstall_mu_plugin();
	}

}
