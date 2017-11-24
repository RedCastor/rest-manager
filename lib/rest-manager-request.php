<?php

/**
 * Rest Manager Request
 *
 * Based on plugin: plugin load filter [plf-filter] v2.5.1
 * enomoto@celtislab
 * http://celtislab.net/wp_plugin_load_filter
 *
 *
 * @link              http://redcastor.io
 * @since             1.0.0
 * @package           Rest_Manager_request
 *
 * @wordpress-plugin
 * Plugin Name:       Rest Manager Request
 * Plugin URI:        http://redcastor.io
 * Description:       Manage the rest API.
 * Version:           1.0.5
 * Author:            RedCastor
 * Author URI:        http://redcastor.io
 * Copyright:         Copyright (c) 2017, RedCastor.
 * License:           MIT License
 * License URI:       http://opensource.org/licenses/MIT
 */


defined( 'ABSPATH' ) or die( 'Cheatin&#8217; uh?' );


/***************************************************************************
 * pluggable.php defined function overwrite
 * pluggable.php read before the query_posts () is processed by the current user undetermined
 **************************************************************************/
if ( !function_exists('wp_get_current_user') ) :
  /**
   * Retrieve the current user object.
   * @return WP_User Current user WP_User object
   */
  function wp_get_current_user() {
    if ( ! function_exists( 'wp_set_current_user' ) ) {
      return 0;
    }

    return _wp_get_current_user();
  }
endif;

if ( !function_exists('get_userdata') ) :
  /**
   * Retrieve user info by user ID.
   * @param int $user_id User ID
   * @return WP_User|bool WP_User object on success, false on failure.
   */
  function get_userdata( $user_id ) {
    return get_user_by( 'id', $user_id );
  }
endif;

if ( !function_exists('get_user_by') ) :
  /**
   * Retrieve user info by a given field
   * @param string $field The field to retrieve the user with. id | slug | email | login
   * @param int|string $value A value for $field. A user ID, slug, email address, or login name.
   * @return WP_User|bool WP_User object on success, false on failure.
   */
  function get_user_by( $field, $value ) {
    $userdata = WP_User::get_data_by( $field, $value );

    if ( !$userdata ) {
      return false;
    }

    $user = new WP_User;
    $user->init( $userdata );

    return $user;
  }
endif;

if ( !function_exists('is_user_logged_in') ) :
  /**
   * Checks if the current visitor is a logged in user.
   * @return bool True if user is logged in, false if not logged in.
   */
  function is_user_logged_in() {
    if ( ! function_exists( 'wp_set_current_user' ) ) {
      return false;
    }

    $user = wp_get_current_user();

    if ( ! $user->exists() ) {
      return false;
    }

    return true;
  }
endif;



/***************************************************************************
 * Active MU plugin if rest-manager plugin is active
 ***************************************************************************/
if(in_array( 'rest-manager/rest-manager.php', (array) get_option( 'active_plugins', array() ) )){
   new Rest_Manager_Request();
}


class Rest_Manager_Request {


  private $option_prefix;
  private $routes_filter;
  private $default_route_option;
  private $cache;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   */
  public function __construct() {

    $this->option_prefix = basename(__FILE__, '.php');

    $this->routes_filter = (array) get_option( 'rest-manager_routes', array() );

    $this->cache = null;

    $this->default_route_option = array(
      'active' => 'off',
      'plugins' => array(
        'value' => 'off',
        'options' => array(),
      )
    );

    if(!empty($this->routes_filter)){
      add_filter('pre_option_active_plugins', array($this, 'active_plugins'));
    }
  }


  /**
   * ake taxonomies and posts available to 'plugin load filter'.
   * force register_taxonomy (category, post_tag, post_format)
   */
  private function init_posts(){
    global $wp_actions;
    $wp_actions[ 'init' ] = 1;
    create_initial_taxonomies();
    create_initial_post_types();
    unset($wp_actions[ 'init' ]);
  }



  public function active_plugins( $default = false) {

    return $this->request_filter( 'active_plugins', $default);
  }


  //Plugin Load Filter Main (active plugins/modules filtering)
  function request_filter( $option, $default = false) {

    if ( defined( 'WP_SETUP_CONFIG' ) || defined( 'WP_INSTALLING' ) ) {
      return false;
    }


    global $wpdb;

    // prevent non-existent options from triggering multiple queries
    $notoptions = wp_cache_get( 'notoptions', 'options' );
    if ( isset( $notoptions[$option] ) ) {
      return apply_filters( 'default_option_' . $option, $default );
    }

    $alloptions = wp_load_alloptions();

    if ( isset( $alloptions[$option] ) ) {
      $options = $alloptions[$option];
    } else {
      $options = wp_cache_get( $option, 'options' );

      if ( false === $options ) {
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );

        // Has to be get_row instead of get_var because of funkiness with 0, false, null values
        if ( is_object( $row ) ) {
          $options = $row->option_value;
          wp_cache_add( $option, $options, 'options' );
        } else { // option does not exist, so we must cache its non-existence
          if ( !is_array( $notoptions ) ) {
            $notoptions = array();
          }
          $notoptions[$option] = true;
          wp_cache_set( 'notoptions', $notoptions, 'options' );

          /** This filter is documented in wp-includes/option.php */
          return apply_filters( 'default_option_' . $option, $default, $option );
        }
      }
    }

    //Admin exclude
    if( is_admin() ) {
      return false;
    }

    //get_option is called many times, intermediate processing data to cache
    $keyid = md5($this->option_prefix . '_' . $_SERVER['REQUEST_URI']);

    if(!empty($this->cache[$keyid][$option])){
      return $this->cache[$keyid][$option];
    }

    if(empty($GLOBALS['wp_the_query'])){

      $GLOBALS['wp_the_query'] = new WP_Query();
      $GLOBALS['wp_query'] = $GLOBALS['wp_the_query'];
      $GLOBALS['wp_rewrite'] = new WP_Rewrite();
      $GLOBALS['wp'] = new WP();

      $this->init_posts();

      $GLOBALS['wp']->parse_request('');
      $GLOBALS['wp']->query_posts();

      rest_api_init();
    }

    global $wp;

    $options  = maybe_unserialize( $options );
    $request = wp_parse_args($wp->matched_query);
    $new_options = $options;

    if (array_key_exists('rest_route', $request)) {

      $rest_route = urldecode($request['rest_route']);
      $routes_filter = $this->routes_filter;

      foreach ( $routes_filter as $route_filter => $route_filter_option ) {

        $match = preg_match( '@^' . $route_filter . '$@i', $rest_route );

        if ( $match  ) {

          $route_filter_option =  array_replace_recursive($this->default_route_option, $route_filter_option);

          if ( $route_filter_option['plugins']['value'] === 'on' ) {
            $new_options = (array)$route_filter_option['plugins']['options'];

            //Load always rest-manager on filter on and active is off.
            if ($route_filter_option['active'] === 'off' && !in_array('rest-manager/rest-manager.php', $new_options)) {
              $new_options[] = 'rest-manager/rest-manager.php';
            }
          }
          break;
        }
      }

    }

    $this->cache[$keyid][$option] = $new_options;

    return $new_options;
  }

}
