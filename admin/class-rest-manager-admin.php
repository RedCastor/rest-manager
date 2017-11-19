<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/admin
 * @author     Auban le Gelle <team@redcastor.io>
 */
class Rest_Manager_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

    $this->load_dependencies();
	}


  private function load_dependencies() {

    require_once plugin_dir_path( __FILE__ ) . '/includes/class-rest-manager-admin-fields-action.php';
  }


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

    global $pagenow;

    if ( !in_array( $pagenow, array( 'options-general.php' ) ) || !isset($_GET['page']) || $_GET['page'] !== 'settings_' . $this->plugin_name ) {
      return;
    }

    //Register style rest-manager-admin
    wp_register_style($this->plugin_name . '-admin', rest_manager_get_admin_asset_path('styles/' . $this->plugin_name . '-admin.css'), array(), $this->version, 'all');
    wp_enqueue_style($this->plugin_name . '-admin');

    //Dequeue jquery chosen load by third party plugin on wn ng settings page.
    wp_dequeue_style('jquery-chosen');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

    global $pagenow;


    if ( !in_array( $pagenow, array( 'options-general.php' ) ) || !isset($_GET['page']) || $_GET['page'] !== 'settings_' . $this->plugin_name ) {
      return;
    }

    //Register settings
    Rest_Manager_Settings::admin_enqueue_scripts();

    //Register script rest-manager-admin
    wp_register_script($this->plugin_name . '-admin', rest_manager_get_admin_asset_path('scripts/' . $this->plugin_name . '-admin.js'), array(), $this->version, false);
    wp_enqueue_script($this->plugin_name . '-admin');

    //Dequeue jquery chosen load by third party plugin on wn ng settings page.
    wp_dequeue_script('jquery-chosen');

	}


  /**
   * Settings Admin Init
   *
   */
  public function settings_init() {

    $settings_page = Rest_Manager_Settings::getInstance( $this->plugin_name );

    //Action for fields !only for global.
    foreach ( $settings_page->get_fields() as $tab_key => $sections ) {
      foreach ($sections as $section_key => $field) {
        foreach ($field as $option) {
          if ( !empty($option['action'] && !empty($option['name']) && $option['global'] === true) ) {

            $option_name = $settings_page->get_option_prefix( $option['name'] );
            add_filter( 'pre_update_option_' . $option_name, $field['action'], 10, 2 );
          }
        }
      }
    }

    //Action for section
    foreach ( $settings_page->get_sections() as $tab_key => $sections ) {
      foreach ($sections as $section) {
        if ( !empty($section['action']) ) {

          $option_name = $settings_page->get_option_prefix( $section['id'] );
          add_filter( 'pre_update_option_' . $option_name, $section['action'], 10, 2 );
        }
      }
    }

    $settings_page->admin_init();
  }


  /**
   * Add Options Page Plugin settings
   */
  public function settings() {
    $page_title = __('Rest Manager', 'rest-manager');
    $menu_title = $page_title;
    $menu_slug  = 'settings_' . $this->plugin_name;

    add_options_page( $page_title, $menu_title, 'manage_options', $menu_slug, array( Rest_Manager_Settings::getInstance( $this->plugin_name ), 'render_settings_page'));

  }

}
