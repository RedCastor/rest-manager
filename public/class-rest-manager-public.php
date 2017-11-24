<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/public
 * @author     Auban le Gelle <team@redcastor.io>
 */
class Rest_Manager_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

    $this->load_dependencies();

    //Init
    Rest_Manager_Public_Hooks::init();

	}

  private function load_dependencies()
  {

    /**
     * Include Hooks class
     */
    require_once plugin_dir_path(__FILE__) . '/includes/class-rest-manager-public-hooks.php';

  }


  /**
   * Initilaize options.
   *
   * @since   1.0.0
   */
  public function init_options() {

    if ( ! function_exists( 'is_plugin_active' ) ) {
      include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    //Setup Settings Page
    $settings_page = Rest_Manager_Settings::getInstance( $this->plugin_name );

    $fields = rest_manager_settings_fields();

    $settings_page->register_fields( $fields );

  }


  /**
   * Initilaize routes fields options.
   *
   * @since   1.0.0
   */
  public function init_route_fields_options() {

    //Setup Settings Page
    $settings_page = Rest_Manager_Settings::getInstance( $this->plugin_name );

    $route_fields = rest_manager_settings_route_fields();

    foreach ( $route_fields as $route_field) {
      $settings_page->add_field( 'rest_manager_routes', 'routes', $route_field );
    }

  }


  /**
   * Update Version
   *
   * @since   1.0.0
   */
  public function update_version( $new_version, $old_version) {

    //Install MU Plugin
    if( $new_version !== $old_version ){

      //Remove deprecated mu plugin
      $deprecated_mu_plugin_filename = trailingslashit(WPMU_PLUGIN_DIR) . REST_MANAGER_PLUGIN_NAME . '.php';
      if ( file_exists( $deprecated_mu_plugin_filename)) {
        @unlink( $deprecated_mu_plugin_filename );
      }

      rest_manager_install_mu_plugin();
    }

  }


  /**
   * On Rest Request before dispatch add hook for filter rest endpoints
   *
   * @param $result
   * @param $rest_server
   * @param $request
   * @return mixed
   */
  public function rest_pre_dispatch( $result, $rest_server, $request ) {

    if (!is_admin()) {

      add_filter('rest_endpoints', array($this, 'rest_endpoints'));
    }

    return $result;
  }

  /**
   * Filter Rest Endpoints
   * Unset route if not active.
   *
   * @param $endpoints
   * @return mixed
   */
  public function rest_endpoints( $endpoints ) {

    $active_routes = rest_manager_get_active_routes();

    foreach( $endpoints as $route => $endpoint ) {

      if( !array_key_exists($route, $active_routes) ){

        unset( $endpoints[ $route ] );
      }
    }

    return $endpoints;
  }

}
