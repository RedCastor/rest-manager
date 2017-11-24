<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Rest_Manager
 * @subpackage Rest_Manager/includes
 * @author     Auban le Gelle <team@redcastor.io>
 */
class Rest_Manager {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rest_Manager_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->version = REST_MANAGER_PLUGIN_VERSION;
		$this->plugin_name = REST_MANAGER_PLUGIN_NAME;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Rest_Manager_Loader. Orchestrates the hooks of the plugin.
	 * - Rest_Manager_i18n. Defines internationalization functionality.
	 * - Rest_Manager_Admin. Defines all hooks for the admin area.
	 * - Rest_Manager_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Modules Descriptor of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rest-manager-settings-descriptor.php';

    /**
     * Global Functions of the plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/rest-manager-core-functions.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rest-manager-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rest-manager-i18n.php';

		/**
		 * The class responsible for orchestrating the settings fields page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rest-manager-settings.php';

    /**
     * The class responsible for defining manifest
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rest-manager-manifest.php';

    /**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rest-manager-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rest-manager-public.php';

		$this->loader = new Rest_Manager_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Rest_Manager_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Rest_Manager_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

    if ( is_admin() ) {

      $plugin_admin = new Rest_Manager_Admin( $this->get_plugin_name(), $this->get_version() );

      $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
      $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

      $this->loader->add_action( 'admin_menu', $plugin_admin, 'settings', 100 );
      $this->loader->add_action( 'admin_init', $plugin_admin, 'settings_init' );

    }

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Rest_Manager_Public( $this->get_plugin_name(), $this->get_version() );

    $this->loader->add_action( $this->plugin_name . '_version_update', $plugin_public, 'update_version', 10, 2 );

    $this->loader->add_action( 'after_setup_theme',  $plugin_public, 'init_options', 5);

    if (is_admin()) {
      $this->loader->add_action( 'init',  $plugin_public, 'init_route_fields_options');
    }
    else {
      $this->loader->add_action( 'rest_api_init',  $plugin_public, 'init_route_fields_options', 1000);
    }

    $this->loader->add_filter('rest_pre_dispatch',   $plugin_public, 'rest_pre_dispatch', 1000, 3);

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();

    //Create Instance settings
    Rest_Manager_Settings::createInstance( $this->plugin_name, $this->version );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Rest_Manager_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
