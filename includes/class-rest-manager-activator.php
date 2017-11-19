<?php

/**
 * Fired during plugin activation
 *
 * @link       https://redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Rest_Manager
 * @subpackage Rest_Manager/includes
 * @author     Auban le Gelle <team@redcastor.io>
 */
class Rest_Manager_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

    rest_manager_install_mu_plugin();

  }

}
