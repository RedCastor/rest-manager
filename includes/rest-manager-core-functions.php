<?php

/**
 * The file that defines the global functions plugin class
 *
 * @link       team@redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/includes
 */

/**
 * Asset Path
 *
 * @param $filename
 * @return string
 */
function rest_manager_get_asset_path($filename) {
  $dist_url = plugins_url( 'public/dist/', dirname( __FILE__ ) );
  $file = basename($filename);
  static $manifest;

  if (empty($manifest)) {
    $manifest_path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/dist/assets.json';
    $manifest = new Rest_Manager_JsonManifest($manifest_path);
  }

  if (array_key_exists($file, $manifest->get())) {
    $directory = trailingslashit(dirname($filename) );

    return $dist_url . $directory . $manifest->get()[$file];
  } else {
    return $dist_url . $filename;
  }
}

/**
 * Asset Admin Path
 *
 * @param $filename
 * @return string
 */
function rest_manager_get_admin_asset_path($filename) {
  $dist_path = plugins_url( 'admin/dist/', dirname( __FILE__ ) );
  $directory = dirname($filename) . '/';
  $file = basename($filename);
  static $manifest;

  if (empty($manifest)) {
    $manifest_path = plugin_dir_path( dirname( __FILE__ ) ) . 'admin/dist/assets.json';
    $manifest = new Rest_Manager_JsonManifest($manifest_path);
  }

  if (array_key_exists($file, $manifest->get())) {
    return $dist_path . $directory . $manifest->get()[$file];
  } else {
    return $dist_path . $directory . $file;
  }
}


/**
 * Install mu plugin
 */
function rest_manager_install_mu_plugin () {

  if (wp_mkdir_p( WPMU_PLUGIN_DIR )) {

    $mu_plugin_filename = REST_MANAGER_MU_PLUGIN_NAME . '.php';

    if ( !file_exists( trailingslashit(WPMU_PLUGIN_DIR) . $mu_plugin_filename )) {

      @copy(REST_MANAGER_PLUGIN_DIR . '/lib/' . $mu_plugin_filename, trailingslashit(WPMU_PLUGIN_DIR) . $mu_plugin_filename);
    }
    else {

      require_once(ABSPATH . 'wp-admin/includes/plugin.php');

      $current_plugin = get_plugin_data( trailingslashit(WPMU_PLUGIN_DIR) . $mu_plugin_filename, false, false );
      $new_plugin = get_plugin_data( REST_MANAGER_PLUGIN_DIR . '/lib/' . $mu_plugin_filename, false, false );

      if(version_compare( $current_plugin['Version'], $new_plugin['Version'], '!=')){

        @copy(REST_MANAGER_PLUGIN_DIR . '/lib/' . $mu_plugin_filename, trailingslashit(WPMU_PLUGIN_DIR) . $mu_plugin_filename);
      }
    }
  }

}


/**
 * Uninstall mu plugin
 */
function rest_manager_uninstall_mu_plugin () {

  //Uninstall MU PLUGIN
  $mu_plugin_filename = trailingslashit(WPMU_PLUGIN_DIR) . REST_MANAGER_MU_PLUGIN_NAME . '.php';
  if ( file_exists( $mu_plugin_filename)) {
    @unlink( $mu_plugin_filename );
  }

}

/**
 * Get Plugin Option value
 */
function rest_manager_get_option( $option, $section , $default = '' ) {

  $settings_page = Rest_Manager_Settings::getInstance( REST_MANAGER_PLUGIN_NAME );

  //$field_key = null;

  //Search Field for section option
  foreach ($settings_page->get_fields() as $key => $tab ){
    if ( isset($tab[$section]) ) {
      $section_fields = $tab[$section];
      $field_key = array_search($option, array_column($section_fields, 'name'));
      break;
    }
  }

  if ( $field_key !== null ) {
    //Set the default value if exist
    $default = ( empty( $default ) && isset( $section_fields[ $field_key ]['default'] ) ) ? $section_fields[ $field_key ]['default'] : $default;

    //Is option global
    $global = boolval( isset( $section_fields[ $field_key ]['global'] ) && $section_fields[ $field_key ]['global'] === true );

    return $settings_page->get_option( $option, $section, $default, $global );
  }

  return '';
}


/**
 * Get Plugin Options section values
 */
function rest_manager_get_options( $section ) {

  $settings = Rest_Manager_Settings::getInstance( REST_MANAGER_PLUGIN_NAME );
  $section_options = array();

  //Search Field for section option
  foreach ($settings->get_fields() as $key => $tab ){
    if ( isset($tab[$section]) ) {
      foreach ($tab[$section] as $field_key => $field) {

        $default = isset($field['default']) ? $field['default'] : '';
        $value = $settings->get_option( $field['name'], $section, $default, false );
        $value = $settings->set_default_values($field, $value);

        $section_options[$field['name']] = $value;
      }
      break;
    }
  }

  return $section_options;
}


/**
 * Checks plugin rest-manager support for a given feature
 *
 * @since 1.0.0
 *
 * @global array $_rest_manager_plugin_features
 *
 * @param string $feature the feature being checked
 * @return bool
 */
function rest_manager_plugin_supports( $feature ) {
  global $_rest_manager_plugin_features;

  if ( !isset( $_rest_manager_plugin_features[$feature] ) ) {
    return false;
  }

  // If no args passed then no extra checks need be performed
  if ( func_num_args() <= 1 ) {
    return true;
  }

  $args = array_slice( func_get_args(), 1 );

  switch ( $feature ) {
    case 'rest-manager_routes':
      $type = $args[0];
      return in_array( $type, $_rest_manager_plugin_features[$feature][0] );
  }

  /**
   * Filters whether the rest-manager plugin supports a specific feature.
   *
   * @since 1.2.15
   *
   * @param bool   true     Whether the rest-manager plugin supports the given feature. Default true.
   * @param array  $args    Array of arguments for the feature.
   * @param string $feature The rest-manager plugin feature.
   */
  return apply_filters( "rest_manager_current_plugin_supports-{$feature}", true, $args, $_rest_manager_plugin_features[$feature] );
}


/*
 * Registers plugin support for a given feature.
 *
 * @global array $_rest_manager_plugin_features
 * @param mixed  $args,...  extra arguments to pass along with certain features.
 *
 * @return void|bool False on failure, void otherwise.
 */
function rest_manager_add_plugin_support( $feature ) {
  global $_rest_manager_plugin_features;

  if ( func_num_args() == 1 ) {
    $args = true;
  }
  else {
    $args = array_slice( func_get_args(), 1 );
  }

  $settings = Plugins_loader_Settings::getInstance( REST_MANAGER_PLUGIN_NAME );
  $feature = $settings->get_option_prefix( $feature );

  if ( !is_array($_rest_manager_plugin_features)  ) {
    $_rest_manager_plugin_features = array();

  }

  if ( !isset($_rest_manager_plugin_features[$feature]) ) {
    $_rest_manager_plugin_features[$feature] = $args;
  }
  else {
    //Add features if not exist and check if feature params is on
    foreach ( $args[0] as $arg_key => $arg_args) {

      if ( is_string($arg_args) && !in_array($arg_args, $_rest_manager_plugin_features[$feature][0]) ) {
        $_rest_manager_plugin_features[$feature][0][] = $arg_args;
      }
      elseif ( is_string($arg_key) && !array_key_exists($arg_key, $_rest_manager_plugin_features[$feature][0]) ) {
        $_rest_manager_plugin_features[$feature][0][$arg_key] = $arg_args;
      }
      elseif ( is_string($arg_key) && array_key_exists($arg_key, $_rest_manager_plugin_features[$feature][0]) && is_array($arg_args) ) {
        foreach ( $arg_args as $key => $value ) {
          if ( $value === 'on' ) {
            $_rest_manager_plugin_features[$feature][0][$arg_key][$key] = $value;
          }
        }
      }
    }

  }

}


/**
 * Get active routes
 *
 * @return array
 */
function rest_manager_get_active_routes() {

  $routes = array();

  $routes_option = rest_manager_get_options( 'routes' );

  if (is_array($routes_option)) {
    $settings = Rest_Manager_Settings::getInstance( REST_MANAGER_PLUGIN_NAME );

    foreach ( $routes_option as $route => $route_settings ) {

      if ( isset($route_settings['active']) && $route_settings['active'] === 'on' ) {
        $routes[$route] = $route_settings;
      }
    }
  }

  return apply_filters('rest_manager_active_routes', $routes);
}


/**
 * Get route options
 *
 * @param null $empty_value
 * @param $route
 *
 * @return bool|mixed|null
 */
function rest_manager_get_route_options( $route ) {

  $routes = rest_manager_get_active_routes();

  if ( isset($routes[$route]) ) {
    return $routes[$route];
  }

  return false;
}


/**
 * Get route options
 *
 * @param string $default
 * @param $option
 * @param $route
 * @return string
 */
function rest_manager_get_route_option( $option, $route, $default = '' ) {

  $options = rest_manager_get_route_options( $route );

  if ( is_array($options) && isset($options[$option]) && !empty($options[$option]) ) {
    return $options[$option];
  }

  return $default;
}


